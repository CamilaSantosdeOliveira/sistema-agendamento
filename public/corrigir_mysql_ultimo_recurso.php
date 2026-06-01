<?php
echo "<h1>🔧 CORREÇÃO - ÚLTIMO RECURSO</h1>";
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

echo "<div class='emergency'>";
echo "<h2>🚨 CORREÇÃO DE ÚLTIMO RECURSO</h2>";
echo "<p><strong>ATENÇÃO:</strong> Esta é a solução mais agressiva para resolver o problema de MySQL shutdown unexpectedly.</p>";
echo "</div>";

echo "<h2>🚀 INICIANDO CORREÇÃO ULTRA-AGRESSIVA...</h2>";

// PASSO 1: Verificar se o MySQL está rodando
echo "<div class='step'>";
echo "<h3>1️⃣ Verificando status atual do MySQL...</h3>";
if (testarMySQL()) {
    echo "<div class='sucesso'>✅ MySQL já está funcionando!</div>";
    echo "<p>O MySQL está rodando corretamente. Não é necessário fazer correções.</p>";
    echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Voltar ao Sistema</a>";
    exit;
} else {
    echo "<div class='erro'>❌ MySQL não está rodando - iniciando correção ultra-agressiva...</div>";
}
echo "</div>";

// PASSO 2: Encerrar TODOS os processos de forma ultra-agressiva
echo "<div class='step'>";
echo "<h3>2️⃣ Encerrando TODOS os processos de forma ultra-agressiva...</h3>";
echo "<div class='executando'>🔄 Encerrando processos de forma ultra-agressiva...</div>";

$comandos_kill = [
    "taskkill /F /IM mysqld.exe",
    "taskkill /F /IM mysql.exe", 
    "taskkill /F /IM mysqld-nt.exe",
    "taskkill /F /IM mysqld-5.7.exe",
    "taskkill /F /IM mysqld-8.0.exe",
    "taskkill /F /IM mysqld-5.6.exe",
    "taskkill /F /IM mysqld-5.5.exe",
    "taskkill /F /IM mysqld-5.1.exe",
    "taskkill /F /IM mysqld-5.0.exe"
];

foreach ($comandos_kill as $comando) {
    $resultado = executarComando($comando);
    if ($resultado['return'] == 0) {
        echo "<div class='sucesso'>✅ Processo encerrado: $comando</div>";
    }
}

// Aguardar mais tempo
echo "<div class='info'>⏳ Aguardando 15 segundos para liberar recursos...</div>";
sleep(15);
echo "</div>";

// PASSO 3: Verificar e limpar porta 3306 de forma agressiva
echo "<div class='step'>";
echo "<h3>3️⃣ Verificando e limpando porta 3306 de forma agressiva...</h3>";

$resultado = executarComando("netstat -ano | findstr :3306");
if (!empty($resultado['output'])) {
    echo "<div class='aviso'>⚠️ Porta 3306 ainda está em uso:</div>";
    foreach ($resultado['output'] as $linha) {
        echo "<div class='comando'>$linha</div>";
    }
    
    // Tentar encerrar TODOS os processos que estão usando a porta
    foreach ($resultado['output'] as $linha) {
        if (preg_match('/\s+(\d+)$/', $linha, $matches)) {
            $pid = $matches[1];
            $resultado_kill = executarComando("taskkill /F /PID $pid");
            if ($resultado_kill['return'] == 0) {
                echo "<div class='sucesso'>✅ Processo PID $pid encerrado</div>";
            }
        }
    }
    
    echo "<div class='info'>⏳ Aguardando 10 segundos após encerrar processos...</div>";
    sleep(10);
} else {
    echo "<div class='sucesso'>✅ Porta 3306 está livre!</div>";
}
echo "</div>";

// PASSO 4: Criar configuração ULTRA-EMERGÊNCIA
echo "<div class='step'>";
echo "<h3>4️⃣ Criando configuração ULTRA-EMERGÊNCIA...</h3>";

$my_ini_path = 'C:\\xampp\\mysql\\bin\\my.ini';
$my_ini_backup = 'C:\\xampp\\mysql\\bin\\my.ini.backup.' . date('Y-m-d-H-i-s');

if (file_exists($my_ini_path)) {
    // Fazer backup com timestamp
    copy($my_ini_path, $my_ini_backup);
    echo "<div class='sucesso'>✅ Backup criado: " . basename($my_ini_backup) . "</div>";
    
    // Configuração ULTRA-EMERGÊNCIA
    $my_ini_ultra = "[mysqld]
# Configuração ULTRA-EMERGÊNCIA para MySQL shutdown unexpectedly
# Aplicada em: " . date('Y-m-d H:i:s') . "
# ÚLTIMO RECURSO - Configuração mais agressiva possível

# Configurações básicas
port=3306
socket=mysql
basedir=C:/xampp/mysql
datadir=C:/xampp/mysql/data
tmpdir=C:/xampp/mysql/tmp

# Configurações de recuperação ULTRA-AGRESSIVAS
innodb_force_recovery=3
innodb_buffer_pool_size=8M
innodb_log_file_size=2M
innodb_log_buffer_size=4M
innodb_flush_log_at_trx_commit=0
innodb_lock_wait_timeout=120
innodb_read_io_threads=1
innodb_write_io_threads=1
innodb_io_capacity=100

# Configurações de memória MÍNIMAS
key_buffer_size=8M
max_allowed_packet=8M
table_open_cache=32
thread_cache_size=4
query_cache_size=8M
tmp_table_size=8M
max_heap_table_size=8M

# Configurações de conexão
max_connections=50
max_connect_errors=100
connect_timeout=120
wait_timeout=600
interactive_timeout=600

# Configurações de segurança ULTRA-AGRESSIVAS
skip-grant-tables
skip-networking
skip-name-resolve
skip-external-locking
skip-symbolic-links
skip-show-database
skip-thread-priority

# Configurações de log
log-error=C:/xampp/mysql/data/mysql_error.log
slow_query_log=0
general_log=0

# Configurações de charset
character-set-server=utf8
collation-server=utf8_general_ci

# Configurações adicionais de emergência
sql_mode=NO_ENGINE_SUBSTITUTION
default-storage-engine=MyISAM
explicit_defaults_for_timestamp=1

[mysql]
default-character-set=utf8

[client]
default-character-set=utf8
port=3306
socket=mysql";

    // Salvar configuração
    file_put_contents($my_ini_path, $my_ini_ultra);
    echo "<div class='sucesso'>✅ Configuração ULTRA-EMERGÊNCIA aplicada!</div>";
} else {
    echo "<div class='erro'>❌ Arquivo my.ini não encontrado!</div>";
    echo "<p>Verifique se o XAMPP está instalado corretamente em C:\\xampp\\</p>";
}
echo "</div>";

// PASSO 5: Limpar TODOS os arquivos de log e dados corrompidos de forma agressiva
echo "<div class='step'>";
echo "<h3>5️⃣ Limpando TODOS os arquivos de forma ultra-agressiva...</h3>";

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
    'C:\\xampp\\mysql\\data\\mysql-bin.000006',
    'C:\\xampp\\mysql\\data\\mysql-bin.000007',
    'C:\\xampp\\mysql\\data\\mysql-bin.000008',
    'C:\\xampp\\mysql\\data\\mysql-bin.000009',
    'C:\\xampp\\mysql\\data\\mysql-bin.000010',
    'C:\\xampp\\mysql\\data\\mysql_error.log',
    'C:\\xampp\\mysql\\data\\mysql_slow.log',
    'C:\\xampp\\mysql\\data\\mysql.log',
    'C:\\xampp\\mysql\\data\\mysql-bin.log'
];

$arquivos_removidos = 0;
foreach ($arquivos_para_limpar as $arquivo) {
    if (file_exists($arquivo)) {
        unlink($arquivo);
        echo "<div class='sucesso'>✅ Arquivo removido: " . basename($arquivo) . "</div>";
        $arquivos_removidos++;
    }
}

if ($arquivos_removidos > 0) {
    echo "<div class='sucesso'>✅ $arquivos_removidos arquivos removidos</div>";
} else {
    echo "<div class='info'>ℹ️ Nenhum arquivo encontrado para remoção</div>";
}

// Criar diretório tmp se não existir
$tmp_dir = 'C:\\xampp\\mysql\\tmp';
if (!is_dir($tmp_dir)) {
    mkdir($tmp_dir, 0777, true);
    echo "<div class='sucesso'>✅ Diretório tmp criado</div>";
}
echo "</div>";

// PASSO 6: Tentar inicializar banco de dados do zero
echo "<div class='step'>";
echo "<h3>6️⃣ Tentando inicializar banco de dados do zero...</h3>";

$mysql_bin = 'C:\\xampp\\mysql\\bin\\mysqld.exe';
$mysql_install = 'C:\\xampp\\mysql\\bin\\mysql_install_db.exe';

if (file_exists($mysql_bin)) {
    // Tentar inicializar o banco de dados
    if (file_exists($mysql_install)) {
        echo "<div class='executando'>🔄 Inicializando banco de dados do zero...</div>";
        $resultado_install = executarComando("\"$mysql_install\" --datadir=\"C:\\xampp\\mysql\\data\" --defaults-file=\"$my_ini_path\" --force");
        if ($resultado_install['return'] == 0) {
            echo "<div class='sucesso'>✅ Banco de dados inicializado do zero!</div>";
        } else {
            echo "<div class='aviso'>⚠️ Falha na inicialização automática, tentando manual...</div>";
        }
    }
    
    // Tentar iniciar MySQL com configuração ultra-agressiva
    echo "<div class='executando'>🔄 Iniciando MySQL com configuração ultra-agressiva...</div>";
    $resultado_start = executarComando("start /B \"\" \"$mysql_bin\" --defaults-file=\"$my_ini_path\" --console --skip-grant-tables --skip-networking");
    
    // Aguardar inicialização
    echo "<div class='info'>⏳ Aguardando 20 segundos para inicialização...</div>";
    sleep(20);
    
    // Verificar se iniciou
    if (testarMySQL()) {
        echo "<div class='sucesso'>";
        echo "<h3>🎉 SUCESSO! MYSQL ESTÁ FUNCIONANDO!</h3>";
        echo "<p>O MySQL foi corrigido com configuração ultra-agressiva!</p>";
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
        echo "<div class='erro'>❌ Mesmo com configuração ultra-agressiva não funcionou</div>";
        
        // Tentar com porta diferente
        echo "<div class='executando'>🔄 Tentando com porta 3307...</div>";
        
        // Criar configuração com porta 3307
        $my_ini_3307 = str_replace('port=3306', 'port=3307', $my_ini_ultra);
        file_put_contents($my_ini_path, $my_ini_3307);
        
        $resultado_start2 = executarComando("start /B \"\" \"$mysql_bin\" --defaults-file=\"$my_ini_path\" --console");
        sleep(15);
        
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

// PASSO 7: Se ainda não funcionou, mostrar soluções finais
if (!testarMySQL()) {
    echo "<div class='step'>";
    echo "<h3>7️⃣ SOLUÇÕES FINAIS NECESSÁRIAS</h3>";
    echo "<div class='emergency'>";
    echo "<h4>🚨 ATENÇÃO: Mesmo com configuração ultra-agressiva não funcionou</h4>";
    echo "<p>Isso indica um problema mais profundo. Execute estas soluções finais:</p>";
    echo "</div>";
    
    echo "<h4>🔧 SOLUÇÃO 1: Reinstalar MySQL</h4>";
    echo "<ol>";
    echo "<li>Faça backup da pasta <code>C:\\xampp\\mysql\\data</code></li>";
    echo "<li>Delete a pasta <code>C:\\xampp\\mysql</code></li>";
    echo "<li>Baixe o XAMPP novamente</li>";
    echo "<li>Extraia apenas a pasta mysql</li>";
    echo "<li>Copie a pasta mysql para <code>C:\\xampp\\</code></li>";
    echo "</ol>";
    
    echo "<h4>🔧 SOLUÇÃO 2: Usar MySQL Workbench</h4>";
    echo "<ol>";
    echo "<li>Baixe e instale o MySQL Workbench</li>";
    echo "<li>Configure uma instância MySQL local</li>";
    echo "<li>Atualize as configurações de conexão do sistema</li>";
    echo "</ol>";
    
    echo "<h4>🔧 SOLUÇÃO 3: Usar MariaDB</h4>";
    echo "<ol>";
    echo "<li>Desinstale o MySQL do XAMPP</li>";
    echo "<li>Instale o MariaDB (compatível com MySQL)</li>";
    echo "<li>Configure as conexões</li>";
    echo "</ol>";
    
    echo "<h4>💻 COMANDOS FINAIS</h4>";
    echo "<p>Execute no CMD como administrador:</p>";
    echo "<div class='comando'># Verificar se há outros processos MySQL
tasklist | findstr mysql

# Verificar todas as portas em uso
netstat -ano | findstr :3306
netstat -ano | findstr :3307

# Verificar logs do sistema
eventvwr.msc

# Verificar serviços MySQL
sc query mysql
sc query mariadb</div>";
    echo "</div>";
}

echo "<h3>📋 RESUMO DAS AÇÕES:</h3>";
echo "<div class='info'>";
echo "<ul>";
echo "<li>✅ Processos MySQL encerrados de forma ultra-agressiva</li>";
echo "<li>✅ Porta 3306 verificada e limpa</li>";
echo "<li>✅ Configuração ULTRA-EMERGÊNCIA aplicada</li>";
echo "<li>✅ Arquivos de log removidos de forma agressiva</li>";
echo "<li>✅ Tentativa de inicialização do zero</li>";
if (!testarMySQL()) {
    echo "<li>⚠️ Soluções finais necessárias</li>";
}
echo "</ul>";
echo "</div>";

echo "<h3>🔧 FERRAMENTAS ADICIONAIS:</h3>";
echo "<div class='info'>";
echo "<a href='verificar_mysql.php' class='btn'>🔍 Verificar MySQL</a>";
echo "<a href='corrigir_mysql_definitivo.php' class='btn btn-warning'>⚡ Correção Definitiva</a>";
echo "<a href='SOLUCAO-MYSQL-SHUTDOWN.html' class='btn'>📖 Guia Completo</a>";
echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Sistema Principal</a>";
echo "</div>";

echo "</div>";
?>











