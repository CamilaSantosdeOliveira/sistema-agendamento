<?php
session_start();

echo "<h1>🔍 Teste de Sessão e Redirecionamento</h1>";

echo "<h2>📋 Status da Sessão:</h2>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Usuário ID: " . ($_SESSION['usuario_id'] ?? 'Não definido') . "</p>";
echo "<p>Usuário Nome: " . ($_SESSION['usuario_nome'] ?? 'Não definido') . "</p>";
echo "<p>Usuário Tipo: " . ($_SESSION['usuario_tipo'] ?? 'Não definido') . "</p>";

echo "<h2>🎯 Teste de Redirecionamento:</h2>";

if (isset($_SESSION['usuario_id'])) {
    echo "<p>✅ Usuário logado - Deve redirecionar para dashboard</p>";
    echo "<p><a href='dashboard_final.php'>🔗 Ir para Dashboard</a></p>";
} else {
    echo "<p>❌ Usuário não logado - Deve redirecionar para login</p>";
    echo "<p><a href='login.php'>🔗 Ir para Login</a></p>";
}

echo "<h2>🧹 Ações:</h2>";
echo "<p><a href='?acao=logout'>🚪 Fazer Logout</a></p>";
echo "<p><a href='?acao=limpar'>🗑️ Limpar Sessão</a></p>";

// Processar ações
if (isset($_GET['acao'])) {
    switch ($_GET['acao']) {
        case 'logout':
            session_destroy();
            echo "<p>✅ Logout realizado!</p>";
            echo "<script>setTimeout(() => window.location.href='index.php', 1000);</script>";
            break;
        case 'limpar':
            session_unset();
            session_destroy();
            echo "<p>✅ Sessão limpa!</p>";
            echo "<script>setTimeout(() => window.location.href='index.php', 1000);</script>";
            break;
    }
}

echo "<h2>🔗 Links de Teste:</h2>";
echo "<p><a href='index.php'>🏠 Testar index.php</a></p>";
echo "<p><a href='login.php'>🔐 Testar login.php</a></p>";
echo "<p><a href='dashboard_final.php'>📊 Testar dashboard_final.php</a></p>";
?>








