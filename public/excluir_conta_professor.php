<?php
session_start();
header('Content-Type: application/json');

// Verificar se o usuário está logado e é professor
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'professor') {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit();
}

include 'db.php';

try {
    $professor_id = $_SESSION['user_id'];

    // Iniciar transação
    $conn->begin_transaction();

    try {
        // Excluir agendamentos do professor
        $stmt = $conn->prepare("DELETE FROM agendamentos WHERE professor_id = ?");
        $stmt->bind_param("i", $professor_id);
        $stmt->execute();

        // Excluir preferências do professor
        $stmt = $conn->prepare("DELETE FROM preferencias_professor WHERE professor_id = ?");
        $stmt->bind_param("i", $professor_id);
        $stmt->execute();

        // Excluir o professor
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'");
        $stmt->bind_param("i", $professor_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Confirmar transação
            $conn->commit();
            
            // Destruir sessão
            session_destroy();
            
            echo json_encode(['success' => true, 'message' => 'Conta excluída com sucesso']);
        } else {
            // Reverter transação
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir conta']);
        }

    } catch (Exception $e) {
        // Reverter transação em caso de erro
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir dados']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>






