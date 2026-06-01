<?php
include 'db.php';

echo "<h2>Verificação dos Dados do Dashboard</h2>";

// Verificar cursos
echo "<h3>Cursos:</h3>";
$cursos_query = "SELECT COUNT(*) as count FROM cursos WHERE status = 'ativo'";
$cursos_result = $conn->query($cursos_query);
$cursos_count = $cursos_result ? $cursos_result->fetch_assoc()['count'] : 0;
echo "Total de cursos ativos: " . $cursos_count . "<br>";

// Verificar professores
echo "<h3>Professores:</h3>";
$professores_query = "SELECT COUNT(*) as count FROM professores WHERE status = 'ativo'";
$professores_result = $conn->query($professores_query);
$professores_count = $professores_result ? $professores_result->fetch_assoc()['count'] : 0;
echo "Total de professores ativos: " . $professores_count . "<br>";

// Verificar alunos
echo "<h3>Alunos:</h3>";
$alunos_query = "SELECT COUNT(*) as count FROM alunos WHERE status = 'ativo'";
$alunos_result = $conn->query($alunos_query);
$alunos_count = $alunos_result ? $alunos_result->fetch_assoc()['count'] : 0;
echo "Total de alunos ativos: " . $alunos_count . "<br>";

// Verificar agendamentos
echo "<h3>Agendamentos:</h3>";
$agendamentos_query = "SELECT COUNT(*) as count FROM agendamentos WHERE status != 'cancelado'";
$agendamentos_result = $conn->query($agendamentos_query);
$agendamentos_count = $agendamentos_result ? $agendamentos_result->fetch_assoc()['count'] : 0;
echo "Total de agendamentos (não cancelados): " . $agendamentos_count . "<br>";

// Mostrar detalhes dos agendamentos
echo "<h3>Detalhes dos Agendamentos:</h3>";
$agendamentos_detalhes = "SELECT * FROM agendamentos WHERE status != 'cancelado' ORDER BY data DESC LIMIT 5";
$result = $conn->query($agendamentos_detalhes);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Data</th><th>Hora</th><th>Professor</th><th>Serviço</th><th>Aluno</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['data'] . "</td>";
        echo "<td>" . $row['hora'] . "</td>";
        echo "<td>" . $row['professor'] . "</td>";
        echo "<td>" . $row['servico'] . "</td>";
        echo "<td>" . $row['nome'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Nenhum agendamento encontrado.<br>";
}

// Verificar se as tabelas existem
echo "<h3>Verificação das Tabelas:</h3>";
$tabelas = ['cursos', 'professores', 'alunos', 'agendamentos'];
foreach ($tabelas as $tabela) {
    $check = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($check && $check->num_rows > 0) {
        echo "✅ Tabela '$tabela' existe<br>";
    } else {
        echo "❌ Tabela '$tabela' NÃO existe<br>";
    }
}
?>


