<?php
echo "<h2>🧪 Teste Direto da API</h2>";

// Testar API de cursos
echo "<h3>1. Testando API de Cursos:</h3>";
$url = 'http://localhost:8080/Sistema%20De%20Agendamento/public/api/cursos.php';
echo "URL: $url<br>";

try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    echo "Resposta bruta:<br>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    // Tentar decodificar JSON
    $json = json_decode($response, true);
    if ($json) {
        echo "✅ JSON válido!<br>";
        echo "Success: " . ($json['success'] ? 'true' : 'false') . "<br>";
        if (isset($json['data'])) {
            echo "Dados: " . count($json['data']) . " cursos encontrados<br>";
        }
    } else {
        echo "❌ JSON inválido!<br>";
        echo "Erro JSON: " . json_last_error_msg() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro na requisição: " . $e->getMessage() . "<br>";
}

// Testar API de cursos com ID específico
echo "<h3>2. Testando API de Cursos com ID:</h3>";
$url_id = 'http://localhost:8080/Sistema%20De%20Agendamento/public/api/cursos.php/1';
echo "URL: $url_id<br>";

try {
    $response_id = file_get_contents($url_id, false, $context);
    echo "Resposta bruta:<br>";
    echo "<pre>" . htmlspecialchars($response_id) . "</pre>";
    
    $json_id = json_decode($response_id, true);
    if ($json_id) {
        echo "✅ JSON válido!<br>";
    } else {
        echo "❌ JSON inválido!<br>";
        echo "Erro JSON: " . json_last_error_msg() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro na requisição: " . $e->getMessage() . "<br>";
}

echo "<br><a href='cursos_completo.php'>Voltar para Cursos</a>";
?>


