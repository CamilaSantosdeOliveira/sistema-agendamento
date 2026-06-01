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

// POST /api/inscricoes.php - Criar nova inscrição
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
    $telefone = $input['telefone'] ?? '';
    
    if (empty($curso_id) || empty($aluno_nome) || empty($aluno_email)) {
        echo json_encode([
            'success' => false,
            'error' => 'Curso, nome e email são obrigatórios'
        ]);
        exit;
    }
    
    try {
        // Verificar se o curso existe e está ativo
        $curso_query = "SELECT id, nome, status FROM cursos WHERE id = ? AND status = 'ativo'";
        $curso_stmt = $conn->prepare($curso_query);
        $curso_stmt->bind_param('i', $curso_id);
        $curso_stmt->execute();
        $curso_result = $curso_stmt->get_result();
        
        if ($curso_result->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Curso não encontrado ou não está disponível'
            ]);
            exit;
        }
        
        $curso = $curso_result->fetch_assoc();
        
        // Verificar se o aluno já está inscrito neste curso
        $check_query = "SELECT id FROM inscricoes WHERE curso_id = ? AND aluno_id = (SELECT id FROM usuarios WHERE email = ?)";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('is', $curso_id, $aluno_email);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Você já está inscrito neste curso'
            ]);
            exit;
        }
        
        // Criar ou buscar aluno na tabela usuarios
        $aluno_query = "SELECT id FROM usuarios WHERE email = ? AND tipo_usuario = 'aluno'";
        $aluno_stmt = $conn->prepare($aluno_query);
        $aluno_stmt->bind_param('s', $aluno_email);
        $aluno_stmt->execute();
        $aluno_result = $aluno_stmt->get_result();
        
        $aluno_id = null;
        if ($aluno_result->num_rows > 0) {
            $aluno = $aluno_result->fetch_assoc();
            $aluno_id = $aluno['id'];
        } else {
            // Criar novo aluno
            $senha = 'senha123'; // Senha padrão
            $insert_aluno = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, telefone, ativo, criado_em) VALUES (?, ?, ?, 'aluno', ?, 1, NOW())";
            $insert_stmt = $conn->prepare($insert_aluno);
            $insert_stmt->bind_param('ssss', $aluno_nome, $aluno_email, $senha, $telefone);
            
            if ($insert_stmt->execute()) {
                $aluno_id = $conn->insert_id;
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Erro ao criar aluno: ' . $insert_stmt->error
                ]);
                exit;
            }
        }
        
        // Inserir inscrição
        $inscricao_query = "INSERT INTO inscricoes (curso_id, aluno_id, data_inicio, observacoes, status, criado_em) VALUES (?, ?, NOW(), ?, 'ativa', NOW())";
        $inscricao_stmt = $conn->prepare($inscricao_query);
        $inscricao_stmt->bind_param('iis', $curso_id, $aluno_id, $telefone);
        
        if ($inscricao_stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Inscrição realizada com sucesso',
                'data' => [
                    'inscricao_id' => $conn->insert_id,
                    'curso_nome' => $curso['nome'],
                    'aluno_nome' => $aluno_nome,
                    'aluno_email' => $aluno_email
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao realizar inscrição: ' . $inscricao_stmt->error
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Erro interno: ' . $e->getMessage()
        ]);
    }
}

// GET /api/inscricoes.php - Listar inscrições
elseif ($metodo === 'GET') {
    try {
        $query = "
            SELECT 
                i.id,
                i.curso_id,
                c.nome as curso_nome,
                i.aluno_id,
                u.nome as aluno_nome,
                u.email as aluno_email,
                u.telefone,
                i.status,
                i.criado_em as data_inscricao
            FROM inscricoes i
            LEFT JOIN cursos c ON i.curso_id = c.id
            LEFT JOIN usuarios u ON i.aluno_id = u.id
            ORDER BY i.criado_em DESC
        ";
        
        $result = $conn->query($query);
        
        if ($result) {
            $inscricoes = [];
            while ($row = $result->fetch_assoc()) {
                $inscricoes[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $inscricoes
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

// DELETE /api/inscricoes.php?id=X - Remover inscrição
elseif ($metodo === 'DELETE') {
    $id = $_GET['id'] ?? 0;
    
    if (empty($id) || !is_numeric($id)) {
        echo json_encode([
            'success' => false,
            'error' => 'ID da inscrição é obrigatório'
        ]);
        exit;
    }
    
    try {
        // Verificar se a inscrição existe
        $check_query = "SELECT id FROM inscricoes WHERE id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('i', $id);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Inscrição não encontrada'
            ]);
            exit;
        }
        
        // Remover a inscrição
        $delete_query = "DELETE FROM inscricoes WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param('i', $id);
        
        if ($delete_stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Inscrição removida com sucesso'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao remover inscrição: ' . $delete_stmt->error
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

