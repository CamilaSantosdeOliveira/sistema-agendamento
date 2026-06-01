<?php
echo "<h2>🧪 Teste da API de Agendamentos</h2>";

// Testar se conseguimos acessar a API
$api_url = 'http://localhost/api/agendamentos.php';

echo "<h3>🔍 Testando acesso à API:</h3>";
echo "<p>URL: $api_url</p>";

// Teste 1: Verificar se a API responde
echo "<h3>📡 Teste 1: Resposta da API</h3>";
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = file_get_contents($api_url, false, $context);
    
    if ($response !== false) {
        echo "✅ API respondeu com sucesso!<br>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    } else {
        echo "❌ API não respondeu<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro ao acessar API: " . $e->getMessage() . "<br>";
}

// Teste 2: Verificar se o arquivo existe
echo "<h3>📁 Teste 2: Verificar arquivo</h3>";
$api_file = 'api/agendamentos.php';
if (file_exists($api_file)) {
    echo "✅ Arquivo existe: $api_file<br>";
    echo "📏 Tamanho: " . filesize($api_file) . " bytes<br>";
} else {
    echo "❌ Arquivo não existe: $api_file<br>";
}

// Teste 3: Testar conexão direta
echo "<h3>🔌 Teste 3: Conexão direta</h3>";
include 'db.php';

if ($conn->ping()) {
    echo "✅ Conexão com banco OK<br>";
    
    // Testar consulta simples
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos");
    if ($result) {
        $count = $result->fetch_assoc()['total'];
        echo "✅ Consulta funcionou: $count agendamentos<br>";
    } else {
        echo "❌ Erro na consulta: " . $conn->error . "<br>";
    }
} else {
    echo "❌ Erro na conexão com banco<br>";
}

echo "<br><h3>🎯 Próximo passo:</h3>";
echo "<p>Se a API não estiver funcionando, vou criar uma versão simplificada</p>";
?>


