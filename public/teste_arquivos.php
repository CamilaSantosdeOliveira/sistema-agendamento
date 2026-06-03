<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>🔍 Teste de Arquivos Individuais</h2>";

// Teste 1: db.php
echo "<h3>1️⃣ Testando db.php:</h3>";
try {
    include 'db.php';
    echo "✅ db.php OK!<br>";
} catch (Exception $e) {
    echo "❌ Erro em db.php: " . $e->getMessage() . "<br>";
}

// Teste 2: api/dashboard_stats.php
echo "<h3>2️⃣ Testando api/dashboard_stats.php:</h3>";
try {
    ob_start();
    include 'api/dashboard_stats.php';
    $output = ob_get_clean();
    echo "✅ api/dashboard_stats.php OK!<br>";
} catch (Exception $e) {
    echo "❌ Erro em api/dashboard_stats.php: " . $e->getMessage() . "<br>";
}

// Teste 3: api/agendamentos.php
echo "<h3>3️⃣ Testando api/agendamentos.php:</h3>";
try {
    ob_start();
    include 'api/agendamentos.php';
    $output = ob_get_clean();
    echo "✅ api/agendamentos.php OK!<br>";
} catch (Exception $e) {
    echo "❌ Erro em api/agendamentos.php: " . $e->getMessage() . "<br>";
}

// Teste 4: agendar_direto.php
echo "<h3>4️⃣ Testando agendar_direto.php:</h3>";
try {
    ob_start();
    include 'agendar_direto.php';
    $output = ob_get_clean();
    echo "✅ agendar_direto.php OK!<br>";
} catch (Exception $e) {
    echo "❌ Erro em agendar_direto.php: " . $e->getMessage() . "<br>";
}

echo "<h3>🎯 Resultado:</h3>";
echo "<p>Se todos os arquivos estão OK, o problema pode estar no JavaScript do dashboard.</p>";
echo "<p><a href='dashboard_corrigido.php'>🚀 TESTAR DASHBOARD</a></p>";
?>




