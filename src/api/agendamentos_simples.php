<?php
/**
 * API Simplificada com Debug
 */

// Headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Log de debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Conexão direta com o banco
    $host = 'localhost';
    $db_name = 'sistema_agendamento';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        // Lista agendamentos
        $stmt = $pdo->query("SELECT * FROM agendamentos ORDER BY data ASC, hora ASC");
        $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($agendamentos);
        
    } elseif ($method === 'POST') {
        // Cria agendamento
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Dados não recebidos');
        }
        
        // Validações básicas
        if (empty($input['nome']) || empty($input['email']) || empty($input['data']) || empty($input['hora']) || empty($input['servico'])) {
            throw new Exception('Dados obrigatórios não informados');
        }
        
        // Insere no banco
        $stmt = $pdo->prepare("INSERT INTO agendamentos (nome, email, data, hora, servico, status) VALUES (?, ?, ?, ?, ?, 'agendado')");
        $stmt->execute([
            $input['nome'],
            $input['email'],
            $input['data'],
            $input['hora'],
            $input['servico']
        ]);
        
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Agendamento criado com sucesso!',
            'id' => $pdo->lastInsertId()
        ]);
        
    } elseif ($method === 'DELETE') {
        // Cancela agendamento
        $id = $_GET['id'] ?? null;
        if (!$id) {
            throw new Exception('ID não informado');
        }
        
        $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Agendamento cancelado!'
        ]);
        
    } else {
        throw new Exception('Método não permitido');
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro de banco de dados: ' . $e->getMessage(),
        'tipo' => 'PDOException'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage(),
        'tipo' => 'Exception'
    ]);
}
?>
