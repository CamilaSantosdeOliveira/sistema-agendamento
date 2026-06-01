<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

// Verifica se o usuário está logado. Se não, use um ID fixo para testes.
if (!isset($_SESSION['user_id'])) {
    // REMOVA ISSO EM PRODUÇÃO.
    // Estamos usando o ID 1 (Professor João) para testar o sistema.
    $_SESSION['user_id'] = 1; 
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_conversations':
        getConversations($pdo, $user_id);
        break;
    case 'get_messages':
        getMessages($pdo, $user_id, $_GET['receiver_id'] ?? null);
        break;
    case 'send_message':
        sendMessage($pdo, $user_id, json_decode(file_get_contents('php://input'), true));
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
        break;
}

function getConversations($pdo, $user_id) {
    // Busca todos os outros usuários para simular a lista de contatos
    try {
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE id != ?");
        $stmt->execute([$user_id]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro interno.']);
    }
}

function getMessages($pdo, $user_id, $receiver_id) {
    if (!$receiver_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID do destinatário ausente.']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT sender_id, message FROM chat_messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC");
        $stmt->execute([$user_id, $receiver_id, $receiver_id, $user_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($messages);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro interno.']);
    }
}

function sendMessage($pdo, $user_id, $data) {
    $receiver_id = $data['receiver_id'] ?? null;
    $message = $data['message'] ?? '';

    if (!$receiver_id || empty($message)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID do destinatário ou mensagem ausente.']);
        return;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $receiver_id, $message]);
        echo json_encode(['success' => true, 'message' => 'Mensagem enviada.']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro interno.']);
    }
}
?>