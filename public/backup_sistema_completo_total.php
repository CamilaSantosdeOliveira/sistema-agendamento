<?php
// Script de Backup Completo de Todo o Sistema
echo "<h1>🔄 Criando Backup Completo de Todo o Sistema</h1>";

// Configurações
$sourceDir = __DIR__ . '/Sistema De Agendamento';
$backupName = 'backup_sistema_completo_total_' . date('Y-m-d_H-i-s') . '.zip';
$backupPath = __DIR__ . '/' . $backupName;

echo "<p><strong>📁 Diretório fonte:</strong> $sourceDir</p>";
echo "<p><strong>📦 Arquivo de backup:</strong> $backupName</p>";

// Verificar se o diretório existe
if (!is_dir($sourceDir)) {
    echo "<p style='color: red;'>❌ Erro: Diretório fonte não encontrado!</p>";
    exit;
}

// Criar backup usando ZipArchive
$zip = new ZipArchive();
if ($zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    
    echo "<p>📦 Iniciando criação do backup completo...</p>";
    
    // Função recursiva para adicionar arquivos
    function addFolderToZip($folder, $zipArchive, $exclusiveLength) {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                $localPath = substr($filePath, $exclusiveLength);
                
                if (is_file($filePath)) {
                    $zipArchive->addFile($filePath, $localPath);
                    echo "<p>✅ Adicionado: $localPath</p>";
                } elseif (is_dir($filePath)) {
                    $zipArchive->addEmptyDir($localPath);
                    echo "<p>📁 Adicionado diretório: $localPath</p>";
                    addFolderToZip($filePath, $zipArchive, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }
    
    // Adicionar todos os arquivos
    addFolderToZip($sourceDir, $zip, strlen($sourceDir . '/'));
    
    $zip->close();
    
    // Verificar se o arquivo foi criado
    if (file_exists($backupPath)) {
        $fileSize = filesize($backupPath);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h2 style='color: #155724; margin-top: 0;'>✅ Backup Completo Criado com Sucesso!</h2>";
        echo "<p><strong>📦 Arquivo:</strong> $backupName</p>";
        echo "<p><strong>📏 Tamanho:</strong> $fileSizeMB MB</p>";
        echo "<p><strong>📍 Localização:</strong> " . dirname($backupPath) . "</p>";
        echo "</div>";
        
        // Link para download
        echo "<div style='text-align: center; margin: 30px 0;'>";
        echo "<a href='$backupName' download style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px;'>";
        echo "⬇️ Download do Backup Completo";
        echo "</a>";
        echo "</div>";
        
        // Informações do sistema
        echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h3>📊 Informações do Sistema</h3>";
        echo "<p><strong>📅 Data do backup:</strong> " . date('d/m/Y H:i:s') . "</p>";
        echo "<p><strong>🖥️ Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
        echo "<p><strong>🐘 PHP:</strong> " . phpversion() . "</p>";
        echo "<p><strong>📁 Total de arquivos:</strong> " . count(glob($sourceDir . '/**/*', GLOB_BRACE)) . "</p>";
        echo "</div>";
        
    } else {
        echo "<p style='color: red;'>❌ Erro: Falha ao criar o arquivo de backup!</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Erro: Não foi possível criar o arquivo ZIP!</p>";
}

echo "<div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 8px;'>";
echo "<h3>📋 Conteúdo Completo do Backup:</h3>";
echo "<ul>";
echo "<li>✅ <strong>Dashboard Administrador</strong> - Sistema completo de administração</li>";
echo "<li>✅ <strong>Dashboard Professor</strong> - Gestão de cursos e alunos</li>";
echo "<li>✅ <strong>Dashboard Aluno</strong> - Acesso aos cursos e certificados</li>";
echo "<li>✅ <strong>Sistema de Login</strong> - Autenticação para todos os usuários</li>";
echo "<li>✅ <strong>APIs e Funcionalidades</strong> - Todas as funcionalidades do sistema</li>";
echo "<li>✅ <strong>Arquivos de Estilo</strong> - CSS e JavaScript</li>";
echo "<li>✅ <strong>Scripts de Backup</strong> - Ferramentas de diagnóstico</li>";
echo "<li>✅ <strong>Configurações</strong> - Arquivos de configuração do sistema</li>";
echo "<li>✅ <strong>Banco de Dados</strong> - Scripts SQL e estrutura</li>";
echo "</ul>";
echo "</div>";

echo "<div style='margin-top: 20px;'>";
echo "<a href='Sistema De Agendamento/public/' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🏠 Voltar ao Sistema</a>";
echo "<a href='Sistema De Agendamento/public/login.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Ir para Login</a>";
echo "</div>";
?>








