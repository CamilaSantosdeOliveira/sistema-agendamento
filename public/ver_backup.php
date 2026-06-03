<?php
// Script para visualizar o conteúdo da pasta de backup
echo "<h1>📁 Visualizar Conteúdo do Backup</h1>";

// Encontrar a pasta de backup mais recente
$backupFolders = glob(__DIR__ . '/backup_sistema_*');
if (empty($backupFolders)) {
    echo "<p style='color: red;'>❌ Nenhuma pasta de backup encontrada!</p>";
    exit;
}

// Pegar a pasta mais recente
$latestBackup = end($backupFolders);
$backupName = basename($latestBackup);

echo "<div style='background: #e7f3ff; border: 1px solid #b3d9ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h2 style='color: #0056b3; margin-top: 0;'>📦 Pasta de Backup: $backupName</h2>";
echo "<p><strong>📍 Caminho completo:</strong> $latestBackup</p>";
echo "</div>";

// Função para listar arquivos recursivamente
function listFiles($dir, $level = 0) {
    $files = scandir($dir);
    $output = "";
    
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $path = $dir . '/' . $file;
            $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
            
            if (is_dir($path)) {
                $output .= "<div style='margin: 5px 0;'>";
                $output .= "<span style='color: #007bff; font-weight: bold;'>📁 $indent$file/</span>";
                $output .= "</div>";
                $output .= listFiles($path, $level + 1);
            } else {
                $size = filesize($path);
                $sizeKB = round($size / 1024, 2);
                $output .= "<div style='margin: 2px 0; font-family: monospace;'>";
                $output .= "<span style='color: #28a745;'>📄 $indent$file</span>";
                $output .= "<span style='color: #6c757d; font-size: 0.9em;'> ($sizeKB KB)</span>";
                $output .= "</div>";
            }
        }
    }
    
    return $output;
}

// Listar arquivos da pasta de backup
echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>📋 Conteúdo da Pasta de Backup:</h3>";
echo "<div style='max-height: 500px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; background: white;'>";
echo listFiles($latestBackup);
echo "</div>";
echo "</div>";

// Estatísticas
$totalFiles = 0;
$totalDirs = 0;
$totalSize = 0;

function countFiles($dir) {
    global $totalFiles, $totalDirs, $totalSize;
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $path = $dir . '/' . $file;
            
            if (is_dir($path)) {
                $totalDirs++;
                countFiles($path);
            } else {
                $totalFiles++;
                $totalSize += filesize($path);
            }
        }
    }
}

countFiles($latestBackup);
$totalSizeMB = round($totalSize / 1024 / 1024, 2);

echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3 style='color: #155724; margin-top: 0;'>📊 Estatísticas do Backup:</h3>";
echo "<p><strong>📁 Pastas:</strong> $totalDirs</p>";
echo "<p><strong>📄 Arquivos:</strong> $totalFiles</p>";
echo "<p><strong>📏 Tamanho total:</strong> $totalSizeMB MB</p>";
echo "</div>";

// Links de navegação
echo "<div style='margin-top: 20px;'>";
echo "<a href='./' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🏠 Voltar ao Sistema</a>";
echo "<a href='login.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🔐 Ir para Login</a>";
echo "<a href='backup_sistema_rapido.php' style='background: #ffc107; color: #212529; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔄 Criar Novo Backup</a>";
echo "</div>";
?>








