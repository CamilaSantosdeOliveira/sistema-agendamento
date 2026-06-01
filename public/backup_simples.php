<?php
// Backup Simples - Teste
echo "<h1>🔄 Teste de Backup Simples</h1>";

// Testar conexão
include 'db.php';
if (!$conn) {
    die("❌ Erro de conexão com banco de dados");
}
echo "<p>✅ Conexão com banco OK</p>";

// Testar escrita na pasta
$test_file = 'backups/teste_backup.txt';
$test_content = "Teste de backup - " . date('Y-m-d H:i:s');

if (file_put_contents($test_file, $test_content)) {
    echo "<p>✅ Escrita na pasta backups OK</p>";
    echo "<p>📄 Arquivo criado: $test_file</p>";
} else {
    echo "<p>❌ Erro ao escrever na pasta backups</p>";
}

// Testar tabela certificados
$result = $conn->query("SHOW TABLES LIKE 'certificados'");
if ($result && $result->num_rows > 0) {
    echo "<p>✅ Tabela certificados existe</p>";
    
    // Contar certificados
    $count = $conn->query("SELECT COUNT(*) as total FROM certificados");
    $total = $count->fetch_assoc()['total'];
    echo "<p>📊 Total de certificados: $total</p>";
} else {
    echo "<p>❌ Tabela certificados não existe</p>";
}

// Criar backup simples
$backup_file = 'backups/backup_simples_' . date('Y-m-d_H-i-s') . '.sql';
$backup_content = "-- Backup Simples\n";
$backup_content .= "-- Data: " . date('Y-m-d H:i:s') . "\n\n";

// Adicionar estrutura da tabela certificados
$structure = $conn->query("SHOW CREATE TABLE certificados");
if ($structure) {
    $row = $structure->fetch_assoc();
    $backup_content .= $row['Create Table'] . ";\n\n";
    echo "<p>✅ Estrutura da tabela certificados exportada</p>";
}

// Adicionar dados dos certificados
$certificados = $conn->query("SELECT * FROM certificados");
if ($certificados && $certificados->num_rows > 0) {
    $backup_content .= "-- Dados dos certificados\n";
    while ($row = $certificados->fetch_assoc()) {
        $backup_content .= "INSERT INTO certificados VALUES (";
        $values = [];
        foreach ($row as $value) {
            if ($value === null) {
                $values[] = 'NULL';
            } else {
                $values[] = "'" . addslashes($value) . "'";
            }
        }
        $backup_content .= implode(', ', $values) . ");\n";
    }
    echo "<p>✅ Dados dos certificados exportados</p>";
}

// Salvar backup
if (file_put_contents($backup_file, $backup_content)) {
    echo "<h2>✅ Backup Simples Criado!</h2>";
    echo "<p>📄 Arquivo: $backup_file</p>";
    echo "<p>📏 Tamanho: " . number_format(filesize($backup_file)) . " bytes</p>";
    echo "<p><a href='download_backup.php?file=" . basename($backup_file) . "'>📥 Download</a></p>";
} else {
    echo "<h2>❌ Erro ao salvar backup</h2>";
}

$conn->close();
echo "<hr>";
echo "<p><a href='dashboard_final.php'>← Voltar para Dashboard</a></p>";
?>
