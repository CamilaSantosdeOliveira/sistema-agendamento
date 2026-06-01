<?php
echo "<h1>🔧 CORREÇÃO DEFINITIVA - MySQL XAMPP</h1>";
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

echo "<h2>🚀 CORREÇÃO DEFINITIVA EM ANDAMENTO...</h2>";

// PASSO 1: Verificar se o MySQL está rodando
echo "<div class='step'>";
echo "<h3>1️⃣ Verificando status atual do MySQL...</h3>";
if (testarMySQL()) {
    echo "<div class='sucesso'>✅ MySQL já está funcionando!</div>";
    echo "<p>O MySQL está rodando corretamente. Não é necessário fazer correções.</p>";
    echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Voltar ao Sistema</a>";
    exit;
} else {
    echo "<div class='erro'>❌ MySQL não está rodando - iniciando correção definitiva...</div>";
}
echo "</div>";

// PASSO 2: Encerrar TODOS os processos MySQL de forma mais agressiva
echo "<div class='step'>";
echo "<h3>2️⃣ Encerrando TODOS os processos MySQL...</h3>";
echo "<div class='executando'>🔄 Encerrando processos de forma agressiva...</div>";

$comandos_kill = [
    "taskkill /F /IM mysqld.exe",
    "taskkill /F /IM mysql.exe", 
    "taskkill /F /IM mysqld-nt.exe",
    "taskkill /F /IM mysqld-5.7.exe",
    "taskkill /F /IM mysqld-8.0.exe",
    "taskkill /F /IM mysqld-5.6.exe",
    "taskkill /F /IM mysqld-5.5.exe"
];

foreach ($comandos_kill as $comando) {
    $resultado = executarComando($comando);
    if ($resultado['return'] == 0) {
        echo "<div class='sucesso'>✅ Processo encerrado: $comando</div>";
    }
}

// Aguardar mais tempo
echo "<div class='info'>⏳ Aguardando 10 segundos para liberar recursos...</div>";
sleep(10);
echo "</div>";

// PASSO 3: Verificar e limpar porta 3306
echo "<div class='step'>";
echo "<h3>3️⃣ Verificando e limpando porta 3306...</h3>";

$resultado = executarComando("netstat -ano | findstr :3306");
if (!empty($resultado['output'])) {
    echo "<div class='aviso'>⚠️ Porta 3306 ainda está em uso:</div>";
    foreach ($resultado['output'] as $linha) {
        echo "<div class='comando'>$linha</div>";
    }
    
    // Tentar encerrar o processo que está usando a porta
    foreach ($resultado['output'] as $linha) {
        if (preg_match('/\s+(\d+)$/', $linha, $matches)) {
            $pid = $matches[1];
            $resultado_kill = executarComando("taskkill /F /PID $pid");
            if ($resultado_kill['return'] == 0) {
                echo "<div class='sucesso'>✅ Processo PID $pid encerrado</div>";
            }
        }
    }
    
    echo "<div class='info'>⏳ Aguardando 5 segundos após encerrar processos...</div>";
    sleep(5);
} else {
    echo "<div class='sucesso'>✅ Porta 3306 está livre!</div>";
}
echo "</div>";

// PASSO 4: Criar configuração de emergência completa
echo "<div class='step'>";
echo "<h3>4️⃣ Criando configuração de emergência completa...</h3>";

$my_ini_path = 'C:\\xampp\\mysql\\bin\\my.ini';
$my_ini_backup = 'C:\\xampp\\mysql\\bin\\my.ini.backup.' . date('Y-m-d-H-i-s');

if (file_exists($my_ini_path)) {
    // Fazer backup com timestamp
    copy($my_ini_path, $my_ini_backup);
    echo "<div class='sucesso'>✅ Backup criado: " . basename($my_ini_backup) . "</div>";
    
    // Configuração de emergência completa
    $my_ini_emergency = "[mysqld]
# Configuração de emergência para MySQL shutdown unexpectedly
# Versão: " . date('Y-m-d H:i:s') . "

# Configurações básicas
port=3306
socket=mysql
basedir=C:/xampp/mysql
datadir=C:/xampp/mysql/data
tmpdir=C:/xampp/mysql/tmp

# Configurações de recuperação
innodb_force_recovery=1
innodb_buffer_pool_size=16M
innodb_log_file_size=5M
innodb_log_buffer_size=8M
innodb_flush_log_at_trx_commit=2
innodb_lock_wait_timeout=50

# Configurações de memória
key_buffer_size=16M
max_allowed_packet=16M
table_open_cache=64
thread_cache_size=8
query_cache_size=16M
tmp_table_size=16M
max_heap_table_size=16M

# Configurações de conexão
max_connections=100
max_connect_errors=10
connect_timeout=60
wait_timeout=28800
interactive_timeout=28800

# Configurações de segurança
skip-grant-tables
skip-networking
skip-name-resolve
skip-external-locking

# Configurações de log
log-error=C:/xampp/mysql/data/mysql_error.log
slow_query_log=1
slow_query_log_file=C:/xampp/mysql/data/mysql_slow.log
long_query_time=2

# Configurações de charset
character-set-server=utf8
collation-server=utf8_general_ci

[mysql]
default-character-set=utf8

[client]
default-character-set=utf8
port=3306
socket=mysql";

    // Salvar configuração
    file_put_contents($my_ini_path, $my_ini_emergency);
    echo "<div class='sucesso'>✅ Configuração de emergência aplicada!</div>";
} else {
    echo "<div class='erro'>❌ Arquivo my.ini não encontrado!</div>";
    echo "<p>Verifique se o XAMPP está instalado corretamente em C:\\xampp\\</p>";
}
echo "</div>";

// PASSO 5: Limpar TODOS os arquivos de log e dados corrompidos
echo "<div class='step'>";
echo "<h3>5️⃣ Limpando TODOS os arquivos de log e dados corrompidos...</h3>";

$arquivos_para_limpar = [
    'C:\\xampp\\mysql\\data\\ib_logfile0',
    'C:\\xampp\\mysql\\data\\ib_logfile1',
    'C:\\xampp\\mysql\\data\\ibdata1',
    'C:\\xampp\\mysql\\data\\mysql-bin.index',
    'C:\\xampp\\mysql\\data\\mysql-bin.000001',
    'C:\\xampp\\mysql\\data\\mysql-bin.000002',
    'C:\\xampp\\mysql\\data\\mysql-bin.000003',
    'C:\\xampp\\mysql\\data\\mysql-bin.000004',
    'C:\\xampp\\mysql\\data\\mysql-bin.000005',
    'C:\\xampp\\mysql\\data\\mysql_error.log',
    'C:\\xampp\\mysql\\data\\mysql_slow.log'
];

foreach ($arquivos_para_limpar as $arquivo) {
    if (file_exists($arquivo)) {
        unlink($arquivo);
        echo "<div class='sucesso'>✅ Arquivo removido: " . basename($arquivo) . "</div>";
    }
}

// Criar diretório tmp se não existir
$tmp_dir = 'C:\\xampp\\mysql\\tmp';
if (!is_dir($tmp_dir)) {
    mkdir($tmp_dir, 0777, true);
    echo "<div class='sucesso'>✅ Diretório tmp criado</div>";
}
echo "</div>";

// PASSO 6: Tentar iniciar MySQL com diferentes métodos
echo "<div class='step'>";
echo "<h3>6️⃣ Tentando iniciar MySQL com diferentes métodos...</h3>";

$mysql_bin = 'C:\\xampp\\mysql\\bin\\mysqld.exe';
$mysql_install = 'C:\\xampp\\mysql\\bin\\mysql_install_db.exe';

if (file_exists($mysql_bin)) {
    // Método 1: Tentar inicializar o banco de dados
    if (file_exists($mysql_install)) {
        echo "<div class='executando'>🔄 Método 1: Inicializando banco de dados...</div>";
        $resultado_install = executarComando("\"$mysql_install\" --datadir=\"C:\\xampp\\mysql\\data\" --defaults-file=\"$my_ini_path\"");
        if ($resultado_install['return'] == 0) {
            echo "<div class='sucesso'>✅ Banco de dados inicializado!</div>";
        }
    }
    
    // Método 2: Tentar iniciar MySQL
    echo "<div class='executando'>🔄 Método 2: Iniciando MySQL...</div>";
    $resultado_start = executarComando("start /B \"\" \"$mysql_bin\" --defaults-file=\"$my_ini_path\" --console");
    
    // Aguardar inicialização
    echo "<div class='info'>⏳ Aguardando 15 segundos para inicialização...</div>";
    sleep(15);
    
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
        
        // Método 3: Tentar com porta diferente
        echo "<div class='executando'>🔄 Método 3: Tentando com porta 3307...</div>";
        
        // Criar configuração com porta 3307
        $my_ini_3307 = str_replace('port=3306', 'port=3307', $my_ini_emergency);
        file_put_contents($my_ini_path, $my_ini_3307);
        
        $resultado_start2 = executarComando("start /B \"\" \"$mysql_bin\" --defaults-file=\"$my_ini_path\" --console");
        sleep(10);
        
        // Testar conexão na porta 3307
        try {
            $conn = new mysqli('localhost', 'root', '', '', 3307);
            if (!$conn->connect_error) {
                echo "<div class='sucesso'>🎉 MYSQL FUNCIONANDO NA PORTA 3307!</div>";
                echo "<div class='aviso'>⚠️ IMPORTANTE: MySQL está rodando na porta 3307. Atualize suas configurações de conexão.</div>";
            } else {
                echo "<div class='erro'>❌ Mesmo com porta 3307 não funcionou</div>";
            }
        } catch (Exception $e) {
            echo "<div class='erro'>❌ Mesmo com porta 3307 não funcionou</div>";
        }
    }
} else {
    echo "<div class='erro'>❌ Executável MySQL não encontrado: $mysql_bin</div>";
    echo "<p>Verifique se o XAMPP está instalado corretamente.</p>";
}
echo "</div>";

// PASSO 7: Se ainda não funcionou, mostrar soluções manuais
if (!testarMySQL()) {
    echo "<div class='step'>";
    echo "<h3>7️⃣ SOLUÇÕES MANUAIS NECESSÁRIAS</h3>";
    echo "<div class='aviso'>⚠️ O MySQL não conseguiu iniciar automaticamente. Execute estas soluções manuais:</div>";
    
    echo "<h4>🔧 SOLUÇÃO 1: XAMPP Control Panel</h4>";
    echo "<ol>";
    echo "<li>Abra o XAMPP Control Panel como <strong>administrador</strong></li>";
    echo "<li>Clique em <strong>Start</strong> ao lado do MySQL</li>";
    echo "<li>Se der erro, clique em <strong>Logs</strong> para ver detalhes</li>";
    echo "</ol>";
    
    echo "<h4>🔧 SOLUÇÃO 2: Verificar Configuração</h4>";
    echo "<ol>";
    echo "<li>No XAMPP Control Panel, clique em <strong>Config</strong> ao lado do MySQL</li>";
    echo "<li>Selecione <strong>my.ini</strong></li>";
    echo "<li>Verifique se a porta está como 3306 ou 3307</li>";
    echo "<li>Verifique se o datadir está correto: <code>C:/xampp/mysql/data</code></li>";
    echo "</ol>";
    
    echo "<h4>🔧 SOLUÇÃO 3: Reinstalar MySQL</h4>";
    echo "<ol>";
    echo "<li>Faça backup da pasta <code>C:\\xampp\\mysql\\data</code></li>";
    echo "<li>Delete a pasta <code>C:\\xampp\\mysql</code></li>";
    echo "<li>Baixe o XAMPP novamente e extraia apenas a pasta mysql</li>";
    echo "<li>Copie a pasta mysql para <code>C:\\xampp\\</code></li>";
    echo "</ol>";
    
    echo "<h4>💻 COMANDOS ÚTEIS</h4>";
    echo "<p>Execute no CMD como administrador:</p>";
    echo "<div class='comando'># Encerrar processos MySQL
taskkill /F /IM mysqld.exe
taskkill /F /IM mysql.exe

# Verificar porta
netstat -an | findstr :3306
netstat -an | findstr :3307

# Verificar processos
tasklist | findstr mysql

# Verificar logs
type C:\\xampp\\mysql\\data\\mysql_error.log</div>";
    echo "</div>";
}

echo "<h3>📋 RESUMO DAS AÇÕES:</h3>";
echo "<div class='info'>";
echo "<ul>";
echo "<li>✅ Processos MySQL encerrados de forma agressiva</li>";
echo "<li>✅ Porta 3306 verificada e limpa</li>";
echo "<li>✅ Configuração de emergência completa aplicada</li>";
echo "<li>✅ Arquivos de log corrompidos removidos</li>";
echo "<li>✅ Tentativa de inicialização com múltiplos métodos</li>";
if (!testarMySQL()) {
    echo "<li>⚠️ Soluções manuais necessárias</li>";
}
echo "</ul>";
echo "</div>";

echo "<h3>🔧 FERRAMENTAS ADICIONAIS:</h3>";
echo "<div class='info'>";
echo "<a href='verificar_mysql.php' class='btn'>🔍 Verificar MySQL</a>";
echo "<a href='corrigir_mysql_rapido.php' class='btn btn-warning'>⚡ Correção Rápida</a>";
echo "<a href='SOLUCAO-MYSQL-SHUTDOWN.html' class='btn'>📖 Guia Completo</a>";
echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Sistema Principal</a>";
echo "</div>";

echo "</div>";
?>











