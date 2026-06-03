<?php
include 'db.php';

echo "<h1>🔍 Verificação Rápida dos Dados</h1>";

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<p style='color: green;'>✅ Conexão OK</p>";

// Verificar usuários
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$total_usuarios = $result ? $result->fetch_assoc()['total'] : 0;
echo "<p>👥 Total de usuários: <strong>$total_usuarios</strong></p>";

// Verificar professores
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'");
$total_professores = $result ? $result->fetch_assoc()['total'] : 0;
echo "<p>👨‍🏫 Total de professores: <strong>$total_professores</strong></p>";

// Verificar alunos
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
$total_alunos = $result ? $result->fetch_assoc()['total'] : 0;
echo "<p>👨‍🎓 Total de alunos: <strong>$total_alunos</strong></p>";

// Verificar cursos
$result = $conn->query("SELECT COUNT(*) as total FROM cursos");
$total_cursos = $result ? $result->fetch_assoc()['total'] : 0;
echo "<p>📚 Total de cursos: <strong>$total_cursos</strong></p>";

// Verificar agendamentos
$result = $conn->query("SELECT COUNT(*) as total FROM agendamentos");
$total_agendamentos = $result ? $result->fetch_assoc()['total'] : 0;
echo "<p>📅 Total de agendamentos: <strong>$total_agendamentos</strong></p>";

// Mostrar alguns professores se existirem
if ($total_professores > 0) {
    echo "<h3>👨‍🏫 Professores:</h3>";
    $result = $conn->query("SELECT nome, email FROM usuarios WHERE tipo_usuario = 'professor' LIMIT 5");
    while ($row = $result->fetch_assoc()) {
        echo "<p>• {$row['nome']} ({$row['email']})</p>";
    }
}

// Mostrar alguns alunos se existirem
if ($total_alunos > 0) {
    echo "<h3>👨‍🎓 Alunos:</h3>";
    $result = $conn->query("SELECT nome, email FROM usuarios WHERE tipo_usuario = 'aluno' LIMIT 5");
    while ($row = $result->fetch_assoc()) {
        echo "<p>• {$row['nome']} ({$row['email']})</p>";
    }
}

// Mostrar alguns cursos se existirem
if ($total_cursos > 0) {
    echo "<h3>📚 Cursos:</h3>";
    $result = $conn->query("SELECT nome, categoria, preco FROM cursos LIMIT 5");
    while ($row = $result->fetch_assoc()) {
        echo "<p>• {$row['nome']} ({$row['categoria']}) - R$ {$row['preco']}</p>";
    }
}

echo "<hr>";
echo "<p><a href='dashboard_final.php' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🚀 Acessar Dashboard</a></p>";
?>










