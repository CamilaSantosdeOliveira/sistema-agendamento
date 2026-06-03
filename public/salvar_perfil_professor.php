<?php
session_start();
header('Content-Type: application/json');

// Verificar se o usuário está logado e é professor
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'professor') {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit();
}

include 'db.php';

try {
    // Receber dados JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit();
    }

    $professor_id = $_SESSION['user_id'];
    $nome = trim($input['nome']);
    $email = trim($input['email']);
    $formacao = trim($input['formacao']);
    $valor_hora = floatval(str_replace(['R$', ' ', ','], ['', '', '.'], $input['valor_hora']));
    $telefone = trim($input['telefone']);

    // Validações
    if (empty($nome)) {
        echo json_encode(['success' => false, 'message' => 'Nome é obrigatório']);
        exit();
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email inválido']);
        exit();
    }

    if ($valor_hora < 0) {
        echo json_encode(['success' => false, 'message' => 'Valor por hora deve ser positivo']);
        exit();
    }

    // Verificar se o email já existe (exceto para o próprio usuário)
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ? AND tipo_usuario = 'professor'");
    $stmt->bind_param("si", $email, $professor_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Este email já está em uso']);
        exit();
    }

    // Atualizar dados do professor
    $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, formacao = ?, valor_hora = ?, telefone = ? WHERE id = ? AND tipo_usuario = 'professor'");
    $stmt->bind_param("sssdsi", $nome, $email, $formacao, $valor_hora, $telefone, $professor_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar perfil']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>








