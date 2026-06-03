<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>🔍 Teste Específico - API Agendamentos GET</h2>";

// Simular requisição GET
$_SERVER['REQUEST_METHOD'] = 'GET';

try {
    ob_start();
    include 'api/agendamentos.php';
    $output = ob_get_clean();
    
    echo "<h3>📄 Resposta da API:</h3>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    $data = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($data['success']) && $data['success']) {
            echo "<h3>✅ SUCESSO!</h3>";
            echo "<p>API funcionando perfeitamente!</p>";
            echo "<p>Agendamentos encontrados: " . count($data['data']) . "</p>";
        } else {
            echo "<h3>❌ ERRO</h3>";
            echo "<p>Erro: " . ($data['message'] ?? 'Erro desconhecido') . "</p>";
        }
    } else {
        echo "<h3>❌ ERRO JSON</h3>";
        echo "<p>Erro ao decodificar JSON: " . json_last_error_msg() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<h3>❌ EXCEÇÃO</h3>";
    echo "<p>Erro: " . $e->getMessage() . "</p>";
}

echo "<br><h3>🎯 Próximo passo:</h3>";
echo "<p><a href='dashboard_corrigido.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 TESTAR DASHBOARD</a></p>";
?>




