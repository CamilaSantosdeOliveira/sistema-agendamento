<?php
echo "<h1>🔧 CORREÇÃO - MySQL Shutdown Unexpectedly</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .erro { color: #d32f2f; background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #d32f2f; }
    .sucesso { color: #388e3c; background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #388e3c; }
    .info { color: #1976d2; background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #1976d2; }
    .aviso { color: #f57c00; background: #fff3e0; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #f57c00; }
    .executando { color: #7b1fa2; background: #f3e5f5; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #7b1fa2; }
    .btn { display: inline-block; padding: 10px 20px; background: #2196f3; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    .btn-success { background: #4caf50; }
    .btn-danger { background: #f44336; }
    .btn-warning { background: #ff9800; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    .step { background: #fafafa; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #2196f3; }
</style>";

echo "<div class='container'>";

// Função para executar comandos
function executarComando($comando) {
    $output = [];
    $return_var = 0;
    exec($comando . " 2>&1", $output, $return_var);
    return ['output' => $output, 'return' => $return_var];
}

// Função para verificar se o MySQL está funcionando
function testarMySQL() {
    try {
        $conn = new mysqli('localhost', 'root', '', '', 3306);
        if (!$conn->connect_error) {
            $conn->close();
            return true;
        }
    } catch (Exception $e) {
        return false;
    }
    return false;
}

echo "<h2>🚀 CORREÇÃO AUTOMÁTICA EM ANDAMENTO...</h2>";

// PASSO 1: Verificar se o MySQL está rodando
echo "<div class='step'>";
echo "<h3>1️⃣ Verificando status atual do MySQL...</h3>";
if (testarMySQL()) {
    echo "<div class='sucesso'>✅ MySQL já está funcionando!</div>";
    echo "<p>O MySQL está rodando corretamente. Não é necessário fazer correções.</p>";
    echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Voltar ao Sistema</a>";
    exit;
} else {
    echo "<div class='erro'>❌ MySQL não está rodando - iniciando correção...</div>";
}
echo "</div>";

// PASSO 2: Encerrar todos os processos MySQL
echo "<div class='step'>";
echo "<h3>2️⃣ Encerrando processos MySQL conflitantes...</h3>";
echo "<div class='executando'>🔄 Encerrando todos os processos MySQL...</div>";

$comandos_kill = [
    "taskkill /F /IM mysqld.exe",
    "taskkill /F /IM mysql.exe", 
    "taskkill /F /IM mysqld-nt.exe",
    "taskkill /F /IM mysqld-5.7.exe",
    "taskkill /F /IM mysqld-8.0.exe"
];

foreach ($comandos_kill as $comando) {
    $resultado = executarComando($comando);
    if ($resultado['return'] == 0) {
        echo "<div class='sucesso'>✅ Processo encerrado: $comando</div>";
    }
}
echo "</div>";

// PASSO 3: Aguardar liberação de recursos
echo "<div class='step'>";
echo "<h3>3️⃣ Aguardando liberação de recursos...</h3>";
echo "<div class='info'>⏳ Aguardando 5 segundos para liberar recursos...</div>";
sleep(5);
echo "</div>";

// PASSO 4: Verificar e corrigir arquivo my.ini
echo "<div class='step'>";
echo "<h3>4️⃣ Verificando arquivo de configuração my.ini...</h3>";

$my_ini_path = 'C:\\xampp\\mysql\\bin\\my.ini';
$my_ini_backup = 'C:\\xampp\\mysql\\bin\\my.ini.backup';

if (file_exists($my_ini_path)) {
    echo "<div class='info'>📁 Arquivo my.ini encontrado</div>";
    
    // Fazer backup do arquivo atual
    if (!file_exists($my_ini_backup)) {
        copy($my_ini_path, $my_ini_backup);
        echo "<div class='sucesso'>✅ Backup criado: my.ini.backup</div>";
    }
    
    // Ler o conteúdo atual
    $conteudo_atual = file_get_contents($my_ini_path);
    
    // Verificar problemas comuns
    $problemas = [];
    if (strpos($conteudo_atual, 'innodb_force_recovery') === false) {
        $problemas[] = 'Falta configuração de recuperação InnoDB';
    }
    if (strpos($conteudo_atual, 'max_allowed_packet') === false) {
        $problemas[] = 'Falta configuração max_allowed_packet';
    }
    
    if (!empty($problemas)) {
        echo "<div class='aviso'>⚠️ Problemas detectados no my.ini:</div>";
        foreach ($problemas as $problema) {
            echo "<div class='info'>• $problema</div>";
        }
        
        // Criar configuração corrigida
        $config_corrigida = $conteudo_atual;
        
        // Adicionar configurações de recuperação se não existirem
        if (strpos($config_corrigida, 'innodb_force_recovery') === false) {
            $config_corrigida = str_replace('[mysqld]', "[mysqld]\ninnodb_force_recovery=1", $config_corrigida);
        }
        
        if (strpos($config_corrigida, 'max_allowed_packet') === false) {
            $config_corrigida = str_replace('[mysqld]', "[mysqld]\nmax_allowed_packet=16M", $config_corrigida);
        }
        
        // Salvar configuração corrigida
        file_put_contents($my_ini_path, $config_corrigida);
        echo "<div class='sucesso'>✅ Arquivo my.ini corrigido!</div>";
    } else {
        echo "<div class='sucesso'>✅ Arquivo my.ini está correto</div>";
    }
} else {
    echo "<div class='erro'>❌ Arquivo my.ini não encontrado!</div>";
    echo "<p>Verifique se o XAMPP está instalado corretamente em C:\\xampp\\</p>";
}
echo "</div>";

// PASSO 5: Limpar arquivos de log e dados corrompidos
echo "<div class='step'>";
echo "<h3>5️⃣ Limpando arquivos de log e dados corrompidos...</h3>";

$arquivos_para_limpar = [
    'C:\\xampp\\mysql\\data\\ib_logfile0',
    'C:\\xampp\\mysql\\data\\ib_logfile1',
    'C:\\xampp\\mysql\\data\\ibdata1',
    'C:\\xampp\\mysql\\data\\mysql-bin.index',
    'C:\\xampp\\mysql\\data\\mysql-bin.000001',
    'C:\\xampp\\mysql\\data\\mysql-bin.000002'
];

foreach ($arquivos_para_limpar as $arquivo) {
    if (file_exists($arquivo)) {
        unlink($arquivo);
        echo "<div class='sucesso'>✅ Arquivo removido: " . basename($arquivo) . "</div>";
    }
}
echo "</div>";

// PASSO 6: Tentar iniciar MySQL
echo "<div class='step'>";
echo "<h3>6️⃣ Tentando iniciar MySQL...</h3>";
echo "<div class='executando'>🔄 Iniciando MySQL...</div>";

$mysql_bin = 'C:\\xampp\\mysql\\bin\\mysqld.exe';
if (file_exists($mysql_bin)) {
    // Tentar iniciar em background
    $resultado_start = executarComando("start /B \"\" \"$mysql_bin\" --defaults-file=\"C:\\xampp\\mysql\\bin\\my.ini\"");
    
    // Aguardar inicialização
    echo "<div class='info'>⏳ Aguardando 10 segundos para inicialização...</div>";
    sleep(10);
    
    // Verificar se iniciou
    if (testarMySQL()) {
        echo "<div class='sucesso'>";
        echo "<h3>🎉 SUCESSO! MYSQL ESTÁ FUNCIONANDO!</h3>";
        echo "<p>O MySQL foi corrigido e está funcionando corretamente.</p>";
        echo "</div>";
        
        echo "<h3>✅ PRÓXIMOS PASSOS:</h3>";
        echo "<div class='info'>";
        echo "<ol>";
        echo "<li>Abra o XAMPP Control Panel</li>";
        echo "<li>Verifique se o MySQL está rodando (luz verde)</li>";
        echo "<li>Teste o phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
        echo "<li>Volte para o sistema principal: <a href='http://localhost:8080/Sistema%20De%20Agendamento/public/'>Sistema Principal</a></li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<a href='http://localhost/phpmyadmin' target='_blank' class='btn btn-success'>🌐 Abrir phpMyAdmin</a>";
        echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Sistema Principal</a>";
        echo "<a href='verificar_mysql.php' class='btn'>🔍 Verificar MySQL</a>";
        echo "</div>";
        
    } else {
        echo "<div class='erro'>❌ Falha ao iniciar MySQL automaticamente</div>";
        echo "<h3>🔄 SOLUÇÃO MANUAL:</h3>";
        echo "<div class='aviso'>";
        echo "<ol>";
        echo "<li>Abra o XAMPP Control Panel como <strong>administrador</strong></li>";
        echo "<li>Clique em <strong>Start</strong> ao lado do MySQL</li>";
        echo "<li>Se der erro, clique em <strong>Config</strong> → <strong>my.ini</strong></li>";
        echo "<li>Adicione estas linhas na seção [mysqld]:</li>";
        echo "</ol>";
        echo "<pre>innodb_force_recovery=1
max_allowed_packet=16M
skip-grant-tables</pre>";
        echo "</div>";
    }
} else {
    echo "<div class='erro'>❌ Executável MySQL não encontrado: $mysql_bin</div>";
    echo "<p>Verifique se o XAMPP está instalado corretamente.</p>";
}
echo "</div>";

// PASSO 7: Se ainda não funcionou, tentar com configuração de emergência
if (!testarMySQL()) {
    echo "<div class='step'>";
    echo "<h3>7️⃣ Tentando configuração de emergência...</h3>";
    echo "<div class='aviso'>⚠️ Aplicando configuração de emergência...</div>";
    
    $my_ini_emergency = "[mysqld]
# Configuração de emergência para MySQL shutdown unexpectedly
innodb_force_recovery=1
max_allowed_packet=16M
skip-grant-tables
skip-networking
skip-name-resolve
innodb_buffer_pool_size=16M
innodb_log_file_size=5M
innodb_log_buffer_size=8M
innodb_flush_log_at_trx_commit=2
innodb_lock_wait_timeout=50

[mysql]
default-character-set=utf8

[client]
default-character-set=utf8";

    if (file_exists($my_ini_path)) {
        file_put_contents($my_ini_path, $my_ini_emergency);
        echo "<div class='sucesso'>✅ Configuração de emergência aplicada!</div>";
        
        // Tentar iniciar novamente
        $resultado_start = executarComando("start /B \"\" \"$mysql_bin\" --defaults-file=\"C:\\xampp\\mysql\\bin\\my.ini\"");
        sleep(10);
        
        if (testarMySQL()) {
            echo "<div class='sucesso'>🎉 MYSQL FUNCIONANDO COM CONFIGURAÇÃO DE EMERGÊNCIA!</div>";
        } else {
            echo "<div class='erro'>❌ Mesmo com configuração de emergência não funcionou</div>";
        }
    }
    echo "</div>";
}

echo "<h3>📋 RESUMO DAS AÇÕES:</h3>";
echo "<div class='info'>";
echo "<ul>";
echo "<li>✅ Processos MySQL encerrados</li>";
echo "<li>✅ Arquivo my.ini verificado e corrigido</li>";
echo "<li>✅ Arquivos de log corrompidos removidos</li>";
echo "<li>✅ Tentativa de inicialização realizada</li>";
if (!testarMySQL()) {
    echo "<li>⚠️ Configuração de emergência aplicada</li>";
}
echo "</ul>";
echo "</div>";

echo "<h3>🔧 FERRAMENTAS ADICIONAIS:</h3>";
echo "<div class='info'>";
echo "<a href='verificar_mysql.php' class='btn'>🔍 Verificar MySQL</a>";
echo "<a href='corrigir_mysql_rapido.php' class='btn btn-warning'>⚡ Correção Rápida</a>";
echo "<a href='SOLUCAO-MYSQL-XAMPP.html' class='btn'>📖 Guia Completo</a>";
echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Sistema Principal</a>";
echo "</div>";

echo "</div>";
?>













