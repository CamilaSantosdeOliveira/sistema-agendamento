<?php
echo "<h1>🔧 CORREÇÃO DO ARQUIVO MY.INI</h1>";

$my_ini_path = "C:/xampp/mysql/bin/my.ini";
$backup_path = "C:/xampp/mysql/bin/my.ini.backup";

// Fazer backup do arquivo original
if (file_exists($my_ini_path)) {
    copy($my_ini_path, $backup_path);
    echo "<p style='color: green;'>✅ Backup criado: my.ini.backup</p>";
}

// Configuração corrigida
$config_corrigida = '[mysqld]
port=3306
socket=mysql
basedir=C:/xampp/mysql
datadir=C:/xampp/mysql/data
tmpdir=C:/xampp/mysql/tmp
character-set-server=utf8
collation-server=utf8_general_ci
max_allowed_packet=16M
innodb_force_recovery=0
default-storage-engine=InnoDB
sql_mode=NO_ENGINE_SUBSTITUTION,STRICT_TRANS_TABLES

[mysql]
default-character-set=utf8

[client]
default-character-set=utf8
port=3306
socket=mysql';

// Escrever a configuração corrigida
if (file_put_contents($my_ini_path, $config_corrigida)) {
    echo "<p style='color: green;'>✅ Arquivo my.ini corrigido!</p>";
    echo "<h3>🔧 Principais correções:</h3>";
    echo "<ul>";
    echo "<li>❌ Removido: skip-slave-start (causa problemas)</li>";
    echo "<li>❌ Removido: skip-networking (impede conexões)</li>";
    echo "<li>❌ Removido: skip-name-resolve (causa problemas)</li>";
    echo "<li>❌ Removido: skip-replica-start (causa problemas)</li>";
    echo "<li>❌ Removido: log-bin=OFF (desnecessário)</li>";
    echo "<li>❌ Removido: binlog-format=OFF (desnecessário)</li>";
    echo "<li>✅ Adicionado: max_allowed_packet=16M</li>";
    echo "<li>✅ Adicionado: innodb_force_recovery=0</li>";
    echo "<li>✅ Adicionado: default-storage-engine=InnoDB</li>";
    echo "<li>✅ Adicionado: sql_mode correto</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ Erro ao escrever arquivo my.ini</p>";
}

echo "<h3>🔄 Próximos passos:</h3>";
echo "<ol>";
echo "<li>Feche o XAMPP Control Panel</li>";
echo "<li>Abra o XAMPP Control Panel como administrador</li>";
echo "<li>Clique em 'Start' ao lado do MySQL</li>";
echo "<li>Teste a conexão</li>";
echo "</ol>";

echo "<p><a href='teste_simples_dashboard.php' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🧪 Testar Conexão</a></p>";
?>


