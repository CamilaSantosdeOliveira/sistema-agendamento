<?php
session_start();

// Receber dados do POST
$input = json_decode(file_get_contents('php://input'), true);

if ($input && isset($input['username'])) {
    // Criar sessão
    $_SESSION['user_logged_in'] = true;
    $_SESSION['username'] = $input['username'];
    $_SESSION['user_data'] = $input['userData'];
    
    // Responder com sucesso
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    // Erro
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
}
?>















