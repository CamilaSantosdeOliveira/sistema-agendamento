<?php
// Função para fazer backup automático
function fazerBackupAutomatico() {
    $database = 'sistema_agendamento';
    $backup_dir = 'backups/';
    $timestamp = date('Y-m-d_H-i-s');
    
    // Criar diretório se não existir
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }
    
    // Nome do arquivo
    $backup_file = $backup_dir . "backup_auto_{$database}_{$timestamp}.sql";
    
    // Conectar ao banco
    $conn = new mysqli('localhost', 'root', '', $database);
    if ($conn->connect_error) {
        return false; // Erro na conexão
    }
    
    // Iniciar backup
    $backup_content = "-- Backup Automático do Sistema\n";
    $backup_content .= "-- Data: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Listar tabelas
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    foreach ($tables as $table) {
        // Estrutura da tabela
        $result = $conn->query("SHOW CREATE TABLE `{$table}`");
        $row = $result->fetch_array();
        $backup_content .= "-- Estrutura da tabela `{$table}`\n";
        $backup_content .= $row[1] . ";\n\n";
        
        // Dados da tabela
        $result = $conn->query("SELECT * FROM `{$table}`");
        if ($result->num_rows > 0) {
            $backup_content .= "-- Dados da tabela `{$table}`\n";
            
            while ($row = $result->fetch_assoc()) {
                $columns = array_keys($row);
                $values = array_values($row);
                
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
    $success = file_put_contents($backup_file, $backup_content);
    $conn->close();
    
    return $success ? $backup_file : false;
}

// Exemplo de uso:
// include 'backup_automatico_script.php';
// $backup_file = fazerBackupAutomatico();
// if ($backup_file) {
//     echo "Backup automático criado: " . basename($backup_file);
// }
?>









