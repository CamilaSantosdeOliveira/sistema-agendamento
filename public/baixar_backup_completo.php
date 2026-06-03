<?php
echo "<h1>📦 Baixar Backup COMPLETO (com Banco)</h1>";

// Backup que tem banco de dados
$backupCompleto = 'backup_direto_2025-08-30_04-01-27';
$backupPath = __DIR__ . '/' . $backupCompleto;

if (!is_dir($backupPath)) {
    echo "<p>❌ Pasta de backup completo não encontrada!</p>";
    echo "<a href='./'>🏠 Voltar</a>";
    exit;
}

echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h2>✅ Backup Completo Encontrado!</h2>";
echo "<p><strong>📁 Pasta:</strong> $backupCompleto</p>";
echo "<p><strong>🗄️ Banco de dados:</strong> ✅ INCLUÍDO</p>";
echo "</div>";

// Verificar arquivos importantes
$importantFiles = [
    'banco_direto.sql' => 'Banco de Dados',
    'restaurar_direto.php' => 'Script de Restauração',
    'login.php' => 'Sistema de Login',
    'dashboard_final.php' => 'Dashboard Admin',
    'dashboard_professor.php' => 'Dashboard Professor',
    'dashboard_aluno.php' => 'Dashboard Aluno'
];

echo "<h3>📋 Conteúdo do Backup:</h3>";
foreach ($importantFiles as $file => $description) {
    if (file_exists($backupPath . '/' . $file)) {
        $size = filesize($backupPath . '/' . $file);
        $sizeKB = round($size / 1024, 2);
        echo "<p>✅ <strong>$description:</strong> $file ($sizeKB KB)</p>";
    }
}

// Criar ZIP
$zipName = $backupCompleto . '_COMPLETO.zip';
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
        echo "<p><strong>🗄️ Banco de dados:</strong> ✅ Incluído</p>";
        echo "<p><strong>🔄 Restauração:</strong> ✅ Automática</p>";
        echo "</div>";
        
        echo "<div style='text-align: center; margin: 30px 0;'>";
        echo "<a href='$zipName' download style='background: #28a745; color: white; padding: 20px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px;'>";
        echo "⬇️ Baixar Backup COMPLETO ($zipSizeMB MB)";
        echo "</a>";
        echo "</div>";
        
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
        echo "<h4>📖 Como usar:</h4>";
        echo "<ol>";
        echo "<li>Baixe o ZIP</li>";
        echo "<li>Extraia em uma pasta</li>";
        echo "<li>Acesse <strong>restaurar_direto.php</strong></li>";
        echo "<li>Pronto! Sistema restaurado</li>";
        echo "</ol>";
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








