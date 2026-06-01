<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../db.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', $uri);

// GET /api/professores.php - Listar todos os professores
if ($metodo === 'GET' && count($path_parts) <= 3) {
    try {
        $query = "
            SELECT 
                u.id,
                u.nome,
                u.email,
                u.formacao,
                u.valor_hora,
                u.ativo,
                u.criado_em,
                COUNT(a.id) as agendamentos_count
            FROM usuarios u
            LEFT JOIN agendamentos a ON u.id = a.professor_id
            WHERE u.tipo_usuario = 'professor'
            GROUP BY u.id
            ORDER BY u.nome
        ";
        
        $result = $conn->query($query);
        
        if ($result) {
            $professores = [];
            while ($row = $result->fetch_assoc()) {
                $professores[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $professores
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erro na consulta SQL: ' . $conn->error
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Erro interno: ' . $e->getMessage()
        ]);
    }
}

// GET /api/professores.php/{id} - Buscar professor específico
elseif ($metodo === 'GET' && count($path_parts) > 3) {
    $id = $path_parts[3];
    
    try {
        $query = "
            SELECT 
                u.id,
                u.nome,
                u.email,
                u.formacao,
                u.valor_hora,
                u.ativo,
                u.criado_em,
                COUNT(a.id) as agendamentos_count
            FROM usuarios u
            LEFT JOIN agendamentos a ON u.id = a.professor_id
            WHERE u.tipo_usuario = 'professor' AND u.id = ?
            GROUP BY u.id
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $professor = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'data' => $professor
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Professor não encontrado'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Erro interno: ' . $e->getMessage()
        ]);
    }
}

// POST /api/professores.php - Criar novo professor
elseif ($metodo === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode([
            'success' => false,
            'error' => 'Dados inválidos'
        ]);
        exit;
    }
    
    $nome = $input['nome'] ?? '';
    $email = $input['email'] ?? '';
    $senha = $input['senha'] ?? 'senha123';
    $formacao = $input['formacao'] ?? '';
    $valor_hora = $input['valor_hora'] ?? 0;
    
    if (empty($nome) || empty($email)) {
        echo json_encode([
            'success' => false,
            'error' => 'Nome e email são obrigatórios'
        ]);
        exit;
    }
    
    try {
        // Verificar se email já existe
        $check_query = "SELECT id FROM usuarios WHERE email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('s', $email);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Email já cadastrado'
            ]);
            exit;
        }
        
        // Inserir novo professor
        $query = "
            INSERT INTO usuarios (nome, email, senha, tipo_usuario, formacao, valor_hora, ativo, criado_em)
            VALUES (?, ?, ?, 'professor', ?, ?, 1, NOW())
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssd', $nome, $email, $senha, $formacao, $valor_hora);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Professor criado com sucesso',
                'id' => $conn->insert_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao criar professor: ' . $stmt->error
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Erro interno: ' . $e->getMessage()
        ]);
    }
}

// PUT /api/professores.php/{id} - Atualizar professor
elseif ($metodo === 'PUT' && count($path_parts) > 3) {
    $id = $path_parts[3];
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode([
            'success' => false,
            'error' => 'Dados inválidos'
        ]);
        exit;
    }
    
    try {
        $updates = [];
        $types = '';
        $values = [];
        
        // Construir query dinamicamente
        if (isset($input['nome'])) {
            $updates[] = 'nome = ?';
            $types .= 's';
            $values[] = $input['nome'];
        }
        
        if (isset($input['email'])) {
            $updates[] = 'email = ?';
            $types .= 's';
            $values[] = $input['email'];
        }
        
        if (isset($input['formacao'])) {
            $updates[] = 'formacao = ?';
            $types .= 's';
            $values[] = $input['formacao'];
        }
        
        if (isset($input['valor_hora'])) {
            $updates[] = 'valor_hora = ?';
            $types .= 'd';
            $values[] = $input['valor_hora'];
        }
        
        if (isset($input['ativo'])) {
            $updates[] = 'ativo = ?';
            $types .= 'i';
            $values[] = $input['ativo'] ? 1 : 0;
        }
        
        if (empty($updates)) {
            echo json_encode([
                'success' => false,
                'error' => 'Nenhum campo para atualizar'
            ]);
            exit;
        }
        
        $query = "UPDATE usuarios SET " . implode(', ', $updates) . " WHERE id = ? AND tipo_usuario = 'professor'";
        $types .= 'i';
        $values[] = $id;
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$values);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Professor atualizado com sucesso'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Professor não encontrado ou nenhuma alteração feita'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao atualizar professor: ' . $stmt->error
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Erro interno: ' . $e->getMessage()
        ]);
    }
}

// DELETE /api/professores.php/{id} - Deletar professor
elseif ($metodo === 'DELETE' && count($path_parts) > 3) {
    $id = $path_parts[3];
    
    try {
        // Verificar se professor tem agendamentos
        $check_query = "SELECT COUNT(*) as count FROM agendamentos WHERE professor_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('i', $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        
        if ($count > 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Não é possível deletar professor com agendamentos ativos'
            ]);
            exit;
        }
        
        $query = "DELETE FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Professor deletado com sucesso'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Professor não encontrado'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao deletar professor: ' . $stmt->error
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Erro interno: ' . $e->getMessage()
        ]);
    }
}

else {
    echo json_encode([
        'success' => false,
        'error' => 'Método não permitido'
    ]);
}

$conn->close();
?>
