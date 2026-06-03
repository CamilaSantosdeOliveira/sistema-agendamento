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

// Informações do sistema
$info = [
    'success' => true,
    'message' => 'PHP está funcionando corretamente!',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido',
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'request_uri' => $_SERVER['REQUEST_URI'],
    'server_name' => $_SERVER['SERVER_NAME'],
    'server_port' => $_SERVER['SERVER_PORT'],
    'document_root' => $_SERVER['DOCUMENT_ROOT'],
    'script_filename' => $_SERVER['SCRIPT_FILENAME'],
    'extensions_loaded' => [
        'mysqli' => extension_loaded('mysqli'),
        'json' => extension_loaded('json'),
        'curl' => extension_loaded('curl')
    ]
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
                'server_info' => $conn->server_info,
                'client_info' => $conn->client_info
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

// Verificar permissões de arquivo
$info['file_permissions'] = [
    'current_file' => [
        'readable' => is_readable(__FILE__),
        'writable' => is_writable(__FILE__),
        'executable' => is_executable(__FILE__)
    ],
    'api_file' => [
        'readable' => file_exists($api_file) ? is_readable($api_file) : false,
        'writable' => file_exists($api_file) ? is_writable($api_file) : false,
        'executable' => file_exists($api_file) ? is_executable($api_file) : false
    ]
];

// Verificar logs de erro
$error_log = ini_get('error_log');
$info['error_logging'] = [
    'error_log_path' => $error_log,
    'display_errors' => ini_get('display_errors'),
    'log_errors' => ini_get('log_errors'),
    'error_reporting' => ini_get('error_reporting')
];

echo json_encode($info, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>



















