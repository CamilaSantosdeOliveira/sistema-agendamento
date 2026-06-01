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
$action = $_GET['action'] ?? '';

// Verificar se a conexão foi estabelecida
if (!$conn) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro de conexão com banco de dados'
    ]);
    exit;
}

try {
    // Se for POST, criar novo curso
    if ($metodo === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validar dados obrigatórios
        if (empty($input['nome'])) {
            throw new Exception('Nome do curso é obrigatório');
        }
        if (empty($input['categoria'])) {
            throw new Exception('Categoria é obrigatória');
        }
        if (empty($input['nivel'])) {
            throw new Exception('Nível é obrigatório');
        }
        
        // Preparar dados
        $nome = $input['nome'] ?? '';
        $descricao = $input['descricao'] ?? '';
        $categoria = $input['categoria'] ?? '';
        $nivel = $input['nivel'] ?? '';
        $duracao_horas = intval($input['duracao_horas'] ?? 0);
        $preco = floatval($input['preco'] ?? 0);
        $vagas = intval($input['vagas'] ?? 30);
        $status = 'ativo';
        
        // Inserir no banco
        $sql = "INSERT INTO cursos (nome, descricao, categoria, nivel, duracao_horas, preco, status, alunos_inscritos, avaliacao) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0.00)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssids', $nome, $descricao, $categoria, $nivel, $duracao_horas, $preco, $status);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Curso criado com sucesso!',
                'id' => $conn->insert_id
            ]);
        } else {
            throw new Exception('Erro ao criar curso: ' . $stmt->error);
        }
        
        $stmt->close();
        exit;
    }
    
    switch ($action) {
        case 'listar':
            $query = "
                SELECT 
                    c.id,
                    c.nome,
                    c.descricao,
                    c.categoria,
                    c.nivel,
                    c.duracao_horas,
                    c.preco,
                    c.status,
                    c.alunos_inscritos,
                    c.avaliacao
                FROM cursos c
                WHERE c.status = 'ativo'
                ORDER BY c.nome
            ";
            
            $result = $conn->query($query);
            
            if ($result) {
                $cursos = [];
                while ($row = $result->fetch_assoc()) {
                    $cursos[] = $row;
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $cursos
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro na consulta SQL: ' . $conn->error
                ]);
            }
            break;
            
        default:
            // Comportamento padrão - listar todos os cursos
            $query = "
                SELECT 
                    c.id,
                    c.nome,
                    c.descricao,
                    c.categoria,
                    c.nivel,
                    c.duracao_horas,
                    c.preco,
                    c.status,
                    c.alunos_inscritos,
                    c.avaliacao
                FROM cursos c
                ORDER BY c.nome
            ";
            
            $result = $conn->query($query);
            
            if ($result) {
                $cursos = [];
                while ($row = $result->fetch_assoc()) {
                    $cursos[] = $row;
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $cursos
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro na consulta SQL: ' . $conn->error
                ]);
            }
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno: ' . $e->getMessage()
    ]);
}

$conn->close();
?>






