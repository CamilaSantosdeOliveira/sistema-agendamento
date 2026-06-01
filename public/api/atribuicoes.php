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

// Criar tabela de atribuições se não existir
function criarTabelaAtribuicoes($conn) {
    $sql = "
        CREATE TABLE IF NOT EXISTS atribuicoes_cursos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            professor_id INT NOT NULL,
            curso_id INT NOT NULL,
            data_atribuicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('ativo', 'inativo') DEFAULT 'ativo',
            FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
            UNIQUE KEY unique_atribuicao (professor_id, curso_id)
        )
    ";
    
    return $conn->query($sql);
}

// GET - Listar atribuições
if ($metodo === 'GET') {
    try {
        // Criar tabela se não existir
        criarTabelaAtribuicoes($conn);
        
        $query = "
            SELECT 
                ac.id,
                ac.professor_id,
                ac.curso_id,
                ac.data_atribuicao,
                ac.status,
                u.nome as professor_nome,
                c.nome as curso_nome,
                c.categoria as curso_categoria
            FROM atribuicoes_cursos ac
            JOIN usuarios u ON ac.professor_id = u.id
            JOIN cursos c ON ac.curso_id = c.id
            ORDER BY u.nome, c.nome
        ";
        
        $result = $conn->query($query);
        
        if ($result) {
            $atribuicoes = [];
            while ($row = $result->fetch_assoc()) {
                $atribuicoes[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $atribuicoes
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

// POST - Criar nova atribuição
elseif ($metodo === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode([
                'success' => false,
                'error' => 'Dados inválidos'
            ]);
            exit;
        }
        
        $professor_id = $input['professor_id'] ?? null;
        $curso_id = $input['curso_id'] ?? null;
        
        if (!$professor_id || !$curso_id) {
            echo json_encode([
                'success' => false,
                'error' => 'Professor ID e Curso ID são obrigatórios'
            ]);
            exit;
        }
        
        // Criar tabela se não existir
        criarTabelaAtribuicoes($conn);
        
        // Verificar se professor existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'");
        $stmt->bind_param('i', $professor_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Professor não encontrado'
            ]);
            exit;
        }
        
        // Verificar se curso existe
        $stmt = $conn->prepare("SELECT id FROM cursos WHERE id = ?");
        $stmt->bind_param('i', $curso_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Curso não encontrado'
            ]);
            exit;
        }
        
        // Inserir atribuição
        $stmt = $conn->prepare("INSERT INTO atribuicoes_cursos (professor_id, curso_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $professor_id, $curso_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Atribuição criada com sucesso',
                'id' => $conn->insert_id
            ]);
        } else {
            if ($conn->errno === 1062) { // Duplicate entry
                echo json_encode([
                    'success' => false,
                    'error' => 'Esta atribuição já existe'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Erro ao criar atribuição: ' . $stmt->error
                ]);
            }
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Erro interno: ' . $e->getMessage()
        ]);
    }
}

// DELETE - Remover atribuição
elseif ($metodo === 'DELETE') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'ID da atribuição é obrigatório'
            ]);
            exit;
        }
        
        $atribuicao_id = $input['id'];
        
        // Criar tabela se não existir
        criarTabelaAtribuicoes($conn);
        
        // Remover atribuição
        $stmt = $conn->prepare("DELETE FROM atribuicoes_cursos WHERE id = ?");
        $stmt->bind_param('i', $atribuicao_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Atribuição removida com sucesso'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Atribuição não encontrada'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao remover atribuição: ' . $stmt->error
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
        'error' => 'Método não suportado'
    ]);
}
?>







