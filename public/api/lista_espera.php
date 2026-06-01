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

// POST /api/lista_espera.php - Adicionar à lista de espera
if ($metodo === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode([
            'success' => false,
            'error' => 'Dados inválidos'
        ]);
        exit;
    }
    
    $curso_id = $input['curso_id'] ?? 0;
    $aluno_nome = $input['aluno_nome'] ?? '';
    $aluno_email = $input['aluno_email'] ?? '';
    
    if (empty($curso_id) || empty($aluno_nome) || empty($aluno_email)) {
        echo json_encode([
            'success' => false,
            'error' => 'Curso, nome e email são obrigatórios'
        ]);
        exit;
    }
    
    try {
        // Verificar se o curso existe
        $curso_query = "SELECT id, nome, status FROM cursos WHERE id = ?";
        $curso_stmt = $conn->prepare($curso_query);
        $curso_stmt->bind_param('i', $curso_id);
        $curso_stmt->execute();
        $curso_result = $curso_stmt->get_result();
        
        if ($curso_result->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Curso não encontrado'
            ]);
            exit;
        }
        
        $curso = $curso_result->fetch_assoc();
        
        // Verificar se já está inscrito no curso
        $check_inscricao = "SELECT id FROM inscricoes WHERE curso_id = ? AND aluno_id = (SELECT id FROM usuarios WHERE email = ?)";
        $check_inscricao_stmt = $conn->prepare($check_inscricao);
        $check_inscricao_stmt->bind_param('is', $curso_id, $aluno_email);
        $check_inscricao_stmt->execute();
        
        if ($check_inscricao_stmt->get_result()->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Você já está inscrito neste curso'
            ]);
            exit;
        }
        
        // Adicionar à lista de espera (usando inscrições com status 'lista_espera')
        $lista_query = "INSERT INTO inscricoes (curso_id, aluno_id, data_inicio, observacoes, status, criado_em) VALUES (?, ?, NOW(), ?, 'lista_espera', NOW())";
        $lista_stmt = $conn->prepare($lista_query);
        $lista_stmt->bind_param('iis', $curso_id, $aluno_id, $aluno_nome);
        
        if ($lista_stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Adicionado à lista de espera com sucesso',
                'data' => [
                    'lista_id' => $conn->insert_id,
                    'curso_nome' => $curso['nome'],
                    'aluno_nome' => $aluno_nome,
                    'aluno_email' => $aluno_email
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao adicionar à lista de espera: ' . $lista_stmt->error
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Erro interno: ' . $e->getMessage()
        ]);
    }
}

// GET /api/lista_espera.php - Listar lista de espera
elseif ($metodo === 'GET') {
    try {
        $query = "
            SELECT 
                i.id,
                i.curso_id,
                c.nome as curso_nome,
                u.nome as aluno_nome,
                u.email as aluno_email,
                i.status,
                i.criado_em as data_inscricao
            FROM inscricoes i
            LEFT JOIN cursos c ON i.curso_id = c.id
            LEFT JOIN usuarios u ON i.aluno_id = u.id
            WHERE i.status = 'lista_espera'
            ORDER BY i.criado_em ASC
        ";
        
        $result = $conn->query($query);
        
        if ($result) {
            $lista_espera = [];
            while ($row = $result->fetch_assoc()) {
                $lista_espera[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $lista_espera
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

else {
    echo json_encode([
        'success' => false,
        'error' => 'Método não permitido'
    ]);
}

$conn->close();
?>
