<?php
echo "<h1>🔍 Verificar Backup Completo</h1>";

// Encontrar pasta de backup mais recente
$items = scandir(__DIR__);
$backupFolders = [];

foreach ($items as $item) {
    if ($item != '.' && $item != '..' && is_dir(__DIR__ . '/' . $item) && strpos($item, 'backup_') === 0) {
        $backupFolders[] = $item;
    }
}

if (empty($backupFolders)) {
    echo "<p>❌ Nenhuma pasta de backup encontrada!</p>";
    echo "<a href='./'>🏠 Voltar</a>";
    exit;
}

// Pegar a mais recente
$latestBackup = end($backupFolders);
$backupPath = __DIR__ . '/' . $latestBackup;

echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h2>✅ Backup Mais Recente: $latestBackup</h2>";
echo "</div>";

// Verificar arquivos de banco de dados
$databaseFiles = [
    'banco_direto.sql',
    'banco_final.sql', 
    'banco_completo.sql',
    'backup_banco.sql',
    'database.sql'
];

$foundDatabase = false;
$databaseFile = '';

foreach ($databaseFiles as $file) {
    if (file_exists($backupPath . '/' . $file)) {
        $size = filesize($backupPath . '/' . $file);
        $sizeKB = round($size / 1024, 2);
        echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>🗄️ Banco de Dados ENCONTRADO!</h3>";
        echo "<p>✅ <strong>$file</strong> ($sizeKB KB)</p>";
        echo "</div>";
        $foundDatabase = true;
        $databaseFile = $file;
        break;
    }
}

if (!$foundDatabase) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Banco de Dados NÃO encontrado!</h3>";
    echo "<p>Este backup NÃO contém banco de dados!</p>";
    echo "</div>";
}

// Verificar arquivos principais
$mainFiles = [
    'login.php',
    'dashboard_final.php',
    'dashboard_professor.php',
    'dashboard_aluno.php',
    'restaurar_direto.php',
    'restaurar_final.php',
    'restaurar_completo.php'
];

echo "<h3>📋 Arquivos Principais:</h3>";
$foundMainFiles = 0;

foreach ($mainFiles as $file) {
    if (file_exists($backupPath . '/' . $file)) {
        $size = filesize($backupPath . '/' . $file);
        $sizeKB = round($size / 1024, 2);
        echo "<p>✅ $file ($sizeKB KB)</p>";
        $foundMainFiles++;
    }
}

// Contar total de arquivos
$allFiles = scandir($backupPath);
$totalFiles = count($allFiles) - 2; // -2 para . e ..

echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
echo "<h3>📊 Resumo:</h3>";
echo "<p><strong>Total de arquivos:</strong> $totalFiles</p>";
echo "<p><strong>Arquivos principais:</strong> $foundMainFiles</p>";
echo "<p><strong>Banco de dados:</strong> " . ($foundDatabase ? "✅ SIM" : "❌ NÃO") . "</p>";
echo "</div>";

if ($foundDatabase) {
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2>🎉 BACKUP COMPLETO!</h2>";
    echo "<p>✅ Tem TUDO: arquivos + banco de dados</p>";
    echo "<p>✅ Pode restaurar completamente</p>";
    echo "</div>";
    
    echo "<div style='text-align: center; margin: 30px 0;'>";
    echo "<a href='$latestBackup/$databaseFile' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin-right: 10px;'>";
    echo "🗄️ Ver Banco SQL";
    echo "</a>";
    echo "<a href='$latestBackup/restaurar_direto.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold;'>";
    echo "🔄 Restaurar";
    echo "</a>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2>⚠️ BACKUP INCOMPLETO!</h2>";
    echo "<p>❌ Só tem arquivos, NÃO tem banco de dados</p>";
    echo "<p>❌ NÃO pode restaurar completamente</p>";
    echo "</div>";
}

echo "<div style='margin-top: 20px;'>";
echo "<a href='./' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Voltar</a>";
echo "</div>";
?>

