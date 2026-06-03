<?php
// Ver Backup Direto - Sem redirecionamento
echo "<h1>📁 Backup Direto - Visualizar</h1>";

// Encontrar pasta de backup direto
$backupFolders = [];
$items = scandir(__DIR__);
foreach ($items as $item) {
    if ($item != '.' && $item != '..' && is_dir(__DIR__ . '/' . $item) && strpos($item, 'backup_direto_') === 0) {
        $backupFolders[] = __DIR__ . '/' . $item;
    }
}

if (empty($backupFolders)) {
    echo "<p style='color: red;'>❌ Nenhuma pasta de backup direto encontrada!</p>";
    echo "<a href='./'>🏠 Voltar</a>";
    exit;
}

// Pegar a mais recente
$latestBackup = end($backupFolders);
$backupName = basename($latestBackup);

echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h2>✅ Backup Direto Encontrado!</h2>";
echo "<p><strong>📁 Pasta:</strong> $backupName</p>";
echo "<p><strong>📍 Caminho:</strong> $latestBackup</p>";
echo "</div>";

// Listar arquivos principais
echo "<h3>📋 Arquivos Principais:</h3>";
$mainFiles = [
    'login.php', 'dashboard_final.php', 'dashboard_professor.php', 
    'dashboard_aluno.php', 'banco_direto.sql', 'restaurar_direto.php',
    'README_DIRETO.txt', 'usuarios_api.php', 'admin_api.php', 'api.php'
];

$totalFiles = 0;
foreach ($mainFiles as $file) {
    $filePath = $latestBackup . '/' . $file;
    if (file_exists($filePath)) {
        $size = filesize($filePath);
        $sizeKB = round($size / 1024, 2);
        echo "<p>✅ $file ($sizeKB KB)</p>";
        $totalFiles++;
    }
}

// Estatísticas
$allFiles = scandir($latestBackup);
$totalAllFiles = count($allFiles) - 2; // -2 para . e ..

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>📊 Resumo:</h3>";
echo "<p><strong>Total de arquivos:</strong> $totalAllFiles</p>";
echo "<p><strong>Arquivos principais:</strong> $totalFiles</p>";
echo "<p><strong>Banco de dados:</strong> ✅ Incluído</p>";
echo "<p><strong>Script de restauração:</strong> ✅ Incluído</p>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='$backupName/restaurar_direto.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin-right: 10px;'>";
echo "🔄 Restaurar Backup";
echo "</a>";
echo "<a href='$backupName/banco_direto.sql' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold;'>";
echo "🗄️ Ver Banco SQL";
echo "</a>";
echo "</div>";

echo "<div style='margin-top: 20px;'>";
echo "<a href='./' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Voltar</a>";
echo "</div>";
?>








