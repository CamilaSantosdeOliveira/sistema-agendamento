<?php

header('Content-Type: application/json');

// Simulação de conexão com o banco de dados
// Em um ambiente de produção, você usaria as credenciais reais do seu DB.
$dbHost = 'localhost';
$dbUser = 'root'; // Altere para seu usuário de banco de dados
$dbPass = '';     // Altere para sua senha de banco de dados
$dbName = 'educonnectdb';

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
    exit();
}

// Recebe e decodifica o corpo da requisição JSON
$data = json_decode(file_get_contents('php://input'), true);

// Validação de dados do lado do servidor
if (empty($data['teacherName']) || empty($data['subject']) || empty($data['student']) || empty($data['date']) || empty($data['time']) || empty($data['duration'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios.']);
    exit();
}

// Coleta e sanitiza os dados para evitar XSS (Cross-Site Scripting)
$teacherName = htmlspecialchars($data['teacherName'], ENT_QUOTES, 'UTF-8');
$subject = htmlspecialchars($data['subject'], ENT_QUOTES, 'UTF-8');
$student = htmlspecialchars($data['student'], ENT_QUOTES, 'UTF-8');
$date = htmlspecialchars($data['date'], ENT_QUOTES, 'UTF-8');
$time = htmlspecialchars($data['time'], ENT_QUOTES, 'UTF-8');
$duration = intval($data['duration']); // Garante que a duração seja um número

// Lógica de agendamento no banco de dados com consultas preparadas (proteção contra SQL Injection)
try {
    $sql = "INSERT INTO classes (teacherName, subject, student, date, time, duration) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // Vincula os parâmetros e executa a consulta
    $stmt->execute([$teacherName, $subject, $student, $date, $time, $duration]);

    // Resposta de sucesso em JSON
    echo json_encode(['success' => true, 'message' => 'Aula agendada com sucesso para ' . $teacherName . '!']);

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Erro interno ao agendar a aula: ' . $e->getMessage()]);
}

?>

