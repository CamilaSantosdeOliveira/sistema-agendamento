<?php
echo "<h1>🔧 CORREÇÃO AUTOMÁTICA - MYSQL XAMPP</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .erro { color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .sucesso { color: green; background: #e6ffe6; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: blue; background: #e6f3ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .aviso { color: orange; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>";

// Função para executar comandos
function executarComando($comando) {
    $output = [];
    $return_var = 0;
    exec($comando . " 2>&1", $output, $return_var);
    return ['output' => $output, 'return' => $return_var];
}

echo "<h2>🔍 DIAGNÓSTICO INICIAL</h2>";

// 1. Verificar se a porta 3306 está em uso
echo "<h3>1️⃣ Verificando porta 3306...</h3>";
$resultado = executarComando("netstat -an | findstr :3306");
if (!empty($resultado['output'])) {
    echo "<div class='erro'>";
    echo "❌ Porta 3306 está em uso!<br>";
    echo "<pre>" . implode("\n", $resultado['output']) . "</pre>";
    echo "</div>";
    
    // Tentar identificar o processo
    $resultado_pid = executarComando("netstat -ano | findstr :3306");
    if (!empty($resultado_pid['output'])) {
        echo "<div class='info'>";
        echo "🔍 Processos usando a porta 3306:<br>";
        echo "<pre>" . implode("\n", $resultado_pid['output']) . "</pre>";
        echo "</div>";
    }
} else {
    echo "<div class='sucesso'>✅ Porta 3306 está livre</div>";
}

// 2. Verificar processos MySQL
echo "<h3>2️⃣ Verificando processos MySQL...</h3>";
$resultado = executarComando("tasklist | findstr mysql");
if (!empty($resultado['output'])) {
    echo "<div class='aviso'>";
    echo "⚠️ Processos MySQL encontrados:<br>";
    echo "<pre>" . implode("\n", $resultado['output']) . "</pre>";
    echo "</div>";
} else {
    echo "<div class='sucesso'>✅ Nenhum processo MySQL encontrado</div>";
}

// 3. Verificar serviços MySQL
echo "<h3>3️⃣ Verificando serviços MySQL...</h3>";
$resultado = executarComando("sc query mysql");
if (!empty($resultado['output'])) {
    echo "<div class='info'>";
    echo "🔍 Status do serviço MySQL:<br>";
    echo "<pre>" . implode("\n", $resultado['output']) . "</pre>";
    echo "</div>";
} else {
    echo "<div class='info'>ℹ️ Serviço MySQL não encontrado (normal para XAMPP)</div>";
}

echo "<h2>🔧 TENTATIVAS DE CORREÇÃO</h2>";

// 4. Tentar encerrar processos MySQL conflitantes
echo "<h3>4️⃣ Encerrando processos MySQL conflitantes...</h3>";
$resultado = executarComando("taskkill /F /IM mysqld.exe 2>nul");
if ($resultado['return'] == 0) {
    echo "<div class='sucesso'>✅ Processos MySQL encerrados</div>";
} else {
    echo "<div class='info'>ℹ️ Nenhum processo MySQL para encerrar</div>";
}

// 5. Verificar arquivos de log
echo "<h3>5️⃣ Verificando arquivos de log...</h3>";
$log_files = [
    'C:\\xampp\\mysql\\data\\mysql_error.log',
    'C:\\xampp\\xampp-control.log'
];

foreach ($log_files as $log_file) {
    if (file_exists($log_file)) {
        echo "<div class='info'>";
        echo "📁 Arquivo encontrado: $log_file<br>";
        $content = file_get_contents($log_file);
        if (strlen($content) > 1000) {
            $content = substr($content, -1000); // Últimos 1000 caracteres
        }
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
        echo "</div>";
    } else {
        echo "<div class='aviso'>⚠️ Arquivo não encontrado: $log_file</div>";
    }
}

// 6. Verificar diretório de dados
echo "<h3>6️⃣ Verificando diretório de dados...</h3>";
$data_dir = 'C:\\xampp\\mysql\\data';
if (is_dir($data_dir)) {
    echo "<div class='sucesso'>✅ Diretório de dados existe: $data_dir</div>";
    
    // Verificar arquivos importantes
    $important_files = ['ibdata1', 'ib_logfile0', 'ib_logfile1'];
    foreach ($important_files as $file) {
        $file_path = $data_dir . '\\' . $file;
        if (file_exists($file_path)) {
            echo "<div class='sucesso'>✅ Arquivo encontrado: $file</div>";
        } else {
            echo "<div class='erro'>❌ Arquivo ausente: $file</div>";
        }
    }
} else {
    echo "<div class='erro'>❌ Diretório de dados não encontrado: $data_dir</div>";
}

echo "<h2>📋 SOLUÇÕES MANUAIS</h2>";

echo "<div class='info'>";
echo "<h3>🔄 SOLUÇÃO 1: Reiniciar XAMPP</h3>";
echo "<ol>";
echo "<li>Feche o XAMPP Control Panel</li>";
echo "<li>Abra o Gerenciador de Tarefas (Ctrl+Shift+Esc)</li>";
echo "<li>Encerre todos os processos relacionados ao MySQL</li>";
echo "<li>Abra o XAMPP Control Panel como administrador</li>";
echo "<li>Tente iniciar o MySQL novamente</li>";
echo "</ol>";
echo "</div>";

echo "<div class='info'>";
echo "<h3>🔧 SOLUÇÃO 2: Verificar Configuração my.ini</h3>";
echo "<ol>";
echo "<li>No XAMPP Control Panel, clique em <strong>Config</strong> ao lado do MySQL</li>";
echo "<li>Selecione <strong>my.ini</strong></li>";
echo "<li>Verifique estas configurações:</li>";
echo "<pre>";
echo "[mysqld]\n";
echo "port=3306\n";
echo "datadir=C:/xampp/mysql/data\n";
echo "socket=mysql\n";
echo "</pre>";
echo "</ol>";
echo "</div>";

echo "<div class='info'>";
echo "<h3>🗂️ SOLUÇÃO 3: Backup e Reinstalação</h3>";
echo "<ol>";
echo "<li>Faça backup da pasta <strong>C:\\xampp\\mysql\\data</strong></li>";
echo "<li>Delete a pasta <strong>C:\\xampp\\mysql</strong></li>";
echo "<li>Baixe o XAMPP novamente</li>";
echo "<li>Extraia apenas a pasta mysql</li>";
echo "<li>Restaure seus dados</li>";
echo "</ol>";
echo "</div>";

echo "<div class='info'>";
echo "<h3>🚫 SOLUÇÃO 4: Mudar Porta</h3>";
echo "<ol>";
echo "<li>Edite o arquivo <strong>C:\\xampp\\mysql\\bin\\my.ini</strong></li>";
echo "<li>Mude a linha <strong>port=3306</strong> para <strong>port=3307</strong></li>";
echo "<li>Reinicie o XAMPP</li>";
echo "<li>Atualize a configuração do seu sistema</li>";
echo "</ol>";
echo "</div>";

// 7. Teste de conexão final
echo "<h2>🧪 TESTE DE CONEXÃO</h2>";
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
    echo "<h3>❌ MYSQL AINDA NÃO ESTÁ FUNCIONANDO</h3>";
    echo "<p>Execute as soluções manuais acima ou reinstale o XAMPP.</p>";
    echo "</div>";
} else {
    echo "<div class='sucesso'>";
    echo "<h3>✅ MYSQL ESTÁ FUNCIONANDO!</h3>";
    echo "<p>Seu sistema está pronto para uso!</p>";
    echo "</div>";
}

echo "<h2>📞 PRÓXIMOS PASSOS</h2>";
echo "<div class='info'>";
echo "<ol>";
echo "<li>Se o MySQL ainda não funcionar, siga as soluções manuais</li>";
echo "<li>Verifique os arquivos de log para mais detalhes</li>";
echo "<li>Considere reinstalar o XAMPP se necessário</li>";
echo "<li>Teste a conexão novamente após cada tentativa</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🔗 LINKS ÚTEIS</h2>";
echo "<div class='info'>";
echo "<ul>";
echo "<li><a href='diagnostico_mysql.php'>🔍 Diagnóstico Completo</a></li>";
echo "<li><a href='verificar_mysql.php'>✅ Verificar MySQL</a></li>";
echo "<li><a href='http://localhost/phpmyadmin' target='_blank'>🌐 phpMyAdmin</a></li>";
echo "<li><a href='http://localhost:8080/Sistema%20De%20Agendamento/public/'>🏠 Sistema Principal</a></li>";
echo "</ul>";
echo "</div>";
?>


