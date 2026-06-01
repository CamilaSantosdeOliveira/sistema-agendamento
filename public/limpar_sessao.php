<?php
session_start();

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();

// Limpar cookies de sessão
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

echo "<h1>✅ Sessão Limpa com Sucesso!</h1>";
echo "<p>A sessão foi destruída e os cookies foram limpos.</p>";
echo "<p>Você pode agora fazer login novamente.</p>";

echo "<div style='margin: 20px 0;'>";
echo "<a href='login.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Ir para Login</a>";
echo "</div>";

echo "<h3>📋 Dados de Login:</h3>";
echo "<ul>";
echo "<li><strong>Admin:</strong> admin@educonnect.com / 123456</li>";
echo "<li><strong>Professor:</strong> ricardo.silva@educonnect.com / 123456</li>";
echo "<li><strong>Aluno:</strong> camilacah7890@gmail.com / 123456</li>";
echo "</ul>";
?>





