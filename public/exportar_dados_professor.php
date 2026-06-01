<?php
session_start();

// Verificar se o usuário está logado e é professor
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header('HTTP/1.1 403 Forbidden');
    exit('Acesso negado');
}

include 'db.php';

try {
    $professor_id = $_SESSION['user_id'];
    $data_exportacao = date('Y-m-d_H-i-s');
    
    // Buscar dados do professor
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'");
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $professor = $stmt->get_result()->fetch_assoc();

    if (!$professor) {
        header('HTTP/1.1 404 Not Found');
        exit('Professor não encontrado');
    }

    // Buscar agendamentos do professor
    $stmt = $conn->prepare("SELECT a.*, c.nome as curso_nome, u.nome as aluno_nome 
                           FROM agendamentos a 
                           JOIN cursos c ON a.curso_id = c.id 
                           JOIN usuarios u ON a.aluno_id = u.id 
                           WHERE a.professor_id = ? 
                           ORDER BY a.data_agendamento DESC");
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $agendamentos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Buscar preferências do professor
    $stmt = $conn->prepare("SELECT * FROM preferencias_professor WHERE professor_id = ?");
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $preferencias = $stmt->get_result()->fetch_assoc();

    $json_exportacao = null;

    // Montar dados para exportação
    $dados_exportacao = [
        'professor' => [
            'id' => $professor['id'],
            'nome' => $professor['nome'],
            'email' => $professor['email'],
            'formacao' => $professor['formacao'] ?? null,
            'valor_hora' => $professor['valor_hora'] ?? null,
            'telefone' => $professor['telefone'] ?? null,
            'data_cadastro' => $professor['data_cadastro'] ?? null
        ],
        'agendamentos' => $agendamentos,
        'preferencias' => $preferencias,
        'estatisticas' => [
            'total_agendamentos' => count($agendamentos),
            'agendamentos_concluidos' => count(array_filter($agendamentos, function($a) { return $a['status'] === 'concluido'; })),
            'agendamentos_pendentes' => count(array_filter($agendamentos, function($a) { return $a['status'] === 'agendado'; })),
            'data_exportacao' => date('Y-m-d H:i:s')
        ]
    ];

    $json_exportacao = json_encode($dados_exportacao, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Configurar headers para download
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="dados_professor_' . $professor['nome'] . '_' . $data_exportacao . '.json"');
    header('Content-Length: ' . strlen($json_exportacao));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    // Exportar dados em JSON formatado
    echo $json_exportacao;

} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    exit('Erro interno do servidor');
}
?>






