<?php
session_start();
include 'db.php';

echo "<h1>🔍 Teste de Aulas do Professor</h1>";

// Buscar todas as aulas do Prof. Ricardo Silva (ID: 2)
$professor_id = 2;

echo "<h2>📊 Todas as Aulas do Prof. Ricardo Silva (ID: $professor_id)</h2>";

$query = "SELECT a.*, c.nome as curso_nome, u.nome as aluno_nome, u.id as aluno_id
          FROM agendamentos a 
          JOIN cursos c ON a.curso_id = c.id 
          JOIN usuarios u ON a.aluno_id = u.id 
          WHERE a.professor_id = ? 
          ORDER BY a.data_agendamento, a.hora_inicio";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th>ID</th><th>Curso</th><th>Aluno</th><th>Data</th><th>Hora</th><th>Status</th>";
echo "</tr>";

$total_aulas = 0;
while ($aula = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $aula['id'] . "</td>";
    echo "<td>" . $aula['curso_nome'] . "</td>";
    echo "<td>" . $aula['aluno_nome'] . " (ID: " . $aula['aluno_id'] . ")</td>";
    echo "<td>" . $aula['data_agendamento'] . "</td>";
    echo "<td>" . $aula['hora_inicio'] . "</td>";
    echo "<td>" . $aula['status'] . "</td>";
    echo "</tr>";
    $total_aulas++;
}

echo "</table>";

echo "<h3>📈 Resumo:</h3>";
echo "<p><strong>Total de aulas:</strong> $total_aulas</p>";

// Verificar aulas futuras
$query_futuras = "SELECT COUNT(*) as total FROM agendamentos 
                  WHERE professor_id = ? AND data_agendamento >= CURDATE()";
$stmt = $conn->prepare($query_futuras);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$futuras = $stmt->get_result()->fetch_assoc();

echo "<p><strong>Aulas futuras:</strong> " . $futuras['total'] . "</p>";

// Verificar aulas passadas
$query_passadas = "SELECT COUNT(*) as total FROM agendamentos 
                   WHERE professor_id = ? AND data_agendamento < CURDATE()";
$stmt = $conn->prepare($query_passadas);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$passadas = $stmt->get_result()->fetch_assoc();

echo "<p><strong>Aulas passadas:</strong> " . $passadas['total'] . "</p>";

echo "<h3>🔗 Links:</h3>";
echo "<p><a href='dashboard_professor.php' target='_blank'>Dashboard do Professor</a></p>";
echo "<p><a href='aulas_professor.php' target='_blank'>Página de Aulas</a></p>";
?>






