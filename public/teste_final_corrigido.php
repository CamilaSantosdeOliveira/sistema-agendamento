<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>🎯 Teste Final - Todas as Correções</h2>";

// Teste 1: api/dashboard_stats.php
echo "<h3>1️⃣ Testando api/dashboard_stats.php:</h3>";
try {
    ob_start();
    include 'api/dashboard_stats.php';
    $output = ob_get_clean();
    
    $data = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($data['success']) && $data['success']) {
            echo "✅ <strong>SUCESSO!</strong> api/dashboard_stats.php funcionando!<br>";
            echo "📊 Estatísticas: " . count($data['data']) . " itens carregados<br>";
        } else {
            echo "❌ Erro na API: " . ($data['error'] ?? 'Erro desconhecido') . "<br>";
        }
    } else {
        echo "❌ Erro no JSON: " . json_last_error_msg() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

// Teste 2: api/agendamentos.php
echo "<h3>2️⃣ Testando api/agendamentos.php:</h3>";
try {
    ob_start();
    include 'api/agendamentos.php';
    $output = ob_get_clean();
    
    $data = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($data['success']) && $data['success']) {
            echo "✅ <strong>SUCESSO!</strong> api/agendamentos.php funcionando!<br>";
            echo "📅 Agendamentos: " . count($data['data']) . " encontrados<br>";
        } else {
            echo "❌ Erro na API: " . ($data['error'] ?? 'Erro desconhecido') . "<br>";
        }
    } else {
        echo "❌ Erro no JSON: " . json_last_error_msg() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<br><h3>🎉 Resultado Final:</h3>";
echo "<p>Se ambos os testes passaram, o sistema está 100% funcional!</p>";
echo "<p><a href='dashboard_corrigido.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 IR PARA O DASHBOARD</a></p>";
?>




