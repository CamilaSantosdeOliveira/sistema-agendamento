<?php
session_start();
include 'db.php';

echo "<h1>🔍 Teste de Certificados</h1>";

// Verificar se está logado
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'aluno') {
    echo "<p>❌ Usuário não está logado como aluno!</p>";
    echo "<p><a href='login.php'>Fazer Login</a></p>";
    exit();
}

echo "<p>✅ Usuário logado: " . $_SESSION['nome'] . " (ID: " . $_SESSION['user_id'] . ")</p>";

// Buscar certificados do aluno
$aluno_id = $_SESSION['user_id'];
$certificados_query = "SELECT c.*, 
                       (SELECT COUNT(*) FROM agendamentos WHERE curso_id = c.id AND aluno_id = ? AND data_agendamento < CURDATE()) as aulas_concluidas,
                       (SELECT COUNT(*) FROM agendamentos WHERE curso_id = c.id AND aluno_id = ?) as total_aulas
                       FROM cursos c 
                       WHERE EXISTS (SELECT 1 FROM agendamentos WHERE curso_id = c.id AND aluno_id = ?)
                       ORDER BY c.nome";
$stmt = $conn->prepare($certificados_query);
$stmt->bind_param("iii", $aluno_id, $aluno_id, $aluno_id);
$stmt->execute();
$certificados = $stmt->get_result();

echo "<h2>📚 Cursos do Aluno:</h2>";
echo "<ul>";

while ($cert = $certificados->fetch_assoc()) {
    echo "<li>";
    echo "<strong>" . $cert['nome'] . "</strong> (ID: " . $cert['id'] . ")";
    echo "<br>📊 Aulas: " . $cert['aulas_concluidas'] . " de " . $cert['total_aulas'];
    echo "<br>🔗 <a href='detalhes_curso_aluno.php?id=" . $cert['id'] . "' target='_blank'>Ver Curso</a>";
    echo "</li>";
}

echo "</ul>";

echo "<h2>🔗 Links de Teste:</h2>";
echo "<p><a href='certificados_aluno.php' target='_blank'>📄 Página de Certificados</a></p>";
echo "<p><a href='detalhes_curso_aluno.php?id=3' target='_blank'>📖 Detalhes do Curso ID 3</a></p>";
echo "<p><a href='dashboard_aluno.php' target='_blank'>🏠 Dashboard do Aluno</a></p>";

echo "<h2>🔧 Debug:</h2>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
echo "<p>Tipo Usuário: " . $_SESSION['tipo_usuario'] . "</p>";
echo "<p>Nome: " . $_SESSION['nome'] . "</p>";
?>










