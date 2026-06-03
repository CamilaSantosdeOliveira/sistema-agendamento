<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar se é uma requisição OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Informações básicas da API
$info = [
    'success' => true,
    'message' => 'API funcionando corretamente!',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido',
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'request_uri' => $_SERVER['REQUEST_URI'],
    'server_name' => $_SERVER['SERVER_NAME'],
    'server_port' => $_SERVER['SERVER_PORT']
];

// Verificar se o arquivo api/certificados.php existe
$api_file = __DIR__ . '/api/certificados.php';
$info['api_file_exists'] = file_exists($api_file);
$info['api_file_path'] = $api_file;

// Verificar se o arquivo db.php existe
$db_file = __DIR__ . '/db.php';
$info['db_file_exists'] = file_exists($db_file);
$info['db_file_path'] = $db_file;

// Testar conexão com banco de dados se possível
if (file_exists($db_file)) {
    try {
        include $db_file;
        if (isset($conn) && $conn instanceof mysqli) {
            $info['database_connection'] = 'success';
            $info['database_info'] = [
                'host' => $conn->host_info,
                'server_info' => $conn->server_info
            ];
        } else {
            $info['database_connection'] = 'failed';
            $info['database_error'] = 'Conexão não estabelecida';
        }
    } catch (Exception $e) {
        $info['database_connection'] = 'error';
        $info['database_error'] = $e->getMessage();
    }
} else {
    $info['database_connection'] = 'not_tested';
    $info['database_error'] = 'Arquivo db.php não encontrado';
}

echo json_encode($info, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>



















