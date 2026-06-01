<?php
echo "<h1>🔍 DEBUG DA API</h1>";

// Teste 1: Verificar se há erros de sintaxe
echo "<h3>1️⃣ Verificando sintaxe da API:</h3>";
$api_file = 'api/usuarios.php';
if (file_exists($api_file)) {
    $syntax_check = shell_exec("php -l $api_file 2>&1");
    if (strpos($syntax_check, 'No syntax errors') !== false) {
        echo "<p style='color: green;'>✅ Sintaxe da API está correta</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro de sintaxe na API:</p>";
        echo "<pre>" . htmlspecialchars($syntax_check) . "</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ Arquivo da API não encontrado</p>";
}

// Teste 2: Simular execução da API
echo "<h3>2️⃣ Simulando execução da API:</h3>";
try {
    // Capturar erros
    ob_start();
    
    // Simular variáveis da API
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['action'] = 'listar_alunos';
    
    // Incluir a API
    include 'api/usuarios.php';
    
    $output = ob_get_clean();
    
    echo "<p style='color: green;'>✅ API executou</p>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na execução: " . $e->getMessage() . "</p>";
}

// Teste 3: Verificar se o arquivo db.php está acessível
echo "<h3>3️⃣ Verificando db.php:</h3>";
$db_file = 'db.php';
if (file_exists($db_file)) {
    echo "<p style='color: green;'>✅ Arquivo db.php existe</p>";
    
    // Testar inclusão
    try {
        include $db_file;
        if (isset($conn)) {
            echo "<p style='color: green;'>✅ Conexão com banco estabelecida</p>";
        } else {
            echo "<p style='color: red;'>❌ Variável \$conn não definida</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro ao incluir db.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Arquivo db.php não encontrado</p>";
}

echo "<br><h3>🔗 Links para testar:</h3>";
echo "<p><a href='alunos.php'>👨‍🎓 Ver Página de Alunos</a></p>";
echo "<p><a href='dashboard_final.php'>📊 Dashboard Principal</a></p>";
?>









