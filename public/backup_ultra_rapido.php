<?php
// Backup Ultra Rápido - Apenas o essencial
set_time_limit(60);
ini_set('memory_limit', '128M');

echo "<h1>⚡ Backup Ultra Rápido</h1>";

// Configurações
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'sistema_agendamento';
$backupDir = __DIR__ . '/backup_ultra_' . date('Y-m-d_H-i-s');

echo "<p>📁 Criando pasta: " . basename($backupDir) . "</p>";

// Criar pasta
mkdir($backupDir, 0777, true);

// 1. BACKUP RÁPIDO DO BANCO
echo "<h3>🗄️ Backup do banco...</h3>";

try {
    $mysqli = new mysqli($host, $username, $password, $database);
    
    if (!$mysqli->connect_error) {
        // Backup básico das tabelas principais
        $tables = ['usuarios', 'cursos', 'agendamentos', 'certificados'];
        $sqlContent = "-- Backup Rápido - " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($tables as $table) {
            $result = $mysqli->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                echo "<p>✅ Backup da tabela: $table</p>";
                
                // Estrutura
                $createResult = $mysqli->query("SHOW CREATE TABLE `$table`");
                $createRow = $createResult->fetch_array();
                $sqlContent .= "DROP TABLE IF EXISTS `$table`;\n";
                $sqlContent .= $createRow[1] . ";\n\n";
                
                // Dados (apenas primeiros 100 registros)
                $dataResult = $mysqli->query("SELECT * FROM `$table` LIMIT 100");
                while ($row = $dataResult->fetch_assoc()) {
                    $sqlContent .= "INSERT INTO `$table` VALUES (";
                    $values = [];
                    foreach ($row as $value) {
                        $values[] = $value === null ? 'NULL' : "'" . $mysqli->real_escape_string($value) . "'";
                    }
                    $sqlContent .= implode(', ', $values) . ");\n";
                }
                $sqlContent .= "\n";
            }
        }
        
        file_put_contents($backupDir . '/banco_backup.sql', $sqlContent);
        echo "<p>✅ Banco salvo!</p>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p>⚠️ Erro no banco: " . $e->getMessage() . "</p>";
}

// 2. BACKUP RÁPIDO DOS ARQUIVOS
echo "<h3>📁 Backup dos arquivos...</h3>";

$files = ['login.php', 'usuarios_api.php', 'dashboard_final.php', 'dashboard_professor.php', 'dashboard_aluno.php', 'index.php', '.htaccess'];
$copied = 0;

foreach ($files as $file) {
    if (file_exists($file)) {
        copy($file, $backupDir . '/' . $file);
        echo "<p>✅ $file</p>";
        $copied++;
    }
}

// 3. SCRIPT DE RESTAURAÇÃO
$restoreScript = '<?php
echo "<h1>🔄 Restaurar Backup</h1>";
$host = "localhost"; $user = "root"; $pass = ""; $db = "sistema_agendamento";
$mysqli = new mysqli($host, $user, $pass);
$mysqli->query("CREATE DATABASE IF NOT EXISTS `$db`");
$mysqli->select_db($db);
$sql = file_get_contents(__DIR__ . "/banco_backup.sql");
$queries = explode(";", $sql);
foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) $mysqli->query($query);
}
echo "<h2>✅ Restaurado!</h2>";
echo "<a href=\"../\">🏠 Voltar</a>";
?>';
file_put_contents($backupDir . '/restaurar.php', $restoreScript);

// 4. README
$readme = "BACKUP ULTRA RÁPIDO\nData: " . date('Y-m-d H:i:s') . "\n\nCOMO RESTAURAR:\n1. Copie esta pasta\n2. Acesse restaurar.php\n3. Pronto!";
file_put_contents($backupDir . '/README.txt', $readme);

echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h2>✅ Backup Ultra Rápido Concluído!</h2>";
echo "<p><strong>📁 Pasta:</strong> " . basename($backupDir) . "</p>";
echo "<p><strong>📄 Arquivos:</strong> $copied copiados</p>";
echo "<p><strong>🗄️ Banco:</strong> Backup básico</p>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='" . basename($backupDir) . "/' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold;'>";
echo "📁 Ver Backup";
echo "</a>";
echo "</div>";

echo "<div style='margin-top: 20px;'>";
echo "<a href='./' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🏠 Voltar</a>";
echo "<a href='login.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Login</a>";
echo "</div>";
?>








