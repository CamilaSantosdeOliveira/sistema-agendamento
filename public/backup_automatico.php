<?php
echo "<h1>💾 SISTEMA DE BACKUP AUTOMÁTICO</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} .warning{color:orange;font-weight:bold;} table{border-collapse:collapse;width:100%;margin:10px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background-color:#f2f2f2;}</style>";

// Configurações
$database = 'sistema_agendamento';
$backup_dir = 'backups/';
$timestamp = date('Y-m-d_H-i-s');

// Criar diretório de backup se não existir
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0777, true);
    echo "<div class='success'>✅ Diretório de backup criado: {$backup_dir}</div>";
}

// Nome do arquivo de backup
$backup_file = $backup_dir . "backup_{$database}_{$timestamp}.sql";

echo "<div class='info'>🕐 Iniciando backup em: " . date('d/m/Y H:i:s') . "</div>";
echo "<div class='info'>📁 Arquivo: {$backup_file}</div>";

// Tentar diferentes caminhos do mysqldump no Windows
$mysqldump_paths = [
    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
    'C:\\xampp\\mysql\\bin\\mysqldump',
    'mysqldump',
    'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe'
];

$command = null;
$mysqldump_found = false;

foreach ($mysqldump_paths as $path) {
    if (file_exists($path) || shell_exec("where {$path} 2>nul")) {
        $command = "\"{$path}\" -u root -p '' {$database} > \"{$backup_file}\" 2>&1";
        $mysqldump_found = true;
        echo "<div class='info'>✅ Encontrado mysqldump em: {$path}</div>";
        break;
    }
}

if (!$mysqldump_found) {
    echo "<div class='error'>❌ mysqldump não encontrado!</div>";
    echo "<div class='info'>🔄 Tentando método alternativo...</div>";
    
    // Método alternativo: usar PHP para fazer backup
    $conn = new mysqli('localhost', 'root', '', $database);
    if ($conn->connect_error) {
        echo "<div class='error'>❌ Erro na conexão: " . $conn->connect_error . "</div>";
        exit;
    }
    
    echo "<div class='info'>⚙️ Fazendo backup via PHP...</div>";
    
    // Obter estrutura das tabelas
    $backup_content = "-- Backup do Sistema de Agendamento\n";
    $backup_content .= "-- Data: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Listar tabelas
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    foreach ($tables as $table) {
        $backup_content .= "-- Estrutura da tabela `{$table}`\n";
        
        // Obter CREATE TABLE
        $result = $conn->query("SHOW CREATE TABLE `{$table}`");
        $row = $result->fetch_array();
        $backup_content .= $row[1] . ";\n\n";
        
        // Obter dados
        $result = $conn->query("SELECT * FROM `{$table}`");
        if ($result->num_rows > 0) {
            $backup_content .= "-- Dados da tabela `{$table}`\n";
            
            while ($row = $result->fetch_assoc()) {
                $columns = array_keys($row);
                $values = array_values($row);
                
                // Escapar valores
                $escaped_values = array_map(function($value) use ($conn) {
                    if ($value === null) return 'NULL';
                    return "'" . $conn->real_escape_string($value) . "'";
                }, $values);
                
                $backup_content .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $escaped_values) . ");\n";
            }
            $backup_content .= "\n";
        }
    }
    
    // Salvar arquivo
    if (file_put_contents($backup_file, $backup_content)) {
        echo "<div class='success'>✅ Backup realizado com sucesso via PHP!</div>";
    } else {
        echo "<div class='error'>❌ Erro ao salvar arquivo de backup!</div>";
        exit;
    }
    
    $conn->close();
    
} else {
    echo "<div class='info'>⚙️ Executando comando de backup...</div>";
    
    // Executar backup
    $output = [];
    $return_var = 0;
    exec($command, $output, $return_var);
    
    if ($return_var !== 0) {
        echo "<div class='error'>❌ Erro no backup!</div>";
        echo "<div class='error'>Comando: {$command}</div>";
        echo "<div class='error'>Saída: " . implode('<br>', $output) . "</div>";
        exit;
    }
    
    echo "<div class='success'>✅ Backup realizado com sucesso!</div>";
}

// Verificar tamanho do arquivo
if (file_exists($backup_file)) {
    $size = filesize($backup_file);
    $size_mb = round($size / 1024 / 1024, 2);
    echo "<div class='info'>📊 Tamanho do backup: {$size_mb} MB</div>";
}

// Listar backups existentes
echo "<h2>📋 Backups disponíveis:</h2>";
$backups = glob($backup_dir . "backup_{$database}_*.sql");

if (count($backups) > 0) {
    echo "<table>";
    echo "<tr><th>Data/Hora</th><th>Arquivo</th><th>Tamanho</th><th>Ações</th></tr>";
    
    // Ordenar por data (mais recente primeiro)
    rsort($backups);
    
    foreach ($backups as $backup) {
        $filename = basename($backup);
        $size = filesize($backup);
        $size_mb = round($size / 1024 / 1024, 2);
        
        // Extrair data do nome do arquivo
        if (preg_match('/backup_' . $database . '_(.+)\.sql/', $filename, $matches)) {
            $date_str = str_replace('_', ' ', $matches[1]);
            $date_obj = DateTime::createFromFormat('Y-m-d H-i-s', $date_str);
            $formatted_date = $date_obj ? $date_obj->format('d/m/Y H:i:s') : $date_str;
        } else {
            $formatted_date = 'Data desconhecida';
        }
        
        echo "<tr>";
        echo "<td>{$formatted_date}</td>";
        echo "<td>{$filename}</td>";
        echo "<td>{$size_mb} MB</td>";
        echo "<td>";
        echo "<a href='restaurar_backup.php?file=" . urlencode($filename) . "' style='background:blue;color:white;padding:5px;text-decoration:none;border-radius:3px;margin:2px;'>🔄 Restaurar</a>";
        echo "<a href='download_backup.php?file=" . urlencode($filename) . "' style='background:green;color:white;padding:5px;text-decoration:none;border-radius:3px;margin:2px;'>⬇️ Download</a>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Manter apenas os 10 backups mais recentes
echo "<h2>🧹 Limpeza automática:</h2>";
$backups = glob($backup_dir . "backup_{$database}_*.sql");
if (count($backups) > 10) {
    // Ordenar por data (mais antigo primeiro)
    sort($backups);
    
    $to_delete = array_slice($backups, 0, count($backups) - 10);
    foreach ($to_delete as $old_backup) {
        unlink($old_backup);
        echo "<div class='warning'>🗑️ Removido backup antigo: " . basename($old_backup) . "</div>";
    }
    echo "<div class='success'>✅ Mantidos apenas os 10 backups mais recentes</div>";
} else {
    echo "<div class='info'>ℹ️ Todos os backups serão mantidos (menos de 10)</div>";
}

echo "<h2>🎯 PRÓXIMOS PASSOS:</h2>";
echo "<p><strong>Para fazer backup manual:</strong></p>";
echo "<ul>";
echo "<li>🔄 <a href='backup_automatico.php'>Fazer backup agora</a></li>";
echo "<li>📊 <a href='dashboard_final.php'>Voltar ao Dashboard</a></li>";
echo "<li>🛠️ <a href='carregar_dados_final.php'>Recarregar dados</a></li>";
echo "</ul>";

echo "<p><strong>Dica:</strong> Execute este script sempre que fizer mudanças importantes no sistema!</p>";
?>
