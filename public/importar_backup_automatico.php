<?php
echo "<h1>🔄 Importação Automática do Backup</h1>";

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sistema_agendamento';

try {
    // Conectar ao MySQL
    echo "<p>🔄 Conectando ao MySQL...</p>";
    $conn = new mysqli($host, $user, $pass, '', 3306);
    
    if ($conn->connect_error) {
        throw new Exception('Erro de conexão: ' . $conn->connect_error);
    }
    
    echo "<p>✅ <strong>MySQL conectado!</strong></p>";
    
    // Selecionar o banco
    $conn->select_db($db);
    
    // Verificar se existem tabelas
    $tables = $conn->query("SHOW TABLES");
    $table_count = $tables ? $tables->num_rows : 0;
    
    echo "<p>📋 <strong>Tabelas encontradas:</strong> $table_count</p>";
    
    if ($table_count == 0) {
        echo "<p>⚠️ <strong>Nenhuma tabela encontrada!</strong></p>";
        echo "<p>🔄 Importando backup automaticamente...</p>";
        
        // Ler o arquivo de backup
        $backup_file = 'backups/backup_completo_corrigido_2025-08-29_07-35-39.sql';
        
        if (file_exists($backup_file)) {
            echo "<p>📁 <strong>Arquivo de backup encontrado:</strong> $backup_file</p>";
            
            $sql = file_get_contents($backup_file);
            
            // Dividir em comandos SQL
            $commands = explode(';', $sql);
            
            $success_count = 0;
            $error_count = 0;
            
            foreach ($commands as $command) {
                $command = trim($command);
                if (!empty($command) && !preg_match('/^--/', $command)) {
                    if ($conn->query($command)) {
                        $success_count++;
                    } else {
                        $error_count++;
                        echo "<p>❌ Erro no comando: " . $conn->error . "</p>";
                    }
                }
            }
            
            echo "<p>✅ <strong>Importação concluída!</strong></p>";
            echo "<p>📊 Comandos executados: $success_count</p>";
            echo "<p>❌ Erros: $error_count</p>";
            
            // Verificar tabelas novamente
            $tables = $conn->query("SHOW TABLES");
            $new_table_count = $tables ? $tables->num_rows : 0;
            echo "<p>📋 <strong>Novas tabelas criadas:</strong> $new_table_count</p>";
            
        } else {
            echo "<p>❌ <strong>Arquivo de backup não encontrado!</strong></p>";
            echo "<p>📁 Procurando por outros backups...</p>";
            
            $backup_files = glob('backups/*.sql');
            if (!empty($backup_files)) {
                echo "<p>📋 <strong>Backups disponíveis:</strong></p>";
                echo "<ul>";
                foreach ($backup_files as $file) {
                    echo "<li><a href='$file' target='_blank'>$file</a></li>";
                }
                echo "</ul>";
            } else {
                echo "<p>❌ <strong>Nenhum backup encontrado!</strong></p>";
            }
        }
        
    } else {
        echo "<p>✅ <strong>Banco já tem tabelas!</strong></p>";
        echo "<ul>";
        while ($table = $tables->fetch_array()) {
            $table_name = $table[0];
            echo "<li>$table_name</li>";
        }
        echo "</ul>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Próximos Passos:</h2>";
echo "<p><a href='index.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Testar Sistema</a></p>";
echo "<p><a href='http://localhost:8080/phpmyadmin' target='_blank'>🗄️ Ver no phpMyAdmin</a></p>";
?>








