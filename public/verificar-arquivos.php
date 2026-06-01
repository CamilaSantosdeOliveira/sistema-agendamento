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

$base_dir = __DIR__;
$files_to_check = [
    'api/certificados.php' => 'API de Certificados',
    'db.php' => 'Conexão com Banco de Dados',
    'certificados.php' => 'Sistema de Certificados',
    'validacao_certificados.php' => 'Validação de Certificados',
    'index.html' => 'Página Principal',
    'teste-metodo-http.html' => 'Teste de Métodos HTTP',
    'SOLUCAO-HTML-ERROR.html' => 'Solução HTML Error',
    'teste-php-simples.php' => 'Teste PHP Simples'
];

$results = [
    'success' => true,
    'message' => 'Verificação de arquivos concluída',
    'timestamp' => date('Y-m-d H:i:s'),
    'base_directory' => $base_dir,
    'files' => []
];

foreach ($files_to_check as $file_path => $description) {
    $full_path = $base_dir . '/' . $file_path;
    $file_info = [
        'path' => $file_path,
        'full_path' => $full_path,
        'description' => $description,
        'exists' => file_exists($full_path),
        'size' => file_exists($full_path) ? filesize($full_path) : 0,
        'last_modified' => file_exists($full_path) ? date('Y-m-d H:i:s', filemtime($full_path)) : null,
        'permissions' => file_exists($full_path) ? [
            'readable' => is_readable($full_path),
            'writable' => is_writable($full_path),
            'executable' => is_executable($full_path)
        ] : null,
        'type' => file_exists($full_path) ? pathinfo($full_path, PATHINFO_EXTENSION) : null
    ];
    
    // Verificar se é um arquivo PHP e testar sintaxe
    if ($file_info['exists'] && $file_info['type'] === 'php') {
        $syntax_check = shell_exec("php -l " . escapeshellarg($full_path) . " 2>&1");
        $file_info['syntax_valid'] = strpos($syntax_check, 'No syntax errors') !== false;
        $file_info['syntax_error'] = $file_info['syntax_valid'] ? null : trim($syntax_check);
    } else {
        $file_info['syntax_valid'] = null;
        $file_info['syntax_error'] = null;
    }
    
    $results['files'][] = $file_info;
}

// Verificar diretórios importantes
$directories_to_check = [
    'api' => 'Diretório da API',
    'css' => 'Diretório de Estilos',
    'js' => 'Diretório de Scripts',
    'images' => 'Diretório de Imagens'
];

$results['directories'] = [];

foreach ($directories_to_check as $dir_path => $description) {
    $full_path = $base_dir . '/' . $dir_path;
    $dir_info = [
        'path' => $dir_path,
        'full_path' => $full_path,
        'description' => $description,
        'exists' => is_dir($full_path),
        'readable' => is_dir($full_path) ? is_readable($full_path) : false,
        'writable' => is_dir($full_path) ? is_writable($full_path) : false,
        'contents' => is_dir($full_path) ? scandir($full_path) : []
    ];
    
    $results['directories'][] = $dir_info;
}

// Verificar configurações do servidor
$results['server_info'] = [
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido',
    'document_root' => $_SERVER['DOCUMENT_ROOT'],
    'script_filename' => $_SERVER['SCRIPT_FILENAME'],
    'request_uri' => $_SERVER['REQUEST_URI'],
    'server_name' => $_SERVER['SERVER_NAME'],
    'server_port' => $_SERVER['SERVER_PORT']
];

// Verificar extensões PHP necessárias
$required_extensions = ['mysqli', 'json', 'curl'];
$results['extensions'] = [];

foreach ($required_extensions as $ext) {
    $results['extensions'][$ext] = [
        'loaded' => extension_loaded($ext),
        'version' => extension_loaded($ext) ? phpversion($ext) : null
    ];
}

// Contar arquivos por tipo
$file_types = [];
foreach ($results['files'] as $file) {
    if ($file['exists'] && $file['type']) {
        $type = $file['type'];
        if (!isset($file_types[$type])) {
            $file_types[$type] = 0;
        }
        $file_types[$type]++;
    }
}

$results['file_types_summary'] = $file_types;

// Resumo geral
$total_files = count($files_to_check);
$existing_files = count(array_filter($results['files'], function($f) { return $f['exists']; }));
$php_files = count(array_filter($results['files'], function($f) { return $f['type'] === 'php'; }));
$valid_php_files = count(array_filter($results['files'], function($f) { return $f['syntax_valid'] === true; }));

$results['summary'] = [
    'total_files_checked' => $total_files,
    'existing_files' => $existing_files,
    'missing_files' => $total_files - $existing_files,
    'php_files' => $php_files,
    'valid_php_files' => $valid_php_files,
    'invalid_php_files' => $php_files - $valid_php_files,
    'success_rate' => round(($existing_files / $total_files) * 100, 2) . '%'
];

echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>

















