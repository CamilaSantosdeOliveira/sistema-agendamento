<?php
echo "<h1>🔧 DIAGNÓSTICO COMPLETO - MYSQL XAMPP</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .erro { color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .sucesso { color: green; background: #e6ffe6; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: blue; background: #e6f3ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .solucao { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>";

// 1. Verificar se a porta 3306 está em uso
echo "<h2>1️⃣ VERIFICANDO PORTA 3306</h2>";
$porta_3306_em_uso = false;

// Simular verificação de porta (em produção, usar exec())
echo "<div class='info'>";
echo "🔍 Verificando se a porta 3306 está em uso...<br>";
echo "💡 Execute no CMD como administrador:<br>";
echo "<pre>netstat -an | findstr :3306</pre>";
echo "</div>";

// 2. Verificar processos MySQL
echo "<h2>2️⃣ VERIFICANDO PROCESSOS MYSQL</h2>";
echo "<div class='info'>";
echo "🔍 Verificando processos MySQL em execução...<br>";
echo "💡 Execute no CMD:<br>";
echo "<pre>tasklist | findstr mysql</pre>";
echo "</div>";

// 3. Verificar serviços Windows
echo "<h2>3️⃣ VERIFICANDO SERVIÇOS WINDOWS</h2>";
echo "<div class='info'>";
echo "🔍 Verificando serviços MySQL no Windows...<br>";
echo "💡 Execute no CMD como administrador:<br>";
echo "<pre>sc query mysql</pre>";
echo "</div>";

// 4. Verificar arquivos de log do XAMPP
echo "<h2>4️⃣ ARQUIVOS DE LOG IMPORTANTES</h2>";
echo "<div class='solucao'>";
echo "<strong>📁 Verifique estes arquivos de log:</strong><br>";
echo "<ul>";
echo "<li><strong>XAMPP MySQL Log:</strong> C:\\xampp\\mysql\\data\\mysql_error.log</li>";
echo "<li><strong>XAMPP Control Log:</strong> C:\\xampp\\xampp-control.log</li>";
echo "<li><strong>Windows Event Viewer:</strong> Aplicativos → MySQL</li>";
echo "</ul>";
echo "</div>";

// 5. Soluções passo a passo
echo "<h2>5️⃣ SOLUÇÕES PASSO A PASSO</h2>";

echo "<div class='solucao'>";
echo "<h3>🔄 SOLUÇÃO 1: Reiniciar XAMPP</h3>";
echo "<ol>";
echo "<li>Feche o XAMPP Control Panel</li>";
echo "<li>Abra o Gerenciador de Tarefas (Ctrl+Shift+Esc)</li>";
echo "<li>Encerre todos os processos relacionados ao MySQL</li>";
echo "<li>Abra o XAMPP Control Panel como administrador</li>";
echo "<li>Tente iniciar o MySQL novamente</li>";
echo "</ol>";
echo "</div>";

echo "<div class='solucao'>";
echo "<h3>🔧 SOLUÇÃO 2: Verificar Configuração</h3>";
echo "<ol>";
echo "<li>No XAMPP Control Panel, clique em <strong>Config</strong> ao lado do MySQL</li>";
echo "<li>Selecione <strong>my.ini</strong></li>";
echo "<li>Verifique se a porta está configurada como 3306</li>";
echo "<li>Verifique se o caminho dos dados está correto</li>";
echo "</ol>";
echo "</div>";

echo "<div class='solucao'>";
echo "<h3>🗂️ SOLUÇÃO 3: Verificar Diretório de Dados</h3>";
echo "<ol>";
echo "<li>Vá para <strong>C:\\xampp\\mysql\\data</strong></li>";
echo "<li>Verifique se existe o arquivo <strong>ibdata1</strong></li>";
echo "<li>Se não existir, pode haver corrupção de dados</li>";
echo "<li>Faça backup e reinstale o MySQL</li>";
echo "</ol>";
echo "</div>";

echo "<div class='solucao'>";
echo "<h3>🚫 SOLUÇÃO 4: Conflito de Porta</h3>";
echo "<ol>";
echo "<li>Execute no CMD como administrador:</li>";
echo "<pre>netstat -an | findstr :3306</pre>";
echo "<li>Se a porta estiver em uso, identifique o processo:</li>";
echo "<pre>netstat -ano | findstr :3306</pre>";
echo "<li>Encerre o processo conflitante ou mude a porta do MySQL</li>";
echo "</ol>";
echo "</div>";

echo "<div class='solucao'>";
echo "<h3>🔄 SOLUÇÃO 5: Reinstalar MySQL (Último Recurso)</h3>";
echo "<ol>";
echo "<li>Faça backup dos seus bancos de dados</li>";
echo "<li>Desinstale o MySQL do XAMPP</li>";
echo "<li>Delete a pasta <strong>C:\\xampp\\mysql</strong></li>";
echo "<li>Reinstale o XAMPP</li>";
echo "<li>Restaure seus backups</li>";
echo "</ol>";
echo "</div>";

// 6. Comandos úteis
echo "<h2>6️⃣ COMANDOS ÚTEIS PARA EXECUTAR</h2>";
echo "<div class='info'>";
echo "<strong>Execute estes comandos no CMD como administrador:</strong><br>";
echo "<pre>";
echo "# Verificar porta 3306\n";
echo "netstat -an | findstr :3306\n\n";
echo "# Verificar processos MySQL\n";
echo "tasklist | findstr mysql\n\n";
echo "# Verificar serviços\n";
echo "sc query mysql\n\n";
echo "# Encerrar processo por PID (substitua XXXX pelo PID)\n";
echo "taskkill /PID XXXX /F\n\n";
echo "# Verificar arquivos de log\n";
echo "type C:\\xampp\\mysql\\data\\mysql_error.log\n";
echo "</pre>";
echo "</div>";

// 7. Verificação automática de conexão
echo "<h2>7️⃣ TESTE DE CONEXÃO</h2>";
$conexoes_testadas = [
    ['localhost', 'root', '', 3306],
    ['127.0.0.1', 'root', '', 3306],
    ['localhost', 'root', 'root', 3306],
    ['127.0.0.1', 'root', 'root', 3306],
];

$mysql_funcionando = false;

foreach ($conexoes_testadas as $config) {
    list($host, $user, $pass, $port) = $config;
    
    $senha_display = $pass ? $pass : '(sem senha)';
    echo "<h4>🔍 Testando: $host:$port - $user - $senha_display</h4>";
    
    try {
        $conn = new mysqli($host, $user, $pass, '', $port);
        
        if ($conn->connect_error) {
            echo "<div class='erro'>❌ Falhou: " . $conn->connect_error . "</div>";
        } else {
            echo "<div class='sucesso'>✅ SUCESSO! MySQL está funcionando!</div>";
            $mysql_funcionando = true;
            break;
        }
    } catch (Exception $e) {
        echo "<div class='erro'>❌ Erro: " . $e->getMessage() . "</div>";
    }
}

if (!$mysql_funcionando) {
    echo "<div class='erro'>";
    echo "<h3>❌ MYSQL NÃO ESTÁ FUNCIONANDO</h3>";
    echo "<p>Siga as soluções acima para resolver o problema.</p>";
    echo "</div>";
} else {
    echo "<div class='sucesso'>";
    echo "<h3>✅ MYSQL ESTÁ FUNCIONANDO!</h3>";
    echo "<p>Seu sistema está pronto para uso!</p>";
    echo "</div>";
}

// 8. Links úteis
echo "<h2>8️⃣ LINKS ÚTEIS</h2>";
echo "<div class='info'>";
echo "<ul>";
echo "<li><a href='verificar_mysql.php' target='_blank'>🔍 Verificar MySQL</a></li>";
echo "<li><a href='verificar_bancos.php' target='_blank'>🗄️ Verificar Bancos</a></li>";
echo "<li><a href='configurar_banco.php' target='_blank'>⚙️ Configurar Banco</a></li>";
echo "<li><a href='http://localhost/phpmyadmin' target='_blank'>🌐 phpMyAdmin</a></li>";
echo "</ul>";
echo "</div>";

echo "<h2>📞 PRÓXIMOS PASSOS</h2>";
echo "<div class='solucao'>";
echo "<ol>";
echo "<li>Execute os comandos de verificação acima</li>";
echo "<li>Siga as soluções passo a passo</li>";
echo "<li>Verifique os arquivos de log</li>";
echo "<li>Se necessário, reinstale o MySQL</li>";
echo "<li>Teste a conexão novamente</li>";
echo "</ol>";
echo "</div>";
?>


