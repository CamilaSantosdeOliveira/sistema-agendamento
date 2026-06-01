<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

// GET - Listar todos os cursos
if ($metodo === 'GET') {
    try {
        $query = "
            SELECT 
                id,
                nome,
                categoria,
                nivel,
                duracao_horas,
                preco,
                status
            FROM cursos 
            WHERE status = 'ativo'
            ORDER BY nome
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

// POST - Criar novo curso
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
        
        $nome = $input['nome'] ?? '';
        $categoria = $input['categoria'] ?? '';
        $nivel = $input['nivel'] ?? '';
        $duracao_horas = $input['duracao_horas'] ?? 0;
        $preco = $input['preco'] ?? 0;
        $descricao = $input['descricao'] ?? '';
        
        if (empty($nome) || empty($categoria)) {
            echo json_encode([
                'success' => false,
                'error' => 'Nome e categoria são obrigatórios'
            ]);
            exit;
        }
        
        $sql = "INSERT INTO cursos (nome, categoria, nivel, duracao_horas, preco, descricao, status) VALUES (?, ?, ?, ?, ?, ?, 'ativo')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssids', $nome, $categoria, $nivel, $duracao_horas, $preco, $descricao);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Curso criado com sucesso',
                'id' => $conn->insert_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao criar curso: ' . $stmt->error
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

$conn->close();
?>
