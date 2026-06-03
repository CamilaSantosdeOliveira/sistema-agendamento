<?php
echo "<h2>🔍 VERIFICAÇÃO COMPLETA - SERVIÇOS XAMPP</h2>";
echo "<p><strong>Problema:</strong> Não é possível acessar localhost (ERR_CONNECTION_REFUSED)</p>";

// Função para executar comandos
function executarComando($comando) {
    $output = [];
    $return_var = 0;
    exec($comando . " 2>&1", $output, $return_var);
    return ['output' => $output, 'return' => $return_var];
}

echo "<h3>📋 PASSO 1: Verificando portas</h3>";

// Verificar porta 80 (Apache)
$resultado = executarComando('netstat -an | find ":80"');
if (strpos(implode(' ', $resultado['output']), 'LISTENING') !== false) {
    echo "✅ <strong>Porta 80:</strong> Apache está rodando<br>";
} else {
    echo "❌ <strong>Porta 80:</strong> Apache NÃO está rodando<br>";
}

// Verificar porta 8080 (Apache alternativo)
$resultado = executarComando('netstat -an | find ":8080"');
if (strpos(implode(' ', $resultado['output']), 'LISTENING') !== false) {
    echo "✅ <strong>Porta 8080:</strong> Apache está rodando<br>";
} else {
    echo "❌ <strong>Porta 8080:</strong> Apache NÃO está rodando<br>";
}

// Verificar porta 3306 (MySQL)
$resultado = executarComando('netstat -an | find ":3306"');
if (strpos(implode(' ', $resultado['output']), 'LISTENING') !== false) {
    echo "✅ <strong>Porta 3306:</strong> MySQL está rodando<br>";
} else {
    echo "❌ <strong>Porta 3306:</strong> MySQL NÃO está rodando<br>";
}

echo "<h3>🔧 PASSO 2: Verificando processos</h3>";

// Verificar processo Apache
$resultado = executarComando('tasklist | find "httpd.exe"');
if ($resultado['return'] == 0) {
    echo "✅ <strong>Apache:</strong> Processo encontrado<br>";
} else {
    echo "❌ <strong>Apache:</strong> Processo NÃO encontrado<br>";
}

// Verificar processo MySQL
$resultado = executarComando('tasklist | find "mysqld.exe"');
if ($resultado['return'] == 0) {
    echo "✅ <strong>MySQL:</strong> Processo encontrado<br>";
} else {
    echo "❌ <strong>MySQL:</strong> Processo NÃO encontrado<br>";
}

echo "<h3>🚀 PASSO 3: Iniciando XAMPP</h3>";

// Tentar iniciar XAMPP Control Panel
echo "🔄 Iniciando XAMPP Control Panel...<br>";
exec('start "" "C:\xampp\xampp-control.exe"');
sleep(3);

echo "<h3>⚡ PASSO 4: Iniciando serviços manualmente</h3>";

// Iniciar Apache
echo "🔄 Iniciando Apache...<br>";
$resultado = executarComando('"C:\xampp\apache\bin\httpd.exe" -k start');
if ($resultado['return'] == 0) {
    echo "✅ Apache iniciado com sucesso!<br>";
} else {
    echo "❌ Erro ao iniciar Apache<br>";
    echo "<pre>" . implode("\n", $resultado['output']) . "</pre>";
}

// Aguardar um pouco
sleep(3);

// Iniciar MySQL (após corrigir replicação)
echo "🔄 Iniciando MySQL...<br>";
$resultado = executarComando('"C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone');
if ($resultado['return'] == 0) {
    echo "✅ MySQL iniciado com sucesso!<br>";
} else {
    echo "❌ Erro ao iniciar MySQL<br>";
    echo "<pre>" . implode("\n", $resultado['output']) . "</pre>";
}

echo "<h3>🔍 PASSO 5: Testando conexões</h3>";

// Aguardar um pouco para os serviços inicializarem
sleep(5);

// Testar Apache
$context = stream_context_create(['http' => ['timeout' => 5]]);
$resultado_apache = @file_get_contents('http://localhost', false, $context);
if ($resultado_apache !== false) {
    echo "✅ <strong>Apache:</strong> Funcionando! Página carregada com sucesso<br>";
} else {
    echo "❌ <strong>Apache:</strong> Não conseguiu carregar página<br>";
}

// Testar MySQL
try {
    $conn = new mysqli('localhost', 'root', '', '', 3306);
    if ($conn->connect_error) {
        echo "❌ <strong>MySQL:</strong> " . $conn->connect_error . "<br>";
    } else {
        echo "✅ <strong>MySQL:</strong> Conexão bem-sucedida!<br>";
        $conn->close();
    }
} catch (Exception $e) {
    echo "❌ <strong>MySQL:</strong> " . $e->getMessage() . "<br>";
}

echo "<h3>📝 SOLUÇÃO MANUAL</h3>";
echo "<ol>";
echo "<li><strong>Abra o XAMPP Control Panel</strong></li>";
echo "<li><strong>Clique em 'Start' para Apache</strong></li>";
echo "<li><strong>Clique em 'Start' para MySQL</strong></li>";
echo "<li><strong>Se houver erro no MySQL, execute primeiro:</strong><br>";
echo "   <a href='corrigir_replicacao_rapido.php' target='_blank'>Correção Rápida MySQL</a></li>";
echo "<li><strong>Teste acessando:</strong><br>";
echo "   - <a href='http://localhost' target='_blank'>http://localhost</a><br>";
echo "   - <a href='http://localhost:8080' target='_blank'>http://localhost:8080</a></li>";
echo "</ol>";

echo "<h3>💡 COMANDOS ÚTEIS PARA CMD</h3>";
echo "<code>\"C:\\xampp\\apache\\bin\\httpd.exe\" -k start</code> - Iniciar Apache<br>";
echo "<code>\"C:\\xampp\\mysql\\bin\\mysqld.exe\" --defaults-file=\"C:\\xampp\\mysql\\bin\\my.ini\" --standalone</code> - Iniciar MySQL<br>";
echo "<code>netstat -an | find \":80\"</code> - Verificar porta 80<br>";
echo "<code>netstat -an | find \":3306\"</code> - Verificar porta 3306<br>";

echo "<hr>";
echo "<p><strong>🎯 Próximos passos:</strong></p>";
echo "<p>1. Execute este script para verificar o status</p>";
echo "<p>2. Se MySQL não funcionar, execute: <a href='corrigir_replicacao_rapido.php' target='_blank'>Correção Rápida MySQL</a></p>";
echo "<p>3. Teste acessando: <a href='http://localhost' target='_blank'>http://localhost</a></p>";
?>


