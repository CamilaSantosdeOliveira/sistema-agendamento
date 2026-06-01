<?php
// Teste simples da API
echo "<h2>🧪 Teste da API de Usuários</h2>";

// Teste 1: Verificar se o arquivo existe
echo "<h3>1. Verificando arquivo da API:</h3>";
if (file_exists('api/usuarios.php')) {
    echo "✅ Arquivo api/usuarios.php existe<br>";
} else {
    echo "❌ Arquivo api/usuarios.php não existe<br>";
}

// Teste 2: Verificar se o arquivo db.php existe
echo "<h3>2. Verificando arquivo de banco:</h3>";
if (file_exists('db.php')) {
    echo "✅ Arquivo db.php existe<br>";
} else {
    echo "❌ Arquivo db.php não existe<br>";
}

// Teste 3: Testar conexão com banco
echo "<h3>3. Testando conexão com banco:</h3>";
try {
    include 'db.php';
    echo "✅ Conexão com banco estabelecida<br>";
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
}

// Teste 4: Testar requisição direta
echo "<h3>4. Testando requisição direta:</h3>";
try {
    // Simular uma requisição POST
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['CONTENT_TYPE'] = 'application/json';
    
    // Capturar output
    ob_start();
    include 'api/usuarios.php';
    $output = ob_get_clean();
    
    echo "✅ API executou sem erros<br>";
    echo "<pre>Output: " . htmlspecialchars($output) . "</pre>";
    
} catch (Exception $e) {
    echo "❌ Erro na API: " . $e->getMessage() . "<br>";
}
?>


