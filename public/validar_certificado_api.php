<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    include 'db.php';
    
    $codigo = $_GET['codigo'] ?? '';
    
    if (empty($codigo)) {
        echo json_encode(['success' => false, 'message' => 'Código de validação é obrigatório']);
        exit;
    }
    
    // Buscar certificado pelo código
    $sql = "
        SELECT 
            c.id,
            c.codigo_verificacao,
            c.data_emissao,
            c.status,
            c.data_conclusao,
            c.carga_horaria,
            u.nome as aluno_nome,
            u.email as aluno_email,
            cur.nome as curso_nome,
            cur.descricao as curso_descricao
        FROM certificados c
        INNER JOIN usuarios u ON c.aluno_id = u.id
        INNER JOIN cursos cur ON c.curso_id = cur.id
        WHERE c.codigo_verificacao = ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $certificado = $result->fetch_assoc();
        
        $dados = [
            'id' => $certificado['id'],
            'codigo_verificacao' => $certificado['codigo_verificacao'],
            'data_emissao' => date('d/m/Y', strtotime($certificado['data_emissao'])),
            'status' => $certificado['status'],
            'aluno_nome' => $certificado['aluno_nome'],
            'aluno_email' => $certificado['aluno_email'],
            'curso_nome' => $certificado['curso_nome'],
            'carga_horaria' => $certificado['carga_horaria'],
            'data_conclusao' => date('d/m/Y', strtotime($certificado['data_conclusao']))
        ];
        
        echo json_encode(['success' => true, 'certificado' => $dados]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Certificado não encontrado']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao validar certificado: ' . $e->getMessage()]);
}
?>


















