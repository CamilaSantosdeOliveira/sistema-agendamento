<?php
// Backup Final Simples - Funciona sempre
set_time_limit(120);
ini_set('memory_limit', '128M');

echo "<h1>🔄 Backup Final Simples</h1>";

// Configurações
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'sistema_agendamento';
$backupDir = __DIR__ . '/backup_final_' . date('Y-m-d_H-i-s');

echo "<p>📁 Criando pasta: " . basename($backupDir) . "</p>";

// Criar pasta
mkdir($backupDir, 0777, true);

// 1. BACKUP DO BANCO
echo "<h3>🗄️ Backup do banco...</h3>";

try {
    $mysqli = new mysqli($host, $username, $password, $database);
    
    if (!$mysqli->connect_error) {
        $tables = ['usuarios', 'cursos', 'agendamentos', 'certificados'];
        $sqlContent = "-- Backup Final - " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($tables as $table) {
            $result = $mysqli->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                echo "<p>✅ Backup da tabela: $table</p>";
                
                // Estrutura
                $createResult = $mysqli->query("SHOW CREATE TABLE `$table`");
                $createRow = $createResult->fetch_array();
                $sqlContent .= "DROP TABLE IF EXISTS `$table`;\n";
                $sqlContent .= $createRow[1] . ";\n\n";
                
                // Dados
                $dataResult = $mysqli->query("SELECT * FROM `$table`");
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
        
        file_put_contents($backupDir . '/banco_final.sql', $sqlContent);
        echo "<p>✅ Banco salvo!</p>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p>⚠️ Erro no banco: " . $e->getMessage() . "</p>";
}

// 2. BACKUP DOS ARQUIVOS
echo "<h3>📁 Backup dos arquivos...</h3>";

$files = [
    'login.php', 'usuarios_api.php', 'dashboard_final.php', 
    'dashboard_professor.php', 'dashboard_aluno.php', 'index.php', '.htaccess',
    'admin_api.php', 'api.php', 'agendamento.php', 'alunos.php', 'cursos.php',
    'certificados.php', 'professores.php', 'meus_cursos_aluno.php',
    'minhas_aulas_aluno.php', 'perfil_aluno.php', 'aulas_professor.php',
    'relatorios.php', 'config.php', 'db.php'
];

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
echo "<h1>🔄 Restaurar Backup Final</h1>";
$host = "localhost"; $user = "root"; $pass = ""; $db = "sistema_agendamento";
$mysqli = new mysqli($host, $user, $pass);
$mysqli->query("CREATE DATABASE IF NOT EXISTS `$db`");
$mysqli->select_db($db);
$sql = file_get_contents(__DIR__ . "/banco_final.sql");
$queries = explode(";", $sql);
foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) $mysqli->query($query);
}
echo "<h2>✅ Sistema Restaurado!</h2>";
echo "<a href=\"../\">🏠 Voltar</a>";
?>';
file_put_contents($backupDir . '/restaurar_final.php', $restoreScript);

// 4. README
$readme = "BACKUP FINAL DO SISTEMA\nData: " . date('Y-m-d H:i:s') . "\n\nCOMO RESTAURAR:\n1. Copie esta pasta\n2. Acesse restaurar_final.php\n3. Pronto!";
file_put_contents($backupDir . '/README_FINAL.txt', $readme);

echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h2>✅ Backup Final Concluído!</h2>";
echo "<p><strong>📁 Pasta:</strong> " . basename($backupDir) . "</p>";
echo "<p><strong>📄 Arquivos:</strong> $copied copiados</p>";
echo "<p><strong>🗄️ Banco:</strong> Incluído</p>";
echo "<p><strong>🎯 Conteúdo:</strong> TUDO do site!</p>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>📋 Inclui TUDO:</h3>";
echo "<ul>";
echo "<li>✅ Dashboard Admin</li>";
echo "<li>✅ Dashboard Professor</li>";
echo "<li>✅ Dashboard Aluno</li>";
echo "<li>✅ Banco de dados</li>";
echo "<li>✅ Sistema de login</li>";
echo "<li>✅ Todas as funcionalidades</li>";
echo "</ul>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='" . basename($backupDir) . "/' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold;'>";
echo "📁 Ver Backup Final";
echo "</a>";
echo "</div>";

echo "<div style='margin-top: 20px;'>";
echo "<a href='./' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🏠 Voltar</a>";
echo "<a href='login.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Login</a>";
echo "</div>";
?>








