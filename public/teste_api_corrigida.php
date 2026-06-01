<?php
echo "<h2>🔧 Teste da API Corrigida</h2>";

// Testar se a API está funcionando
$api_url = 'http://localhost/api/agendamentos.php';

echo "<h3>📡 Testando API de agendamentos:</h3>";

try {
    // Fazer requisição GET para a API
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = file_get_contents($api_url, false, $context);
    
    if ($response === false) {
        echo "❌ Erro ao acessar a API<br>";
    } else {
        $data = json_decode($response, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($data['success']) && $data['success']) {
                echo "✅ <strong>SUCESSO!</strong> API funcionando perfeitamente!<br>";
                echo "📋 Agendamentos encontrados: " . count($data['data']) . "<br>";
                
                if (count($data['data']) > 0) {
                    echo "<h4>📅 Próximos agendamentos:</h4>";
                    echo "<ul>";
                    foreach (array_slice($data['data'], 0, 3) as $agendamento) {
                        echo "<li><strong>" . $agendamento['aluno_nome'] . "</strong> - " . 
                             date('d/m/Y', strtotime($agendamento['data'])) . " às " . 
                             $agendamento['hora'] . " com " . $agendamento['professor_nome'] . "</li>";
                    }
                    echo "</ul>";
                }
            } else {
                echo "❌ API retornou erro: " . ($data['error'] ?? 'Erro desconhecido') . "<br>";
            }
        } else {
            echo "❌ Erro ao decodificar JSON da API<br>";
            echo "Resposta: " . htmlspecialchars($response) . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<br><h3>🎯 Próximo passo:</h3>";
echo "<p>Se a API está funcionando, o dashboard deve carregar sem erros!</p>";
echo "<p><a href='dashboard_corrigido.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 TESTAR DASHBOARD</a></p>";
?>


