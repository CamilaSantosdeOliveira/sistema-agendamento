<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>🔍 Teste de Erros PHP</h2>";

echo "<h3>✅ PHP básico funcionando</h3>";
echo "Data: " . date('d/m/Y H:i:s') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";

echo "<h3>🔌 Testando conexão com banco:</h3>";

try {
    echo "Tentando conectar ao banco...<br>";
    include 'db.php';
    echo "✅ Conexão com banco OK!<br>";
    
    // Testar consulta simples
    $result = $conn->query("SELECT 1 as teste");
    if ($result) {
        echo "✅ Consulta simples OK!<br>";
    } else {
        echo "❌ Erro na consulta: " . $conn->error . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
}

echo "<h3>🎯 Próximo passo:</h3>";
echo "<p>Se chegou até aqui, o sistema está funcionando!</p>";
echo "<p><a href='dashboard_corrigido.php'>🚀 IR PARA O DASHBOARD</a></p>";
?>




