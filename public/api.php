<?php
// Configuração básica
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Para CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Configuração do banco
$host = 'localhost';
$dbname = 'sistema_agendamento';
$user = 'root';
$pass = '';

try {
    // Conecta com o banco
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch($method) {
        case 'GET':
            // Lista todos os agendamentos
            $sql = "SELECT * FROM agendamentos ORDER BY data, hora";
            $stmt = $pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
            break;
            
        case 'POST':
            // Cria novo agendamento
            $input = json_decode(file_get_contents('php://input'), true);
            
            $sql = "INSERT INTO agendamentos (nome, email, data, hora, servico, status) VALUES (?, ?, ?, ?, ?, 'agendado')";
            $stmt = $pdo->prepare($sql);
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
            break;
            
        case 'DELETE':
            // Cancela agendamento
            $id = $_GET['id'];
            $sql = "DELETE FROM agendamentos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Agendamento cancelado!'
            ]);
            break;
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => true,
        'mensagem' => $e->getMessage()
    ]);
}
?>


