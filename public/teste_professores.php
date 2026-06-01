<?php
echo "<h2>🧪 Teste API Professores</h2>";

// Testar se o arquivo da API existe
echo "<h3>1. Verificando arquivo da API:</h3>";
if (file_exists('api/professores.php')) {
    echo "✅ Arquivo api/professores.php existe<br>";
} else {
    echo "❌ Arquivo api/professores.php não existe<br>";
}

// Testar se o arquivo db.php existe
echo "<h3>2. Verificando arquivo db.php:</h3>";
if (file_exists('db.php')) {
    echo "✅ Arquivo db.php existe<br>";
} else {
    echo "❌ Arquivo db.php não existe<br>";
}

// Testar conexão com banco
echo "<h3>3. Testando conexão com banco:</h3>";
include 'db.php';
if ($conn) {
    echo "✅ Conexão com banco OK<br>";
} else {
    echo "❌ Erro na conexão com banco<br>";
}

// Testar consulta de professores
echo "<h3>4. Testando consulta de professores:</h3>";
try {
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "✅ Consulta OK - Total de professores: " . $row['total'] . "<br>";
    } else {
        echo "❌ Erro na consulta: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>";
}

// Testar se a API retorna JSON válido
echo "<h3>5. Testando API diretamente:</h3>";
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
        }
    } else {
        echo "❌ JSON inválido!<br>";
        echo "Erro JSON: " . json_last_error_msg() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao testar API: " . $e->getMessage() . "<br>";
}

echo "<br><a href='professores.php'>Voltar para Professores</a>";
?>
