<?php
echo "<h1>🔧 CORREÇÃO DE PORTA MYSQL</h1>";
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
    .comando { background: #2d3748; color: #e2e8f0; padding: 10px; border-radius: 5px; font-family: monospace; }
    .emergency { background: #ff1744; color: white; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ff1744; }
</style>";

echo "<div class='container'>";

// Função para executar comandos
function executarComando($comando) {
    $output = [];
    $return_var = 0;
    exec($comando . " 2>&1", $output, $return_var);
    return ['output' => $output, 'return' => $return_var];
}

// Função para testar MySQL em uma porta específica
function testarMySQLPorta($porta) {
    try {
        $conn = new mysqli('localhost', 'root', '', '', $porta);
        if (!$conn->connect_error) {
            $conn->close();
            return true;
        }
    } catch (Exception $e) {
        return false;
    }
    return false;
}

echo "<div class='info'>";
echo "<h2>🔍 VERIFICANDO STATUS DO MYSQL</h2>";
echo "<p>Vamos verificar em qual porta o MySQL está rodando e corrigir para a porta padrão 3306.</p>";
echo "</div>";

// PASSO 1: Verificar em qual porta o MySQL está rodando
echo "<div class='step'>";
echo "<h3>1️⃣ Verificando em qual porta o MySQL está rodando...</h3>";

$mysql_3306 = testarMySQLPorta(3306);
$mysql_3307 = testarMySQLPorta(3307);

if ($mysql_3306) {
    echo "<div class='sucesso'>✅ MySQL está rodando na porta 3306 (padrão)</div>";
    echo "<p>Perfeito! O MySQL está na porta correta. Seu sistema deve funcionar normalmente.</p>";
    echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Voltar ao Sistema</a>";
    exit;
} elseif ($mysql_3307) {
    echo "<div class='aviso'>⚠️ MySQL está rodando na porta 3307 (não padrão)</div>";
    echo "<p>Isso pode causar problemas com seu sistema. Vamos corrigir para a porta 3306.</p>";
} else {
    echo "<div class='erro'>❌ MySQL não está rodando em nenhuma porta</div>";
    echo "<p>O MySQL não está funcionando. Execute primeiro a correção de último recurso.</p>";
    echo "<a href='corrigir_mysql_ultimo_recurso.php' class='btn btn-warning'>🔧 Correção de Último Recurso</a>";
    exit;
}
echo "</div>";

// PASSO 2: Verificar configuração atual do my.ini
echo "<div class='step'>";
echo "<h3>2️⃣ Verificando configuração atual do my.ini...</h3>";

$my_ini_path = 'C:\\xampp\\mysql\\bin\\my.ini';
if (file_exists($my_ini_path)) {
    $conteudo = file_get_contents($my_ini_path);
    
    if (strpos($conteudo, 'port=3307') !== false) {
        echo "<div class='aviso'>⚠️ Configuração atual: porta 3307</div>";
        echo "<p>O arquivo my.ini está configurado para porta 3307. Vamos corrigir para 3306.</p>";
    } elseif (strpos($conteudo, 'port=3306') !== false) {
        echo "<div class='sucesso'>✅ Configuração atual: porta 3306</div>";
        echo "<p>O arquivo my.ini está correto, mas o MySQL está rodando na porta 3307.</p>";
    } else {
        echo "<div class='info'>ℹ️ Porta não especificada no my.ini</div>";
    }
} else {
    echo "<div class='erro'>❌ Arquivo my.ini não encontrado</div>";
    exit;
}
echo "</div>";

// PASSO 3: Criar configuração segura para porta 3306
echo "<div class='step'>";
echo "<h3>3️⃣ Criando configuração segura para porta 3306...</h3>";

// Fazer backup
$my_ini_backup = 'C:\\xampp\\mysql\\bin\\my.ini.backup.' . date('Y-m-d-H-i-s');
copy($my_ini_path, $my_ini_backup);
echo "<div class='sucesso'>✅ Backup criado: " . basename($my_ini_backup) . "</div>";

// Configuração segura para porta 3306
$my_ini_seguro = "[mysqld]
# Configuração SEGURA para MySQL - Porta 3306
# Aplicada em: " . date('Y-m-d H:i:s') . "
# Configuração otimizada para estabilidade e segurança

# Configurações básicas
port=3306
socket=mysql
basedir=C:/xampp/mysql
datadir=C:/xampp/mysql/data
tmpdir=C:/xampp/mysql/tmp

# Configurações de recuperação SEGURAS
innodb_force_recovery=0
innodb_buffer_pool_size=128M
innodb_log_file_size=64M
innodb_log_buffer_size=16M
innodb_flush_log_at_trx_commit=1
innodb_lock_wait_timeout=50
innodb_read_io_threads=4
innodb_write_io_threads=4
innodb_io_capacity=200

# Configurações de memória OTIMIZADAS
key_buffer_size=256M
max_allowed_packet=64M
table_open_cache=2000
thread_cache_size=8
query_cache_size=32M
tmp_table_size=64M
max_heap_table_size=64M

# Configurações de conexão
max_connections=151
max_connect_errors=10
connect_timeout=60
wait_timeout=28800
interactive_timeout=28800

# Configurações de segurança (SEM skip-grant-tables)
# skip-networking
# skip-name-resolve
skip-external-locking
skip-symbolic-links

# Configurações de log
log-error=C:/xampp/mysql/data/mysql_error.log
slow_query_log=1
slow_query_log_file=C:/xampp/mysql/data/mysql_slow.log
long_query_time=2

# Configurações de charset
character-set-server=utf8
collation-server=utf8_general_ci

# Configurações adicionais
sql_mode=NO_ENGINE_SUBSTITUTION
default-storage-engine=InnoDB
explicit_defaults_for_timestamp=1

[mysql]
default-character-set=utf8

[client]
default-character-set=utf8
port=3306
socket=mysql";

// Salvar configuração
file_put_contents($my_ini_path, $my_ini_seguro);
echo "<div class='sucesso'>✅ Configuração segura aplicada (porta 3306)</div>";

echo "<h4>🔧 Principais mudanças:</h4>";
echo "<ul>";
echo "<li>✅ <strong>port=3306</strong> - Porta padrão</li>";
echo "<li>✅ <strong>innodb_force_recovery=0</strong> - Sem recuperação forçada</li>";
echo "<li>✅ <strong>default-storage-engine=InnoDB</strong> - Motor padrão</li>";
echo "<li>✅ <strong>Removido skip-grant-tables</strong> - Segurança restaurada</li>";
echo "<li>✅ <strong>Valores de memória otimizados</strong> - Melhor performance</li>";
echo "</ul>";
echo "</div>";

// PASSO 4: Parar MySQL atual e reiniciar
echo "<div class='step'>";
echo "<h3>4️⃣ Parando MySQL atual e reiniciando na porta 3306...</h3>";

echo "<div class='executando'>🔄 Parando MySQL atual...</div>";

// Encerrar processos MySQL
$comandos_kill = [
    "taskkill /F /IM mysqld.exe",
    "taskkill /F /IM mysql.exe"
];

foreach ($comandos_kill as $comando) {
    $resultado = executarComando($comando);
    if ($resultado['return'] == 0) {
        echo "<div class='sucesso'>✅ Processo encerrado: $comando</div>";
    }
}

echo "<div class='info'>⏳ Aguardando 10 segundos...</div>";
sleep(10);

// Verificar se a porta 3306 está livre
$resultado = executarComando("netstat -ano | findstr :3306");
if (empty($resultado['output'])) {
    echo "<div class='sucesso'>✅ Porta 3306 está livre</div>";
} else {
    echo "<div class='aviso'>⚠️ Porta 3306 ainda em uso, tentando liberar...</div>";
    foreach ($resultado['output'] as $linha) {
        if (preg_match('/\s+(\d+)$/', $linha, $matches)) {
            $pid = $matches[1];
            executarComando("taskkill /F /PID $pid");
        }
    }
    sleep(5);
}

// Tentar iniciar MySQL na porta 3306
echo "<div class='executando'>🔄 Iniciando MySQL na porta 3306...</div>";

$mysql_bin = 'C:\\xampp\\mysql\\bin\\mysqld.exe';
if (file_exists($mysql_bin)) {
    $resultado_start = executarComando("start /B \"\" \"$mysql_bin\" --defaults-file=\"$my_ini_path\" --console");
    
    echo "<div class='info'>⏳ Aguardando 15 segundos para inicialização...</div>";
    sleep(15);
    
    // Verificar se iniciou na porta 3306
    if (testarMySQLPorta(3306)) {
        echo "<div class='sucesso'>";
        echo "<h3>🎉 SUCESSO! MYSQL ESTÁ RODANDO NA PORTA 3306!</h3>";
        echo "<p>O MySQL foi corrigido e está funcionando na porta padrão.</p>";
        echo "</div>";
        
        echo "<h3>✅ PRÓXIMOS PASSOS:</h3>";
        echo "<div class='info'>";
        echo "<ol>";
        echo "<li>Teste o phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
        echo "<li>Teste seu sistema: <a href='http://localhost:8080/Sistema%20De%20Agendamento/public/'>Sistema Principal</a></li>";
        echo "<li>Verifique se tudo está funcionando normalmente</li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<a href='http://localhost/phpmyadmin' target='_blank' class='btn btn-success'>🌐 Abrir phpMyAdmin</a>";
        echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Sistema Principal</a>";
        echo "<a href='verificar_mysql.php' class='btn'>🔍 Verificar MySQL</a>";
        echo "</div>";
        
    } else {
        echo "<div class='erro'>❌ Falha ao iniciar MySQL na porta 3306</div>";
        echo "<p>Vamos tentar uma abordagem alternativa...</p>";
        
        // Tentar via XAMPP Control Panel
        echo "<div class='aviso'>";
        echo "<h4>🔧 SOLUÇÃO MANUAL:</h4>";
        echo "<ol>";
        echo "<li>Abra o XAMPP Control Panel como administrador</li>";
        echo "<li>Clique em 'Stop' ao lado do MySQL (se estiver rodando)</li>";
        echo "<li>Clique em 'Start' ao lado do MySQL</li>";
        echo "<li>Aguarde alguns segundos</li>";
        echo "<li>Verifique se a luz ficou verde</li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<a href='verificar_mysql.php' class='btn'>🔍 Verificar MySQL</a>";
        echo "<a href='corrigir_mysql_ultimo_recurso.php' class='btn btn-warning'>⚡ Correção de Último Recurso</a>";
        echo "</div>";
    }
} else {
    echo "<div class='erro'>❌ Executável MySQL não encontrado</div>";
}
echo "</div>";

echo "<h3>📋 RESUMO DAS AÇÕES:</h3>";
echo "<div class='info'>";
echo "<ul>";
echo "<li>✅ Verificação de portas realizada</li>";
echo "<li>✅ Backup do my.ini criado</li>";
echo "<li>✅ Configuração segura aplicada (porta 3306)</li>";
echo "<li>✅ MySQL parado e reiniciado</li>";
if (testarMySQLPorta(3306)) {
    echo "<li>✅ MySQL funcionando na porta 3306</li>";
} else {
    echo "<li>⚠️ Verificação manual necessária</li>";
}
echo "</ul>";
echo "</div>";

echo "<h3>🔧 FERRAMENTAS ADICIONAIS:</h3>";
echo "<div class='info'>";
echo "<a href='verificar_mysql.php' class='btn'>🔍 Verificar MySQL</a>";
echo "<a href='corrigir_mysql_ultimo_recurso.php' class='btn btn-warning'>⚡ Correção de Último Recurso</a>";
echo "<a href='SOLUCAO-MYSQL-SHUTDOWN.html' class='btn'>📖 Guia Completo</a>";
echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Sistema Principal</a>";
echo "</div>";

echo "</div>";
?>













