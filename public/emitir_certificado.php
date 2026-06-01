<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    include 'db.php';
    
    $aluno_id = intval($_GET['aluno_id'] ?? 0);
    
    if (!$aluno_id) {
        echo json_encode(['success' => false, 'message' => 'ID do aluno é obrigatório'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Verificar se o aluno existe
    $sql = "SELECT id, nome, email FROM usuarios WHERE id = ? AND tipo_usuario = 'aluno'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $aluno_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Aluno não encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $aluno = $result->fetch_assoc();
    
    // Buscar primeiro curso disponível
    $sql_curso = "SELECT id, nome, carga_horaria FROM cursos LIMIT 1";
    $result_curso = $conn->query($sql_curso);
    $curso = $result_curso->fetch_assoc();
    
    if (!$curso) {
        echo json_encode(['success' => false, 'message' => 'Nenhum curso disponível'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Gerar código único
    $codigo = 'CERT-' . strtoupper(substr(md5($aluno_id . time()), 0, 8)) . '-' . date('Y');
    
    // Inserir certificado no banco
    $sql_insert = "
        INSERT INTO certificados (aluno_id, curso_id, codigo_verificacao, data_emissao, data_conclusao, status)
        VALUES (?, ?, ?, CURDATE(), CURDATE(), 'emitido')
    ";
    
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iis", $aluno_id, $curso['id'], $codigo);
    $stmt_insert->execute();
    
    $certificado_id = $conn->insert_id;
    
    $novo_certificado = [
        'id' => $certificado_id,
        'codigo_verificacao' => $codigo,
        'data_emissao' => date('Y-m-d'),
        'status' => 'emitido',
        'aluno_nome' => $aluno['nome'],
        'aluno_email' => $aluno['email'],
        'curso_nome' => $curso['nome'],
        'carga_horaria' => $curso['carga_horaria'],
        'data_conclusao' => date('d/m/Y')
    ];
    
    echo json_encode(['success' => true, 'data' => $novo_certificado], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao emitir certificado: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
















