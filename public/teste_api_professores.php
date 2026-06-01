<?php
echo "<h1>🧪 TESTE API PROFESSORES</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow-x:auto;}</style>";

// Testar API diretamente
$url = 'http://localhost:8080/Sistema%20De%20Agendamento/public/api/professores_simples.php';

echo "<div class='info'>🔗 Testando URL: {$url}</div>";

// Fazer requisição
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json'
    ]
]);

$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "<div class='error'>❌ Erro ao acessar API</div>";
} else {
    echo "<div class='success'>✅ Resposta recebida!</div>";
    echo "<div class='info'>📊 Tamanho da resposta: " . strlen($response) . " bytes</div>";
    
    echo "<h2>📋 Resposta da API:</h2>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    // Tentar decodificar JSON
    $json = json_decode($response, true);
    if ($json === null) {
        echo "<div class='error'>❌ Erro ao decodificar JSON</div>";
        echo "<div class='error'>Erro: " . json_last_error_msg() . "</div>";
    } else {
        echo "<div class='success'>✅ JSON válido!</div>";
        
        if (isset($json['success']) && $json['success']) {
            echo "<div class='success'>✅ API funcionando!</div>";
            if (isset($json['data'])) {
                echo "<div class='info'>📊 Professores encontrados: " . count($json['data']) . "</div>";
                
                if (count($json['data']) > 0) {
                    echo "<h2>👨‍🏫 Professores:</h2>";
                    echo "<table style='border-collapse:collapse;width:100%;'>";
                    echo "<tr style='background:#f2f2f2;'><th>ID</th><th>Nome</th><th>Email</th><th>Ativo</th></tr>";
                    
                    foreach ($json['data'] as $professor) {
                        echo "<tr>";
                        echo "<td style='border:1px solid #ddd;padding:8px;'>{$professor['id']}</td>";
                        echo "<td style='border:1px solid #ddd;padding:8px;'><strong>{$professor['nome']}</strong></td>";
                        echo "<td style='border:1px solid #ddd;padding:8px;'>{$professor['email']}</td>";
                        echo "<td style='border:1px solid #ddd;padding:8px;'>" . ($professor['ativo'] ? '✅' : '❌') . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            }
        } else {
            echo "<div class='error'>❌ API retornou erro</div>";
            if (isset($json['error'])) {
                echo "<div class='error'>Erro: {$json['error']}</div>";
            }
        }
    }
}

echo "<h2>🎯 PRÓXIMOS PASSOS:</h2>";
echo "<div style='margin:20px 0;'>";
echo "<a href='professores.php' style='background:green;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;'>📊 Ver Página Professores</a>";
echo "<a href='api/professores_simples.php' style='background:blue;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;'>🔗 Ver API Direto</a>";
echo "</div>";
?>







