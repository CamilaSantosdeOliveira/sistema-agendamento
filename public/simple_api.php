<?php
// API Ultra Simples - GARANTIDO QUE FUNCIONA
require_once 'email_service.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

try {
    // Conecta com o banco
    $pdo = new PDO('mysql:host=localhost;dbname=sistema_agendamento;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    switch($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Lista agendamentos
            $sql = "SELECT * FROM agendamentos ORDER BY data DESC, hora DESC";
            $stmt = $pdo->query($sql);
            $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($agendamentos);
            break;
            
        case 'POST':
            // Cria agendamento de aula
            $dados = json_decode(file_get_contents('php://input'), true);
            
            $sql = "INSERT INTO agendamentos (nome, email, telefone, professor, data, hora, servico, observacoes, status, criado_em) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pendente', NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $dados['nome'],
                $dados['email'],
                $dados['telefone'] ?? null,
                $dados['professor'] ?? null,
                $dados['data'],
                $dados['hora'],
                $dados['servico'],
                $dados['observacoes'] ?? null
            ]);
            
            $agendamentoId = $pdo->lastInsertId();
            
            // Enviar e-mail de confirmação
            enviarNotificacaoAgendamento($agendamentoId);
            
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Solicitação de aula enviada com sucesso!',
                'id' => $agendamentoId
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
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);
}
?>
