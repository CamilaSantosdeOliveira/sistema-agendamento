<?php
echo "<h2>🧪 Teste Local da API</h2>";

// Simular uma requisição POST com JSON
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Simular dados JSON
$json_data = '{"action":"desativar_usuario","id":1}';
file_put_contents('php://temp', $json_data);
rewind(fopen('php://temp', 'r'));

// Capturar output da API
ob_start();

try {
    // Incluir a API
    include 'api/usuarios.php';
    $output = ob_get_clean();
    
    echo "<h3>✅ API executou com sucesso!</h3>";
    echo "<pre>Output: " . htmlspecialchars($output) . "</pre>";
    
    // Tentar decodificar JSON
    $json = json_decode($output, true);
    if ($json) {
        echo "<h3>✅ JSON válido!</h3>";
        echo "<pre>" . print_r($json, true) . "</pre>";
    } else {
        echo "<h3>❌ JSON inválido!</h3>";
        echo "Erro: " . json_last_error_msg();
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "<h3>❌ Erro na API:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>


















