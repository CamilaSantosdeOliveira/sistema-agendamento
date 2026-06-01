<?php
include 'db.php';

echo "=== VERIFICAÇÃO COMPLETA DOS DADOS ===\n\n";

// Verificar TODOS os cursos (qualquer status)
echo "=== CURSOS ===\n";
$cursos_query = "SELECT COUNT(*) as count FROM cursos";
$cursos_result = $conn->query($cursos_query);
$cursos_total = $cursos_result ? $cursos_result->fetch_assoc()['count'] : 0;
echo "Total de cursos: " . $cursos_total . "\n";

$cursos_query = "SELECT COUNT(*) as count FROM cursos WHERE status = 'ativo'";
$cursos_result = $conn->query($cursos_query);
$cursos_ativos = $cursos_result ? $cursos_result->fetch_assoc()['count'] : 0;
echo "Cursos ativos: " . $cursos_ativos . "\n";

// Verificar TODOS os professores (qualquer status)
echo "\n=== PROFESSORES ===\n";
$professores_query = "SELECT COUNT(*) as count FROM professores";
$professores_result = $conn->query($professores_query);
$professores_total = $professores_result ? $professores_result->fetch_assoc()['count'] : 0;
echo "Total de professores: " . $professores_total . "\n";

$professores_query = "SELECT COUNT(*) as count FROM professores WHERE status = 'ativo'";
$professores_result = $conn->query($professores_query);
$professores_ativos = $professores_result ? $professores_result->fetch_assoc()['count'] : 0;
echo "Professores ativos: " . $professores_ativos . "\n";

// Verificar TODOS os alunos (qualquer status)
echo "\n=== ALUNOS ===\n";
$alunos_query = "SELECT COUNT(*) as count FROM alunos";
$alunos_result = $conn->query($alunos_query);
$alunos_total = $alunos_result ? $alunos_result->fetch_assoc()['count'] : 0;
echo "Total de alunos: " . $alunos_total . "\n";

$alunos_query = "SELECT COUNT(*) as count FROM alunos WHERE status = 'ativo'";
$alunos_result = $conn->query($alunos_query);
$alunos_ativos = $alunos_result ? $alunos_result->fetch_assoc()['count'] : 0;
echo "Alunos ativos: " . $alunos_ativos . "\n";

// Verificar TODOS os agendamentos
echo "\n=== AGENDAMENTOS ===\n";
$agendamentos_query = "SELECT COUNT(*) as count FROM agendamentos";
$agendamentos_result = $conn->query($agendamentos_query);
$agendamentos_total = $agendamentos_result ? $agendamentos_result->fetch_assoc()['count'] : 0;
echo "Total de agendamentos: " . $agendamentos_total . "\n";

$agendamentos_query = "SELECT COUNT(*) as count FROM agendamentos WHERE status != 'cancelado'";
$agendamentos_result = $conn->query($agendamentos_query);
$agendamentos_ativos = $agendamentos_result ? $agendamentos_result->fetch_assoc()['count'] : 0;
echo "Agendamentos ativos: " . $agendamentos_ativos . "\n";

// Mostrar detalhes dos professores
echo "\n=== DETALHES DOS PROFESSORES ===\n";
$professores_detalhes = "SELECT * FROM professores ORDER BY nome LIMIT 5";
$result = $conn->query($professores_detalhes);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " | Nome: " . $row['nome'] . " | Status: " . $row['status'] . " | Formação: " . $row['formacao'] . "\n";
    }
} else {
    echo "Nenhum professor encontrado.\n";
}

// Mostrar detalhes dos alunos
echo "\n=== DETALHES DOS ALUNOS ===\n";
$alunos_detalhes = "SELECT * FROM alunos ORDER BY nome LIMIT 5";
$result = $conn->query($alunos_detalhes);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " | Nome: " . $row['nome'] . " | Status: " . $row['status'] . " | Email: " . $row['email'] . "\n";
    }
} else {
    echo "Nenhum aluno encontrado.\n";
}

// Verificar se as tabelas existem
echo "\n=== VERIFICAÇÃO DAS TABELAS ===\n";
$tabelas = ['cursos', 'professores', 'alunos', 'agendamentos'];
foreach ($tabelas as $tabela) {
    $check = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($check && $check->num_rows > 0) {
        echo "✅ Tabela '$tabela' existe\n";
    } else {
        echo "❌ Tabela '$tabela' NÃO existe\n";
    }
}
?>


