<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    include 'db.php';
    
    $sql = "
        SELECT 
            u.id,
            u.nome,
            u.email,
            u.criado_em,
            COUNT(DISTINCT c.id) as certificados_count
        FROM usuarios u
        LEFT JOIN certificados c ON u.id = c.aluno_id
        WHERE u.tipo_usuario = 'aluno' AND u.ativo = 1
        GROUP BY u.id
        ORDER BY u.nome
    ";
    
    $result = $conn->query($sql);
    $alunos = [];
    
    while ($row = $result->fetch_assoc()) {
        $alunos[] = [
            'id' => $row['id'],
            'nome' => $row['nome'],
            'email' => $row['email'],
            'certificados_count' => $row['certificados_count']
        ];
    }
    
    echo json_encode([
        'success' => true, 
        'data' => $alunos
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao buscar alunos: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>


















