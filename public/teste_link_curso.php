<?php
session_start();

echo "<h1>🔗 Teste de Link do Curso</h1>";

// Verificar se está logado
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'aluno') {
    echo "<p>❌ Usuário não está logado como aluno!</p>";
    echo "<p><a href='login.php'>Fazer Login</a></p>";
    exit();
}

echo "<p>✅ Usuário logado: " . $_SESSION['nome'] . "</p>";

// Testar links diretos
echo "<h2>🔗 Links de Teste:</h2>";
echo "<p><a href='detalhes_curso_aluno.php?id=3' target='_blank'>📖 Curso ID 3 (Mobile App Development)</a></p>";
echo "<p><a href='detalhes_curso_aluno.php?id=1' target='_blank'>📖 Curso ID 1 (Desenvolvimento Web)</a></p>";
echo "<p><a href='detalhes_curso_aluno.php?id=2' target='_blank'>📖 Curso ID 2 (DevOps)</a></p>";

echo "<h2>🔧 Debug Session:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>🔗 Teste com JavaScript:</h2>";
echo "<button onclick=\"window.open('detalhes_curso_aluno.php?id=3', '_blank')\">Abrir Curso ID 3</button>";
echo "<button onclick=\"window.location.href='detalhes_curso_aluno.php?id=3'\">Ir para Curso ID 3</button>";
?>








