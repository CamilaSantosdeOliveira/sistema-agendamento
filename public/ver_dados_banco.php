<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    include 'db.php';
    
    $dados = [];
    
    // Buscar usuários
    $sql_usuarios = "SELECT id, nome, email, tipo_usuario, criado_em FROM usuarios ORDER BY nome";
    $result_usuarios = $conn->query($sql_usuarios);
    $usuarios = [];
    while ($row = $result_usuarios->fetch_assoc()) {
        $usuarios[] = $row;
    }
    
    // Buscar cursos
    $sql_cursos = "SELECT id, nome, descricao, carga_horaria, criado_em FROM cursos ORDER BY nome";
    $result_cursos = $conn->query($sql_cursos);
    $cursos = [];
    while ($row = $result_cursos->fetch_assoc()) {
        $cursos[] = $row;
    }
    
    // Buscar certificados
    $sql_certificados = "
        SELECT 
            c.id,
            c.codigo_verificacao,
            c.data_emissao,
            c.status,
            c.data_conclusao_curso,
            u.nome as aluno_nome,
            u.email as aluno_email,
            cur.nome as curso_nome
        FROM certificados c
        JOIN usuarios u ON c.aluno_id = u.id
        JOIN cursos cur ON c.curso_id = cur.id
        ORDER BY c.data_emissao DESC
    ";
    $result_certificados = $conn->query($sql_certificados);
    $certificados = [];
    while ($row = $result_certificados->fetch_assoc()) {
        $certificados[] = $row;
    }
    
    // Contar registros
    $total_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
    $total_cursos = $conn->query("SELECT COUNT(*) as total FROM cursos")->fetch_assoc()['total'];
    $total_certificados = $conn->query("SELECT COUNT(*) as total FROM certificados")->fetch_assoc()['total'];
    
    $dados = [
        'resumo' => [
            'usuarios' => $total_usuarios,
            'cursos' => $total_cursos,
            'certificados' => $total_certificados
        ],
        'usuarios' => $usuarios,
        'cursos' => $cursos,
        'certificados' => $certificados
    ];
    
    echo json_encode([
        'success' => true,
        'message' => 'Dados do banco carregados com sucesso',
        'data' => $dados
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>



















