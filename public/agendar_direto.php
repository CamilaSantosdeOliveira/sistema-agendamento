<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir conexão
include 'db.php';

try {
    // Debug: ver o que está chegando
    $raw_input = file_get_contents('php://input');
    
    // Pegar dados do POST (FormData)
    $input = $_POST;
    
    // Debug: mostrar dados recebidos
    error_log("Dados recebidos: " . print_r($input, true));
    
    if (!$input) {
        throw new Exception('Dados inválidos - nenhum dado recebido');
    }
    
    // Extrair dados com fallbacks
    $nome = $input['nome'] ?? $input['aluno_nome'] ?? '';
    $email = $input['email'] ?? $input['aluno_email'] ?? '';
    $telefone = $input['telefone'] ?? $input['aluno_telefone'] ?? '';
    $professor = $input['professor'] ?? $input['professor_nome'] ?? '';
    $data = $input['data'] ?? $input['data_aula'] ?? '';
    $hora = $input['hora'] ?? $input['hora_inicio'] ?? '';
    $servico = $input['servico'] ?? $input['curso'] ?? $input['curso_nome'] ?? '';
    $observacoes = $input['observacoes'] ?? $input['comentarios'] ?? '';
    
    // Debug: mostrar dados extraídos
    error_log("Dados extraídos: nome=$nome, data=$data, hora=$hora, servico=$servico");
    
    // Validar dados obrigatórios
    if (empty($nome)) {
        throw new Exception('Nome é obrigatório');
    }
    if (empty($data)) {
        throw new Exception('Data é obrigatória');
    }
    if (empty($hora)) {
        throw new Exception('Hora é obrigatória');
    }
    if (empty($servico)) {
        throw new Exception('Serviço/Curso é obrigatório');
    }
    
    // Inserir no banco
    $sql = "INSERT INTO agendamentos (nome, email, telefone, professor, data, hora, servico, observacoes, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pendente')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssss', $nome, $email, $telefone, $professor, $data, $hora, $servico, $observacoes);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Agendamento realizado com sucesso!',
            'id' => $conn->insert_id
        ]);
    } else {
        throw new Exception('Erro ao salvar agendamento: ' . $stmt->error);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'raw_input' => $raw_input ?? 'não disponível',
            'input' => $input ?? 'não disponível'
        ]
    ]);
}
?>
