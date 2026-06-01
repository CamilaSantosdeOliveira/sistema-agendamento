<?php
session_start();
include 'db.php';

echo "<h1>🔍 Verificação de Inscrição do Aluno</h1>";

// Verificar se o aluno está logado
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'aluno') {
    echo "<p style='color: red;'>❌ Aluno não está logado!</p>";
    echo "<a href='login.php'>Fazer Login</a>";
    exit();
}

$aluno_id = $_SESSION['user_id'];

// Buscar dados do aluno
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'aluno'");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$aluno = $stmt->get_result()->fetch_assoc();

echo "<h2>👨‍🎓 Dados do Aluno</h2>";
echo "<p><strong>Nome:</strong> " . $aluno['nome'] . "</p>";
echo "<p><strong>Email:</strong> " . $aluno['email'] . "</p>";
echo "<p><strong>ID:</strong> " . $aluno['id'] . "</p>";

// Verificar agendamentos do aluno
$stmt = $conn->prepare("SELECT a.*, c.nome as curso_nome, u.nome as professor_nome 
                       FROM agendamentos a 
                       JOIN cursos c ON a.curso_id = c.id 
                       JOIN usuarios u ON a.professor_id = u.id 
                       WHERE a.aluno_id = ?");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$agendamentos = $stmt->get_result();

echo "<h2>📅 Agendamentos do Aluno</h2>";
if ($agendamentos->num_rows > 0) {
    echo "<p style='color: green;'>✅ Encontrados " . $agendamentos->num_rows . " agendamentos</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Curso</th><th>Professor</th><th>Data</th><th>Hora</th><th>Duração</th><th>Status</th></tr>";
    
    while ($agendamento = $agendamentos->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $agendamento['curso_nome'] . "</td>";
        echo "<td>" . $agendamento['professor_nome'] . "</td>";
        echo "<td>" . $agendamento['data_agendamento'] . "</td>";
        echo "<td>" . $agendamento['hora_inicio'] . "</td>";
        echo "<td>" . $agendamento['duracao_horas'] . "h</td>";
        echo "<td>" . ($agendamento['status'] ?: 'Agendado') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Nenhum agendamento encontrado!</p>";
}

// Verificar cursos disponíveis
$stmt = $conn->prepare("SELECT c.*, 
                       (SELECT COUNT(*) FROM agendamentos WHERE curso_id = c.id AND aluno_id = ?) as ja_inscrito
                       FROM cursos c");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$cursos = $stmt->get_result();

echo "<h2>📚 Cursos Disponíveis</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Curso</th><th>Categoria</th><th>Nível</th><th>Preço</th><th>Inscrito</th></tr>";

while ($curso = $cursos->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $curso['nome'] . "</td>";
    echo "<td>" . $curso['categoria'] . "</td>";
    echo "<td>" . $curso['nivel'] . "</td>";
    echo "<td>R$ " . number_format($curso['preco'], 2, ',', '.') . "</td>";
    echo "<td>" . ($curso['ja_inscrito'] > 0 ? "✅ Sim" : "❌ Não") . "</td>";
    echo "</tr>";
}
echo "</table>";

// Verificar professores disponíveis
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1");
$stmt->execute();
$professores = $stmt->get_result();

echo "<h2>👨‍🏫 Professores Disponíveis</h2>";
if ($professores->num_rows > 0) {
    echo "<p style='color: green;'>✅ Encontrados " . $professores->num_rows . " professores</p>";
    echo "<ul>";
    while ($professor = $professores->fetch_assoc()) {
        echo "<li>" . $professor['nome'] . " (ID: " . $professor['id'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ Nenhum professor encontrado!</p>";
}

echo "<h2>🔧 Ações</h2>";
echo "<p><a href='criar_agendamentos_teste.php' style='background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>➕ Criar Agendamentos de Teste</a></p>";
echo "<p><a href='minhas_aulas_aluno.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📅 Ver Minhas Aulas</a></p>";
echo "<p><a href='meus_cursos_aluno.php' style='background: #8b5cf6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📚 Ver Meus Cursos</a></p>";
echo "<p><a href='dashboard_aluno.php' style='background: #f59e0b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Voltar ao Dashboard</a></p>";
?>






