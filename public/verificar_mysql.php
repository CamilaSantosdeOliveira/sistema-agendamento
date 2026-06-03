<?php
echo "<h2>🔍 VERIFICANDO STATUS DO MYSQL</h2>";

// Verificar se conseguimos conectar
$conexoes_testadas = [
    ['localhost', 'root', '', 3306],
    ['127.0.0.1', 'root', '', 3306],
    ['localhost', 'root', 'root', 3306],
    ['127.0.0.1', 'root', 'root', 3306],
];

$mysql_rodando = false;
$configuracao_funcional = null;

foreach ($conexoes_testadas as $config) {
    list($host, $user, $pass, $port) = $config;
    
    $senha_display = $pass ? $pass : '(sem senha)';
    echo "<h3>🔍 Testando: $host:$port - $user - $senha_display</h3>";
    
    try {
        $conn = new mysqli($host, $user, $pass, '', $port);
        
        if ($conn->connect_error) {
            echo "❌ <strong>Falhou:</strong> " . $conn->connect_error . "<br>";
        } else {
            echo "✅ <strong>SUCESSO!</strong> MySQL está rodando!<br>";
            $mysql_rodando = true;
            $configuracao_funcional = $config;
            break;
        }
    } catch (Exception $e) {
        echo "❌ <strong>Erro:</strong> " . $e->getMessage() . "<br>";
    }
    
    echo "<br>";
}

if (!$mysql_rodando) {
    echo "<h2>❌ MYSQL NÃO ESTÁ RODANDO</h2>";
    
    echo "<h3>🔧 SOLUÇÕES:</h3>";
    
    echo "<h4>1️⃣ Se você usa XAMPP:</h4>";
    echo "<ol>";
    echo "<li>Abra o <strong>XAMPP Control Panel</strong></li>";
    echo "<li>Procure por <strong>MySQL</strong> na lista</li>";
    echo "<li>Clique em <strong>Start</strong> ao lado do MySQL</li>";
    echo "<li>Se der erro, clique em <strong>Config</strong> → <strong>my.ini</strong></li>";
    echo "<li>Verifique se a porta 3306 está livre</li>";
    echo "</ol>";
    
    echo "<h4>2️⃣ Se você usa MySQL Workbench:</h4>";
    echo "<ol>";
    echo "<li>Abra o <strong>MySQL Workbench</strong></li>";
    echo "<li>Vá em <strong>Server</strong> → <strong>Start/Stop Server</strong></li>";
    echo "<li>Clique em <strong>Start Server</strong></li>";
    echo "<li>Ou use <strong>Services</strong> do Windows</li>";
    echo "</ol>";
    
    echo "<h4>3️⃣ Verificar via Windows Services:</h4>";
    echo "<ol>";
    echo "<li>Pressione <strong>Win + R</strong></li>";
    echo "<li>Digite <strong>services.msc</strong></li>";
    echo "<li>Procure por <strong>MySQL</strong></li>";
    echo "<li>Clique com botão direito → <strong>Start</strong></li>";
    echo "</ol>";
    
    echo "<h4>4️⃣ Verificar portas em uso:</h4>";
    echo "<p>Execute no CMD como administrador:</p>";
    echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
    echo "netstat -an | findstr :3306\n";
    echo "tasklist | findstr mysql\n";
    echo "</pre>";
    
    echo "<h3>🔄 DEPOIS DE ATIVAR:</h3>";
    echo "<p>Recarregue esta página para testar novamente!</p>";
    
    echo "<h3>📞 AJUDA RÁPIDA:</h3>";
    echo "<ul>";
    echo "<li><strong>XAMPP:</strong>  http://localhost/phpmyadmin</li>";
    echo "<li><strong>MySQL Workbench:</strong> Verifique a conexão</li>";
    echo "<li><strong>Teste:</strong> <a href='verificar_bancos.php'>Verificar Bancos</a></li>";
    echo "</ul>";
    
} else {
    echo "<h2>✅ MYSQL ESTÁ FUNCIONANDO!</h2>";
    
    list($host, $user, $pass, $port) = $configuracao_funcional;
    
    echo "<h3>🎯 Configuração Funcional:</h3>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> $host</li>";
    echo "<li><strong>Porta:</strong> $port</li>";
    echo "<li><strong>Usuário:</strong> $user</li>";
    echo "<li><strong>Senha:</strong> " . ($pass ? $pass : '(sem senha)') . "</li>";
    echo "</ul>";
    
    echo "<h3>🔍 Próximos Passos:</h3>";
    echo "<ol>";
    echo "<li><a href='descobrir_dados.php'>Descobrir seus dados</a></li>";
    echo "<li><a href='verificar_bancos.php'>Verificar bancos</a></li>";
    echo "<li>Configurar o sistema</li>";
    echo "</ol>";
}

echo "<h3>📋 COMANDOS ÚTEIS:</h3>";
echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
echo "# Verificar se MySQL está rodando\n";
echo "netstat -an | findstr :3306\n\n";
echo "# Verificar processos MySQL\n";
echo "tasklist | findstr mysql\n\n";
echo "# Verificar serviços\n";
echo "sc query mysql\n";
echo "</pre>";
?>



