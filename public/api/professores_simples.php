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

// GET - Listar todos os professores ou buscar por ID
if ($metodo === 'GET') {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        // Buscar professor específico
        try {
            $query = "
                SELECT 
                    u.id,
                    u.nome,
                    u.email,
                    u.ativo,
                    u.criado_em,
                    0 as agendamentos_count
                FROM usuarios u
                WHERE u.tipo_usuario = 'professor' AND u.id = ?
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
    } else {
        // Listar todos os professores
        try {
            $query = "
                SELECT 
                    u.id,
                    u.nome,
                    u.email,
                    u.ativo,
                    u.criado_em,
                    0 as agendamentos_count
                FROM usuarios u
                WHERE u.tipo_usuario = 'professor'
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
}

// POST - Criar novo professor
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
            INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo)
            VALUES (?, ?, ?, 'professor', 1)
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sss', $nome, $email, $senha);
        
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

// PUT - Atualizar professor
elseif ($metodo === 'PUT') {
    $id = $_GET['id'] ?? null;
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$id || !$input) {
        echo json_encode([
            'success' => false,
            'error' => 'ID e dados são obrigatórios'
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
        
        // Removendo campos que não existem na tabela
        
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

else {
    echo json_encode([
        'success' => false,
        'error' => 'Método não permitido'
    ]);
}

$conn->close();
?>
