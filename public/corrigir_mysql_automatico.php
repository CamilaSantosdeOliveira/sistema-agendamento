<?php
echo "<h1>🔧 CORREÇÃO AUTOMÁTICA AVANÇADA - MYSQL XAMPP</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .erro { color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .sucesso { color: green; background: #e6ffe6; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: blue; background: #e6f3ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .aviso { color: orange; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .executando { color: purple; background: #f0e6ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
    .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
</style>";

// Função para executar comandos
function executarComando($comando) {
    $output = [];
    $return_var = 0;
    exec($comando . " 2>&1", $output, $return_var);
    return ['output' => $output, 'return' => $return_var];
}

// Função para verificar se o MySQL está funcionando
function testarMySQL() {
    $conexoes_testadas = [
        ['localhost', 'root', '', 3306],
        ['127.0.0.1', 'root', '', 3306],
    ];
    
    foreach ($conexoes_testadas as $config) {
        list($host, $user, $pass, $port) = $config;
        try {
            $conn = new mysqli($host, $user, $pass, '', $port);
            if (!$conn->connect_error) {
                return true;
            }
        } catch (Exception $e) {
            continue;
        }
    }
    return false;
}

echo "<h2>🚀 INICIANDO CORREÇÃO AUTOMÁTICA</h2>";

// PASSO 1: Verificar se a porta 3306 está em uso
echo "<h3>1️⃣ Verificando porta 3306...</h3>";
$resultado = executarComando("netstat -an | findstr :3306");
if (!empty($resultado['output'])) {
    echo "<div class='erro'>";
    echo "❌ Porta 3306 está em uso!<br>";
    echo "<pre>" . implode("\n", $resultado['output']) . "</pre>";
    echo "</div>";
    
    // Identificar o processo
    $resultado_pid = executarComando("netstat -ano | findstr :3306");
    if (!empty($resultado_pid['output'])) {
        echo "<div class='info'>";
        echo "🔍 Processos usando a porta 3306:<br>";
        echo "<pre>" . implode("\n", $resultado_pid['output']) . "</pre>";
        echo "</div>";
        
        // Tentar encerrar processos MySQL
        echo "<div class='executando'>🔄 Tentando encerrar processos MySQL...</div>";
        $resultado_kill = executarComando("taskkill /F /IM mysqld.exe");
        if ($resultado_kill['return'] == 0) {
            echo "<div class='sucesso'>✅ Processos MySQL encerrados com sucesso!</div>";
        } else {
            echo "<div class='aviso'>⚠️ Nenhum processo MySQL para encerrar</div>";
        }
    }
} else {
    echo "<div class='sucesso'>✅ Porta 3306 está livre</div>";
}

// PASSO 2: Verificar processos MySQL
echo "<h3>2️⃣ Verificando processos MySQL...</h3>";
$resultado = executarComando("tasklist | findstr mysql");
if (!empty($resultado['output'])) {
    echo "<div class='aviso'>";
    echo "⚠️ Processos MySQL encontrados:<br>";
    echo "<pre>" . implode("\n", $resultado['output']) . "</pre>";
    echo "</div>";
    
    // Encerrar todos os processos MySQL
    echo "<div class='executando'>🔄 Encerrando todos os processos MySQL...</div>";
    $resultado_kill = executarComando("taskkill /F /IM mysqld.exe");
    $resultado_kill2 = executarComando("taskkill /F /IM mysql.exe");
    
    if ($resultado_kill['return'] == 0 || $resultado_kill2['return'] == 0) {
        echo "<div class='sucesso'>✅ Processos MySQL encerrados!</div>";
    } else {
        echo "<div class='info'>ℹ️ Nenhum processo MySQL para encerrar</div>";
    }
} else {
    echo "<div class='sucesso'>✅ Nenhum processo MySQL encontrado</div>";
}

// PASSO 3: Verificar diretório de dados
echo "<h3>3️⃣ Verificando diretório de dados...</h3>";
$data_dir = 'C:\\xampp\\mysql\\data';
if (is_dir($data_dir)) {
    echo "<div class='sucesso'>✅ Diretório de dados existe: $data_dir</div>";
    
    // Verificar arquivos importantes
    $important_files = ['ibdata1', 'ib_logfile0', 'ib_logfile1'];
    $files_missing = [];
    foreach ($important_files as $file) {
        $file_path = $data_dir . '\\' . $file;
        if (file_exists($file_path)) {
            echo "<div class='sucesso'>✅ Arquivo encontrado: $file</div>";
        } else {
            echo "<div class='erro'>❌ Arquivo ausente: $file</div>";
            $files_missing[] = $file;
        }
    }
    
    if (!empty($files_missing)) {
        echo "<div class='erro'>";
        echo "❌ Arquivos importantes ausentes! Isso pode indicar corrupção de dados.<br>";
        echo "Arquivos ausentes: " . implode(', ', $files_missing);
        echo "</div>";
    }
} else {
    echo "<div class='erro'>❌ Diretório de dados não encontrado: $data_dir</div>";
}

// PASSO 4: Tentar iniciar o MySQL via linha de comando
echo "<h3>4️⃣ Tentando iniciar MySQL...</h3>";
echo "<div class='executando'>🔄 Tentando iniciar MySQL via linha de comando...</div>";

// Tentar iniciar MySQL
$mysql_bin = 'C:\\xampp\\mysql\\bin\\mysqld.exe';
if (file_exists($mysql_bin)) {
    echo "<div class='sucesso'>✅ Executável MySQL encontrado</div>";
    
    // Tentar iniciar em background
    $resultado_start = executarComando("start /B \"\" \"$mysql_bin\" --defaults-file=\"C:\\xampp\\mysql\\bin\\my.ini\"");
    
    // Aguardar um pouco
    sleep(3);
    
    // Verificar se iniciou
    if (testarMySQL()) {
        echo "<div class='sucesso'>✅ MySQL iniciado com sucesso!</div>";
    } else {
        echo "<div class='erro'>❌ Falha ao iniciar MySQL via linha de comando</div>";
    }
} else {
    echo "<div class='erro'>❌ Executável MySQL não encontrado: $mysql_bin</div>";
}

// PASSO 5: Verificar arquivos de log
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
        if (strlen($content) > 500) {
            $content = substr($content, -500); // Últimos 500 caracteres
        }
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
        echo "</div>";
    } else {
        echo "<div class='aviso'>⚠️ Arquivo não encontrado: $log_file</div>";
    }
}

// PASSO 6: Teste final
echo "<h3>6️⃣ Teste final de conexão...</h3>";
if (testarMySQL()) {
    echo "<div class='sucesso'>";
    echo "<h3>🎉 SUCESSO! MYSQL ESTÁ FUNCIONANDO!</h3>";
    echo "<p>O MySQL foi corrigido e está funcionando corretamente.</p>";
    echo "</div>";
} else {
    echo "<div class='erro'>";
    echo "<h3>❌ MYSQL AINDA NÃO ESTÁ FUNCIONANDO</h3>";
    echo "<p>O MySQL ainda não está funcionando. Execute as soluções manuais abaixo.</p>";
    echo "</div>";
}

// SOLUÇÕES MANUAIS
echo "<h2>📋 SOLUÇÕES MANUAIS (se a correção automática falhou)</h2>";

echo "<div class='info'>";
echo "<h3>🔄 SOLUÇÃO 1: Reiniciar XAMPP Manualmente</h3>";
echo "<ol>";
echo "<li>Feche completamente o XAMPP Control Panel</li>";
echo "<li>Abra o Gerenciador de Tarefas (Ctrl+Shift+Esc)</li>";
echo "<li>Encerre TODOS os processos relacionados ao MySQL</li>";
echo "<li>Abra o XAMPP Control Panel como administrador</li>";
echo "<li>Tente iniciar o MySQL novamente</li>";
echo "</ol>";
echo "</div>";

echo "<div class='info'>";
echo "<h3>🔧 SOLUÇÃO 2: Verificar Configuração my.ini</h3>";
echo "<ol>";
echo "<li>No XAMPP Control Panel, clique em <strong>Config</strong> ao lado do MySQL</li>";
echo "<li>Selecione <strong>my.ini</strong></li>";
echo "<li>Verifique se estas configurações estão corretas:</li>";
echo "<pre>";
echo "[mysqld]\n";
echo "port=3306\n";
echo "datadir=C:/xampp/mysql/data\n";
echo "socket=mysql\n";
echo "max_allowed_packet=16M\n";
echo "innodb_force_recovery=0\n";
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

// COMANDOS ÚTEIS
echo "<h2>💻 COMANDOS ÚTEIS PARA EXECUTAR MANUALMENTE</h2>";
echo "<div class='info'>";
echo "<strong>Execute estes comandos no CMD como administrador:</strong><br>";
echo "<pre>";
echo "# Verificar porta 3306\n";
echo "netstat -an | findstr :3306\n\n";
echo "# Verificar processos MySQL\n";
echo "tasklist | findstr mysql\n\n";
echo "# Encerrar todos os processos MySQL\n";
echo "taskkill /F /IM mysqld.exe\n";
echo "taskkill /F /IM mysql.exe\n\n";
echo "# Verificar arquivos de log\n";
echo "type C:\\xampp\\mysql\\data\\mysql_error.log\n\n";
echo "# Tentar iniciar MySQL manualmente\n";
echo "cd C:\\xampp\\mysql\\bin\n";
echo "mysqld.exe --defaults-file=my.ini\n";
echo "</pre>";
echo "</div>";

// LINKS ÚTEIS
echo "<h2>🔗 FERRAMENTAS ÚTEIS</h2>";
echo "<div class='info'>";
echo "<a href='diagnostico_mysql.php' class='btn'>🔍 Diagnóstico Completo</a>";
echo "<a href='verificar_mysql.php' class='btn'>✅ Verificar MySQL</a>";
echo "<a href='http://localhost/phpmyadmin' target='_blank' class='btn'>🌐 phpMyAdmin</a>";
echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn'>🏠 Sistema Principal</a>";
echo "</div>";

echo "<h2>📞 PRÓXIMOS PASSOS</h2>";
echo "<div class='info'>";
echo "<ol>";
echo "<li>Se o MySQL ainda não funcionar, siga as soluções manuais</li>";
echo "<li>Verifique os arquivos de log para mais detalhes</li>";
echo "<li>Considere reinstalar o XAMPP se necessário</li>";
echo "<li>Teste a conexão novamente após cada tentativa</li>";
echo "</ol>";
echo "</div>";
?>


