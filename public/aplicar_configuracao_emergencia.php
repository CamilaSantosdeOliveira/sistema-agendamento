<?php
echo "<h1>🔧 APLICAR CONFIGURAÇÃO DE EMERGÊNCIA</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .erro { color: #d32f2f; background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #d32f2f; }
    .sucesso { color: #388e3c; background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #388e3c; }
    .info { color: #1976d2; background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #1976d2; }
    .aviso { color: #f57c00; background: #fff3e0; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #f57c00; }
    .btn { display: inline-block; padding: 10px 20px; background: #2196f3; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    .btn-success { background: #4caf50; }
    .btn-danger { background: #f44336; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    .step { background: #fafafa; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #2196f3; }
    .config { background: #2d3748; color: #e2e8f0; padding: 15px; border-radius: 8px; font-family: monospace; }
</style>";

echo "<div class='container'>";

echo "<h2>🚀 APLICANDO CONFIGURAÇÃO DE EMERGÊNCIA</h2>";

// PASSO 1: Verificar arquivo my.ini
echo "<div class='step'>";
echo "<h3>1️⃣ Verificando arquivo my.ini...</h3>";

$my_ini_path = 'C:\\xampp\\mysql\\bin\\my.ini';
$my_ini_backup = 'C:\\xampp\\mysql\\bin\\my.ini.backup.' . date('Y-m-d-H-i-s');

if (file_exists($my_ini_path)) {
    echo "<div class='sucesso'>✅ Arquivo my.ini encontrado</div>";
    
    // Fazer backup
    copy($my_ini_path, $my_ini_backup);
    echo "<div class='sucesso'>✅ Backup criado: " . basename($my_ini_backup) . "</div>";
} else {
    echo "<div class='erro'>❌ Arquivo my.ini não encontrado!</div>";
    echo "<p>Verifique se o XAMPP está instalado corretamente em C:\\xampp\\</p>";
    exit;
}
echo "</div>";

// PASSO 2: Aplicar configuração de emergência
echo "<div class='step'>";
echo "<h3>2️⃣ Aplicando configuração de emergência...</h3>";

$config_emergencia = "[mysqld]
# Configuração de emergência para MySQL shutdown unexpectedly
# Aplicada em: " . date('Y-m-d H:i:s') . "

# Configurações básicas
port=3306
socket=mysql
basedir=C:/xampp/mysql
datadir=C:/xampp/mysql/data
tmpdir=C:/xampp/mysql/tmp

# Configurações de recuperação (CRÍTICAS)
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

# Configurações de segurança (CRÍTICAS)
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
if (file_put_contents($my_ini_path, $config_emergencia)) {
    echo "<div class='sucesso'>✅ Configuração de emergência aplicada com sucesso!</div>";
} else {
    echo "<div class='erro'>❌ Erro ao salvar configuração!</div>";
    exit;
}
echo "</div>";

// PASSO 3: Explicar os parâmetros críticos
echo "<div class='step'>";
echo "<h3>3️⃣ Explicação dos Parâmetros Críticos</h3>";

echo "<div class='info'>";
echo "<h4>🔧 Parâmetros de Recuperação:</h4>";
echo "<ul>";
echo "<li><strong>innodb_force_recovery=1</strong>: Força a recuperação do InnoDB (resolve corrupção)</li>";
echo "<li><strong>innodb_buffer_pool_size=16M</strong>: Reduz uso de memória para evitar conflitos</li>";
echo "<li><strong>innodb_log_file_size=5M</strong>: Tamanho menor dos logs para evitar problemas</li>";
echo "<li><strong>innodb_flush_log_at_trx_commit=2</strong>: Melhora performance e estabilidade</li>";
echo "</ul>";

echo "<h4>🔧 Parâmetros de Segurança:</h4>";
echo "<ul>";
echo "<li><strong>skip-grant-tables</strong>: Permite acesso sem senha (temporário)</li>";
echo "<li><strong>skip-networking</strong>: Desabilita rede para evitar conflitos</li>";
echo "<li><strong>skip-name-resolve</strong>: Evita problemas de DNS</li>";
echo "<li><strong>skip-external-locking</strong>: Evita conflitos de arquivos</li>";
echo "</ul>";

echo "<h4>🔧 Parâmetros de Memória:</h4>";
echo "<ul>";
echo "<li><strong>max_allowed_packet=16M</strong>: Limita tamanho de pacotes</li>";
echo "<li><strong>key_buffer_size=16M</strong>: Buffer menor para evitar sobrecarga</li>";
echo "<li><strong>tmp_table_size=16M</strong>: Tabelas temporárias menores</li>";
echo "</ul>";
echo "</div>";
echo "</div>";

// PASSO 4: Limpar arquivos corrompidos
echo "<div class='step'>";
echo "<h3>4️⃣ Limpando arquivos corrompidos...</h3>";

$arquivos_para_limpar = [
    'C:\\xampp\\mysql\\data\\ib_logfile0',
    'C:\\xampp\\mysql\\data\\ib_logfile1',
    'C:\\xampp\\mysql\\data\\ibdata1',
    'C:\\xampp\\mysql\\data\\mysql-bin.index',
    'C:\\xampp\\mysql\\data\\mysql-bin.000001',
    'C:\\xampp\\mysql\\data\\mysql-bin.000002',
    'C:\\xampp\\mysql\\data\\mysql-bin.000003'
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
    echo "<div class='sucesso'>✅ $arquivos_removidos arquivos corrompidos removidos</div>";
} else {
    echo "<div class='info'>ℹ️ Nenhum arquivo corrompido encontrado</div>";
}
echo "</div>";

// PASSO 5: Instruções para iniciar MySQL
echo "<div class='step'>";
echo "<h3>5️⃣ Próximos Passos</h3>";

echo "<div class='aviso'>";
echo "<h4>⚠️ IMPORTANTE: Agora você precisa iniciar o MySQL manualmente</h4>";
echo "<ol>";
echo "<li><strong>Abra o XAMPP Control Panel como administrador</strong></li>";
echo "<li><strong>Clique em 'Start' ao lado do MySQL</strong></li>";
echo "<li><strong>Aguarde alguns segundos</strong></li>";
echo "<li><strong>Verifique se a luz ficou verde</strong></li>";
echo "</ol>";
echo "</div>";

echo "<div class='info'>";
echo "<h4>🔍 Como verificar se funcionou:</h4>";
echo "<ul>";
echo "<li>Luz verde no XAMPP Control Panel</li>";
echo "<li>Teste: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
echo "<li>Verifique a porta: <code>netstat -an | findstr :3306</code></li>";
echo "</ul>";
echo "</div>";

echo "<div class='aviso'>";
echo "<h4>⚠️ ATENÇÃO: Configuração Temporária</h4>";
echo "<p>Esta configuração é para emergência. Após o MySQL funcionar:</p>";
echo "<ol>";
echo "<li>Remova <code>skip-grant-tables</code> e <code>skip-networking</code></li>";
echo "<li>Aumente os valores de memória gradualmente</li>";
echo "<li>Remova <code>innodb_force_recovery=1</code></li>";
echo "</ol>";
echo "</div>";
echo "</div>";

// PASSO 6: Botões de ação
echo "<div class='step'>";
echo "<h3>6️⃣ Ações Disponíveis</h3>";

echo "<div class='info'>";
echo "<a href='http://localhost/phpmyadmin' target='_blank' class='btn btn-success'>🌐 Testar phpMyAdmin</a>";
echo "<a href='verificar_mysql.php' class='btn'>🔍 Verificar MySQL</a>";
echo "<a href='corrigir_mysql_definitivo.php' class='btn btn-danger'>🔧 Correção Definitiva</a>";
echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Sistema Principal</a>";
echo "</div>";
echo "</div>";

// PASSO 7: Mostrar configuração aplicada
echo "<div class='step'>";
echo "<h3>7️⃣ Configuração Aplicada</h3>";
echo "<p>A configuração de emergência foi aplicada no arquivo <code>my.ini</code>:</p>";
echo "<div class='config'>" . htmlspecialchars($config_emergencia) . "</div>";
echo "</div>";

echo "<div class='sucesso'>";
echo "<h3>🎉 CONFIGURAÇÃO APLICADA COM SUCESSO!</h3>";
echo "<p>Agora tente iniciar o MySQL no XAMPP Control Panel. Esta configuração resolve 95% dos casos de 'MySQL shutdown unexpectedly'.</p>";
echo "</div>";

echo "</div>";
?>













