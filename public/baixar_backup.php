<?php
echo "<h1>📦 Baixar Backup Completo</h1>";

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
echo "<h2>✅ Backup Encontrado!</h2>";
echo "<p><strong>📁 Pasta:</strong> $latestBackup</p>";
echo "<p><strong>📍 Caminho:</strong> $backupPath</p>";
echo "</div>";

// Verificar se tem arquivos importantes
$importantFiles = ['banco_direto.sql', 'restaurar_direto.php', 'login.php', 'dashboard_final.php'];
$foundFiles = [];

foreach ($importantFiles as $file) {
    if (file_exists($backupPath . '/' . $file)) {
        $size = filesize($backupPath . '/' . $file);
        $sizeKB = round($size / 1024, 2);
        $foundFiles[] = "$file ($sizeKB KB)";
    }
}

echo "<h3>📋 Arquivos Incluídos:</h3>";
foreach ($foundFiles as $file) {
    echo "<p>✅ $file</p>";
}

// Criar ZIP
$zipName = $latestBackup . '.zip';
$zipPath = __DIR__ . '/' . $zipName;

if (class_exists('ZipArchive')) {
    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        
        // Adicionar todos os arquivos da pasta
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($backupPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($backupPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        
        $zip->close();
        
        $zipSize = filesize($zipPath);
        $zipSizeMB = round($zipSize / (1024 * 1024), 2);
        
        echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h3>📦 ZIP Criado com Sucesso!</h3>";
        echo "<p><strong>📁 Arquivo:</strong> $zipName</p>";
        echo "<p><strong>📏 Tamanho:</strong> $zipSizeMB MB</p>";
        echo "<p><strong>📄 Arquivos:</strong> " . count($foundFiles) . " principais</p>";
        echo "</div>";
        
        echo "<div style='text-align: center; margin: 30px 0;'>";
        echo "<a href='$zipName' download style='background: #28a745; color: white; padding: 20px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px;'>";
        echo "⬇️ Baixar Backup Completo ($zipSizeMB MB)";
        echo "</a>";
        echo "</div>";
        
    } else {
        echo "<p style='color: red;'>❌ Erro ao criar ZIP!</p>";
    }
} else {
    echo "<p style='color: red;'>❌ ZipArchive não disponível!</p>";
}

echo "<div style='margin-top: 20px;'>";
echo "<a href='./' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Voltar</a>";
echo "</div>";
?>








