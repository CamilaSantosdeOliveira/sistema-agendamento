<?php
echo "<h2>🧪 Teste Direto da API</h2>";

// Teste 1: Verificar se há erros de sintaxe
echo "<h3>1. Verificando sintaxe PHP:</h3>";
$output = shell_exec('php -l api/usuarios.php 2>&1');
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Teste 2: Verificar se o arquivo db.php existe
echo "<h3>2. Verificando arquivo db.php:</h3>";
if (file_exists('db.php')) {
    echo "✅ Arquivo db.php existe<br>";
} else {
    echo "❌ Arquivo db.php não existe<br>";
}

// Teste 3: Verificar se há erros de sintaxe no db.php
echo "<h3>3. Verificando sintaxe do db.php:</h3>";
$output = shell_exec('php -l db.php 2>&1');
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Teste 4: Testar conexão com banco
echo "<h3>4. Testando conexão com banco:</h3>";
try {
    include 'db.php';
    echo "✅ Conexão com banco estabelecida<br>";
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
}

// Teste 5: Simular uma requisição simples
echo "<h3>5. Testando API diretamente:</h3>";
try {
    // Simular variáveis de servidor
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['action'] = 'listar_alunos';
    
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
















