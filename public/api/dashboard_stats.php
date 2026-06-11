<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir conexão com banco
require_once __DIR__ . '/../db.php';

// Verificar se a conexão foi estabelecida
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro de conexão com o banco de dados']);
    exit();
}

try {
    /**
     * Executa contagem com fallback para evitar quebrar o endpoint inteiro
     * quando alguma tabela/coluna não existir no schema atual.
     */
    $safeCount = function (string $query) use ($conn): int {
        try {
            $result = $conn->query($query);
            if (!$result) {
                return 0;
            }

            $row = $result->fetch_assoc();
            return isset($row['count']) ? (int)$row['count'] : 0;
        } catch (Throwable $e) {
            error_log('dashboard_stats query error: ' . $e->getMessage());
            return 0;
        }
    };

    // Contar cursos ativos (schema pode não possuir tabela cursos)
    $cursos_count = $safeCount("SELECT COUNT(*) as count FROM cursos WHERE status = 'ativo'");

    // Contar professores ativos
    $professores_count = $safeCount("SELECT COUNT(*) as count FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1");

    // Contar alunos cadastrados
    $alunos_count = $safeCount("SELECT COUNT(*) as count FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1");

    // Contar agendamentos futuros
    $agendamentos_count = $safeCount("SELECT COUNT(*) as count FROM agendamentos WHERE data_agendamento >= CURDATE()");

    // Retornar dados
    echo json_encode([
        'success' => true,
        'data' => [
            'cursos_count' => (int)$cursos_count,
            'professores_count' => (int)$professores_count,
            'alunos_count' => (int)$alunos_count,
            'agendamentos_count' => (int)$agendamentos_count
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao buscar estatísticas: ' . $e->getMessage()]);
}
?>






