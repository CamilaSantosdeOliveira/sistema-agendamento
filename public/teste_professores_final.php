<?php
echo "<h2>🧪 Teste Final API Professores</h2>";

// Testar se a API retorna JSON válido
echo "<h3>Testando API diretamente:</h3>";
try {
    // Simular uma requisição GET
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/api/professores.php';
    
    // Capturar a saída da API
    ob_start();
    include 'api/professores.php';
    $output = ob_get_clean();
    
    echo "Resposta da API:<br>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    // Tentar decodificar JSON
    $json = json_decode($output, true);
    if ($json) {
        echo "✅ JSON válido!<br>";
        echo "Success: " . ($json['success'] ? 'true' : 'false') . "<br>";
        if (isset($json['data'])) {
            echo "Total de professores na API: " . count($json['data']) . "<br>";
            echo "<br>📋 Professores encontrados:<br>";
            foreach ($json['data'] as $prof) {
                echo "- {$prof['nome']} ({$prof['email']}) - " . ($prof['ativo'] ? 'Ativo' : 'Inativo') . "<br>";
            }
        }
    } else {
        echo "❌ JSON inválido!<br>";
        echo "Erro JSON: " . json_last_error_msg() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao testar API: " . $e->getMessage() . "<br>";
}

echo "<br><h3>🎯 Próximos passos:</h3>";
echo "<p><a href='professores.php'>Testar página de professores</a></p>";
echo "<p><a href='dashboard_corrigido.php'>Voltar ao Dashboard</a></p>";
?>


