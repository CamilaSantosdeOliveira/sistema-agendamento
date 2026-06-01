<?php
session_start();

echo "<h1>Teste de Redirecionamento</h1>";
echo "<p>Status da sessão: " . (isset($_SESSION['user_logged_in']) ? $_SESSION['user_logged_in'] : 'NÃO DEFINIDA') . "</p>";

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    echo "<p style='color: red;'>❌ NÃO está logado - Redirecionando...</p>";
    echo "<script>setTimeout(function() { window.location.href = 'login.html'; }, 2000);</script>";
} else {
    echo "<p style='color: green;'>✅ Está logado!</p>";
    echo "<p>Usuário: " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'N/A') . "</p>";
}
?>


