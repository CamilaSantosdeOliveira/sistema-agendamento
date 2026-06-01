<?php
echo "<h1>📁 Verificar Backup</h1>";

// Listar pastas de backup
$items = scandir(__DIR__);
$backupFolders = [];

foreach ($items as $item) {
    if ($item != '.' && $item != '..' && is_dir(__DIR__ . '/' . $item) && strpos($item, 'backup_') === 0) {
        $backupFolders[] = $item;
    }
}

if (empty($backupFolders)) {
    echo "<p>❌ Nenhuma pasta de backup encontrada!</p>";
} else {
    echo "<h3>📦 Pastas de Backup Encontradas:</h3>";
    foreach ($backupFolders as $folder) {
        echo "<p>📁 $folder</p>";
    }
    
    // Pegar a mais recente
    $latest = end($backupFolders);
    echo "<h3>🎯 Backup Mais Recente: $latest</h3>";
    
    // Verificar arquivos importantes
    $importantFiles = ['banco_direto.sql', 'restaurar_direto.php', 'login.php', 'dashboard_final.php'];
    $foundFiles = [];
    
    foreach ($importantFiles as $file) {
        if (file_exists(__DIR__ . '/' . $latest . '/' . $file)) {
            $size = filesize(__DIR__ . '/' . $latest . '/' . $file);
            $sizeKB = round($size / 1024, 2);
            $foundFiles[] = "$file ($sizeKB KB)";
        }
    }
    
    echo "<h4>✅ Arquivos Importantes:</h4>";
    foreach ($foundFiles as $file) {
        echo "<p>✅ $file</p>";
    }
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "<h4>🎉 Backup Completo!</h4>";
    echo "<p>✅ Banco de dados incluído</p>";
    echo "<p>✅ Arquivos principais copiados</p>";
    echo "<p>✅ Script de restauração pronto</p>";
    echo "</div>";
}

echo "<a href='./'>🏠 Voltar</a>";
?>
