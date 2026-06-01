<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

include '../db.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$path_parts = explode('/', trim($path, '/'));

// Extrair ID se presente na URL
$id = null;
if (count($path_parts) > 2 && is_numeric($path_parts[2])) {
    $id = (int)$path_parts[2];
}

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                // Buscar curso específico
                $stmt = $conn->prepare("SELECT * FROM cursos WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    echo json_encode([
                        'success' => true,
                        'data' => $result->fetch_assoc()
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Curso não encontrado'
                    ]);
                }
            } else {
                // Listar todos os cursos
                $result = $conn->query("SELECT * FROM cursos ORDER BY nome");
                $cursos = [];
                
                while ($row = $result->fetch_assoc()) {
                    $cursos[] = $row;
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $cursos
                ]);
            }
            break;
            
        case 'POST':
            // Criar novo curso
            $input = json_decode(file_get_contents('php://input'), true);
            
            $nome = $input['nome'] ?? '';
            $descricao = $input['descricao'] ?? '';
            $categoria = $input['categoria'] ?? '';
            $nivel = $input['nivel'] ?? '';
            $duracao_horas = $input['duracao_horas'] ?? 0;
            $preco = $input['preco'] ?? 0;
            $status = $input['status'] ?? 'ativo';
            
            if (empty($nome)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Nome do curso é obrigatório'
                ]);
                break;
            }
            
            $stmt = $conn->prepare("
                INSERT INTO cursos (nome, descricao, categoria, nivel, duracao_horas, preco, status, criado_em) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param("ssssids", $nome, $descricao, $categoria, $nivel, $duracao_horas, $preco, $status);
            
            if ($stmt->execute()) {
                $curso_id = $conn->insert_id;
                echo json_encode([
                    'success' => true,
                    'message' => 'Curso criado com sucesso',
                    'id' => $curso_id
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Erro ao criar curso: ' . $stmt->error
                ]);
            }
            break;
            
        case 'PUT':
            // Atualizar curso
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'ID do curso é obrigatório'
                ]);
                break;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $nome = $input['nome'] ?? '';
            $descricao = $input['descricao'] ?? '';
            $categoria = $input['categoria'] ?? '';
            $nivel = $input['nivel'] ?? '';
            $duracao_horas = $input['duracao_horas'] ?? 0;
            $preco = $input['preco'] ?? 0;
            $status = $input['status'] ?? 'ativo';
            
            $stmt = $conn->prepare("
                UPDATE cursos 
                SET nome = ?, descricao = ?, categoria = ?, nivel = ?, duracao_horas = ?, preco = ?, status = ?, atualizado_em = NOW()
                WHERE id = ?
            ");
            
            $stmt->bind_param("ssssidsi", $nome, $descricao, $categoria, $nivel, $duracao_horas, $preco, $status, $id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Curso atualizado com sucesso'
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Curso não encontrado'
                    ]);
                }
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Erro ao atualizar curso: ' . $stmt->error
                ]);
            }
            break;
            
        case 'DELETE':
            // Deletar curso
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'ID do curso é obrigatório'
                ]);
                break;
            }
            
            $stmt = $conn->prepare("DELETE FROM cursos WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Curso deletado com sucesso'
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Curso não encontrado'
                    ]);
                }
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Erro ao deletar curso: ' . $stmt->error
                ]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'error' => 'Método não permitido'
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro interno do servidor: ' . $e->getMessage()
    ]);
}

$conn->close();
?>















