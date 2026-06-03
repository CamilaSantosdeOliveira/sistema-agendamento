<?php
session_start();
include 'db.php';

// Verificar se o usuário está logado e é aluno
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'aluno') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Usuário não autorizado']);
    exit();
}

// Verificar se foi passado um ID de agendamento
if (!isset($_POST['agendamento_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID do agendamento não fornecido']);
    exit();
}

$aluno_id = $_SESSION['user_id'];
$agendamento_id = $_POST['agendamento_id'];

try {
    // Verificar se o agendamento pertence ao aluno
    $stmt = $conn->prepare("SELECT a.*, c.nome as curso_nome FROM agendamentos a 
                           JOIN cursos c ON a.curso_id = c.id 
                           WHERE a.id = ? AND a.aluno_id = ?");
    $stmt->bind_param("ii", $agendamento_id, $aluno_id);
    $stmt->execute();
    $agendamento = $stmt->get_result()->fetch_assoc();
    
    if (!$agendamento) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Aula não encontrada ou não pertence a você']);
        exit();
    }
    
    // Verificar se a aula já foi realizada (data passada)
    $data_aula = new DateTime($agendamento['data_agendamento']);
    $data_atual = new DateTime();
    
    if ($data_aula < $data_atual) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Não é possível excluir aulas já realizadas']);
        exit();
    }
    
    // Excluir o agendamento
    $stmt = $conn->prepare("DELETE FROM agendamentos WHERE id = ? AND aluno_id = ?");
    $stmt->bind_param("ii", $agendamento_id, $aluno_id);
    
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Aula excluída com sucesso!',
            'curso' => $agendamento['curso_nome'],
            'data' => $agendamento['data_agendamento']
        ]);
    } else {
        throw new Exception("Erro ao excluir aula: " . $stmt->error);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir aula: ' . $e->getMessage()]);
}
?>








