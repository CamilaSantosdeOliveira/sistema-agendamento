<?php
/**
 * Teste de conexão da API
 */

// Headers para permitir CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Testa conexão com o banco
try {
    $host = 'localhost';
    $db_name = 'sistema_agendamento';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Testa uma consulta simples
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM agendamentos");
    $result = $stmt->fetch();
    
    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Conexão OK!',
        'total_agendamentos' => $result['total'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
