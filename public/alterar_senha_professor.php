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
    $senha_atual = $input['senha_atual'];
    $nova_senha = $input['nova_senha'];

    // Validações
    if (empty($senha_atual) || empty($nova_senha)) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
        exit();
    }

    if (strlen($nova_senha) < 6) {
        echo json_encode(['success' => false, 'message' => 'A nova senha deve ter pelo menos 6 caracteres']);
        exit();
    }

    // Verificar senha atual
    $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'");
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
        exit();
    }

    $usuario = $result->fetch_assoc();
    
    if (!password_verify($senha_atual, $usuario['senha'])) {
        echo json_encode(['success' => false, 'message' => 'Senha atual incorreta']);
        exit();
    }

    // Hash da nova senha
    $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

    // Atualizar senha
    $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ? AND tipo_usuario = 'professor'");
    $stmt->bind_param("si", $nova_senha_hash, $professor_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Senha alterada com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao alterar senha']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>






