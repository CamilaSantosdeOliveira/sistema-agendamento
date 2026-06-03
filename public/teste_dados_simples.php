<?php
include 'db.php';

echo "=== VERIFICAÇÃO DOS DADOS DO DASHBOARD ===\n\n";

// Verificar cursos
$cursos_query = "SELECT COUNT(*) as count FROM cursos WHERE status = 'ativo'";
$cursos_result = $conn->query($cursos_query);
$cursos_count = $cursos_result ? $cursos_result->fetch_assoc()['count'] : 0;
echo "Cursos ativos: " . $cursos_count . "\n";

// Verificar professores
$professores_query = "SELECT COUNT(*) as count FROM professores WHERE status = 'ativo'";
$professores_result = $conn->query($professores_query);
$professores_count = $professores_result ? $professores_result->fetch_assoc()['count'] : 0;
echo "Professores ativos: " . $professores_count . "\n";

// Verificar alunos
$alunos_query = "SELECT COUNT(*) as count FROM alunos WHERE status = 'ativo'";
$alunos_result = $conn->query($alunos_query);
$alunos_count = $alunos_result ? $alunos_result->fetch_assoc()['count'] : 0;
echo "Alunos ativos: " . $alunos_count . "\n";

// Verificar agendamentos
$agendamentos_query = "SELECT COUNT(*) as count FROM agendamentos WHERE status != 'cancelado'";
$agendamentos_result = $conn->query($agendamentos_query);
$agendamentos_count = $agendamentos_result ? $agendamentos_result->fetch_assoc()['count'] : 0;
echo "Agendamentos (não cancelados): " . $agendamentos_count . "\n";

echo "\n=== DETALHES DOS AGENDAMENTOS ===\n";
$agendamentos_detalhes = "SELECT * FROM agendamentos WHERE status != 'cancelado' ORDER BY data DESC LIMIT 3";
$result = $conn->query($agendamentos_detalhes);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " | Data: " . $row['data'] . " | Hora: " . $row['hora'] . " | Professor: " . $row['professor'] . " | Aluno: " . $row['nome'] . " | Status: " . $row['status'] . "\n";
    }
} else {
    echo "Nenhum agendamento encontrado.\n";
}
?>




