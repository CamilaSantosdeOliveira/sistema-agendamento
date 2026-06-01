<?php
// Suprimir exibição de erros para garantir JSON válido
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

include '../db.php';

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        $action = $_GET['action'] ?? '';
        
        if ($action === 'buscar_usuario') {
            $id = $_GET['id'] ?? 0;
            
            $query = "SELECT * FROM usuarios WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $usuario = $result->fetch_assoc();
                echo json_encode([
                    'success' => true,
                    'data' => $usuario
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ]);
            }
            
        } elseif ($_GET['tipo'] === 'aluno') {
            // Listar alunos ativos
            $query = "SELECT id, nome, email FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1 ORDER BY nome";
            $result = $conn->query($query);
            
            $alunos = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $alunos[] = $row;
                }
            }
            
            echo json_encode([
                'success' => true,
                'data' => $alunos
            ]);
            
        } elseif ($_GET['tipo'] === 'professor') {
            // Listar professores ativos
            $query = "SELECT id, nome, email FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1 ORDER BY nome";
            $result = $conn->query($query);
            
            $professores = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $professores[] = $row;
                }
            }
            
            echo json_encode([
                'success' => true,
                'data' => $professores
            ]);
            
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Ação não especificada'
            ]);
        }
        
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? '';
        
        if ($action === 'criar_usuario') {
            $nome = trim($data['nome'] ?? '');
            $email = trim($data['email'] ?? '');
            $senha = $data['senha'] ?? '';
            $tipo_usuario = $data['tipo_usuario'] ?? 'aluno';
            $telefone = trim($data['telefone'] ?? '');
            $formacao = trim($data['formacao'] ?? '');
            $valor_hora = $data['valor_hora'] ?? '';

            if ($nome === '' || $email === '' || $senha === '') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Nome, e-mail e senha são obrigatórios'
                ]);
                exit;
            }

            if (!in_array($tipo_usuario, ['aluno', 'professor', 'admin'], true)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Tipo de usuário inválido'
                ]);
                exit;
            }

            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            $query = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, telefone, formacao, valor_hora, ativo) VALUES (?, ?, ?, ?, NULLIF(?, ''), NULLIF(?, ''), NULLIF(?, ''), 1)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sssssss', $nome, $email, $senha_hash, $tipo_usuario, $telefone, $formacao, $valor_hora);

            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuário criado com sucesso!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro ao criar usuário: ' . $stmt->error
                ]);
            }

        } elseif ($action === 'editar_usuario') {
            $id = $data['id'] ?? 0;
            $nome = $data['nome'] ?? '';
            $email = $data['email'] ?? '';
            $formacao = $data['formacao'] ?? '';
            $valor_hora = $data['valor_hora'] ?? '';
            $telefone = $data['telefone'] ?? '';
            $data_nascimento = $data['data_nascimento'] ?? '';
            $endereco = $data['endereco'] ?? '';
            $cidade = $data['cidade'] ?? '';
            $estado = $data['estado'] ?? '';
            $cep = $data['cep'] ?? '';
            
            $query = "UPDATE usuarios SET nome = ?, email = ?, formacao = ?, valor_hora = NULLIF(?, ''), telefone = ?, data_nascimento = NULLIF(?, ''), endereco = ?, cidade = ?, estado = ?, cep = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssssssssssi', $nome, $email, $formacao, $valor_hora, $telefone, $data_nascimento, $endereco, $cidade, $estado, $cep, $id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuário atualizado com sucesso!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro ao atualizar usuário: ' . $stmt->error
                ]);
            }
            
        } elseif ($action === 'desativar_usuario') {
            $id = $data['id'] ?? 0;
            
            $query = "UPDATE usuarios SET ativo = 0 WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuário desativado com sucesso!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro ao desativar usuário: ' . $stmt->error
                ]);
            }
            
        } elseif ($action === 'excluir_usuario') {
            $id = $data['id'] ?? 0;
            
            $query = "DELETE FROM usuarios WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuário excluído com sucesso!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro ao excluir usuário: ' . $stmt->error
                ]);
            }
            
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Ação não reconhecida'
            ]);
        }
        
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Método não permitido'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erro na consulta: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
