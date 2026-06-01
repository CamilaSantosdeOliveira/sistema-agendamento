<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Processar requisições
$method = $_SERVER['REQUEST_METHOD'];

// Para requisições POST com JSON, pegar a ação do JSON
$input = '';
$json_data = [];
if ($method === 'POST' && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $input = file_get_contents('php://input');
    $json_data = json_decode($input, true);
    $action = $json_data['action'] ?? '';
} else {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
}

try {
    switch ($action) {
        case 'teste':
            echo json_encode([
                'success' => true,
                'message' => 'API funcionando!',
                'method' => $method,
                'action' => $action
            ]);
            break;
            
        case 'desativar_usuario':
            echo json_encode([
                'success' => true,
                'message' => 'Usuário desativado com sucesso! (teste)',
                'id' => $json_data['id'] ?? 'não informado'
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Ação não reconhecida: "' . $action . '"',
                'debug' => [
                    'method' => $method,
                    'action' => $action,
                    'get' => $_GET,
                    'post' => $_POST,
                    'raw_input' => $input
                ]
            ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno: ' . $e->getMessage()
    ]);
}
?>
















