<?php
// Script de Download de Backup
// Verificar se foi solicitado um arquivo
if (isset($_GET['file']) && !empty($_GET['file'])) {
    $filename = $_GET['file'];
    
    // Verificar se o arquivo existe na pasta backups
    $filepath = 'backups/' . $filename;
    
    if (file_exists($filepath) && is_file($filepath)) {
        // Configurar headers para download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: 0');
        
        // Ler e enviar o arquivo
        readfile($filepath);
        exit;
    } else {
        echo "<h2>❌ Arquivo não encontrado!</h2>";
        echo "<p>O arquivo '$filename' não foi encontrado.</p>";
        echo "<p>Caminho tentado: $filepath</p>";
    }
} else {
    // Listar todos os backups disponíveis
    echo "<h1>📥 Download de Backups</h1>";
    echo "<p>Selecione um backup para download:</p>";
    
    $backup_dir = 'backups/';
    if (is_dir($backup_dir)) {
        $files = glob($backup_dir . '*.sql');
        
        if (empty($files)) {
            echo "<p>❌ Nenhum backup encontrado.</p>";
        } else {
            echo "<div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>";
            
            // Ordenar por data (mais recente primeiro)
            usort($files, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            foreach ($files as $file) {
                $filename = basename($file);
                $filesize = filesize($file);
                $filedate = date('d/m/Y H:i:s', filemtime($file));
                
                // Determinar status do arquivo
                $status = '';
                if ($filesize == 0) {
                    $status = '<span style="color: red;">❌ Vazio</span>';
                } elseif ($filesize < 1000) {
                    $status = '<span style="color: orange;">⚠️ Pequeno</span>';
                } else {
                    $status = '<span style="color: green;">✅ Completo</span>';
                }
                
                echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
                echo "<h3>📄 $filename</h3>";
                echo "<p><strong>Tamanho:</strong> " . number_format($filesize) . " bytes</p>";
                echo "<p><strong>Data:</strong> $filedate</p>";
                echo "<p><strong>Status:</strong> $status</p>";
                
                if ($filesize > 0) {
                    echo "<a href='download_backup.php?file=$filename' class='btn btn-primary'>📥 Download</a>";
                } else {
                    echo "<span style='color: red;'>❌ Arquivo vazio - não pode ser baixado</span>";
                }
                echo "</div>";
            }
            
            echo "</div>";
            
            // Recomendação
            echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
            echo "<h3>💡 Recomendação:</h3>";
            echo "<p><strong>Baixe o arquivo mais recente e maior:</strong></p>";
            echo "<p>📄 backup_sistema_agendamento_2025-08-29_03-39-59.sql (17,349 bytes)</p>";
            echo "<p>Este é o backup mais completo disponível!</p>";
            echo "</div>";
        }
    } else {
        echo "<p>❌ Pasta de backups não encontrada.</p>";
    }
}

echo "<hr>";
echo "<p><a href='dashboard_final.php'>← Voltar para Dashboard</a></p>";
echo "<p><a href='backup_completo_sistema.php'>← Criar Novo Backup</a></p>";
?>

<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 20px; 
    background: #f5f5f5; 
}
h1 { color: #2c3e50; }
h2 { color: #34495e; }
h3 { color: #7f8c8d; }
.btn { 
    display: inline-block; 
    padding: 10px 20px; 
    background: #3498db; 
    color: white; 
    text-decoration: none; 
    border-radius: 5px; 
    margin: 5px; 
}
.btn-primary { background: #3498db; }
.btn:hover { opacity: 0.8; }
</style>
