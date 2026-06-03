<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>🔍 Teste de Requisições do Dashboard</h2>";

// Simular as requisições que o dashboard faz
echo "<h3>1️⃣ Testando api/dashboard_stats.php (GET):</h3>";

try {
    // Simular requisição GET
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    ob_start();
    include 'api/dashboard_stats.php';
    $output = ob_get_clean();
    
    $data = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($data['success']) && $data['success']) {
            echo "✅ api/dashboard_stats.php funcionando!<br>";
            echo "📊 Dados recebidos: " . count($data['data']) . " estatísticas<br>";
        } else {
            echo "❌ Erro na API: " . ($data['error'] ?? 'Erro desconhecido') . "<br>";
        }
    } else {
        echo "❌ Erro no JSON: " . json_last_error_msg() . "<br>";
        echo "Resposta: " . htmlspecialchars($output) . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<h3>2️⃣ Testando api/agendamentos.php (GET):</h3>";

try {
    // Simular requisição GET
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    ob_start();
    include 'api/agendamentos.php';
    $output = ob_get_clean();
    
    $data = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($data['success']) && $data['success']) {
            echo "✅ api/agendamentos.php funcionando!<br>";
            echo "📅 Agendamentos: " . count($data['data']) . " encontrados<br>";
        } else {
            echo "❌ Erro na API: " . ($data['error'] ?? 'Erro desconhecido') . "<br>";
        }
    } else {
        echo "❌ Erro no JSON: " . json_last_error_msg() . "<br>";
        echo "Resposta: " . htmlspecialchars($output) . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<h3>🎯 Resultado:</h3>";
echo "<p>Se ambas as APIs estão funcionando, o problema pode estar no JavaScript.</p>";
echo "<p><a href='dashboard_corrigido.php'>🚀 TESTAR DASHBOARD</a></p>";
?>




