<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include 'db.php';

try {
    $dados = [];
    
    // Buscar professores
    $result = $conn->query("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1 ORDER BY nome");
    $professores = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $professores[] = $row;
        }
    }
    $dados['professores'] = $professores;
    
    // Buscar cursos
    $result = $conn->query("SELECT id, nome FROM cursos WHERE status = 'ativo' ORDER BY nome");
    $cursos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cursos[] = $row;
        }
    }
    $dados['cursos'] = $cursos;
    
    // Buscar alunos
    $result = $conn->query("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1 ORDER BY nome");
    $alunos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $alunos[] = $row;
        }
    }
    $dados['alunos'] = $alunos;
    
    echo json_encode([
        'success' => true,
        'data' => $dados
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao carregar dados: ' . $e->getMessage()
    ]);
}
?>
