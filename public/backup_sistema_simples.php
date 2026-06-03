<?php
// Script de Backup Simples do Sistema (sem ZIP)
echo "<h1>🔄 Criando Backup Simples do Sistema</h1>";

// Configurações
$sourceDir = __DIR__;
$backupDir = __DIR__ . '/backup_sistema_' . date('Y-m-d_H-i-s');

echo "<p><strong>📁 Diretório fonte:</strong> $sourceDir</p>";
echo "<p><strong>📁 Pasta de backup:</strong> $backupDir</p>";

// Verificar se o diretório existe
if (!is_dir($sourceDir)) {
    echo "<p style='color: red;'>❌ Erro: Diretório fonte não encontrado!</p>";
    exit;
}

// Criar pasta de backup
if (!mkdir($backupDir, 0777, true)) {
    echo "<p style='color: red;'>❌ Erro: Não foi possível criar a pasta de backup!</p>";
    exit;
}

echo "<p>📦 Iniciando cópia dos arquivos...</p>";

// Função recursiva para copiar arquivos
function copyFolder($source, $destination) {
    $handle = opendir($source);
    while (false !== $f = readdir($handle)) {
        if ($f != '.' && $f != '..' && $f != 'backup_sistema_simples.php' && $f != 'backup_sistema_final.php') {
            $sourcePath = "$source/$f";
            $destPath = "$destination/$f";
            
            if (is_file($sourcePath)) {
                if (copy($sourcePath, $destPath)) {
                    echo "<p>✅ Copiado: $f</p>";
                } else {
                    echo "<p>⚠️ Erro ao copiar: $f</p>";
                }
            } elseif (is_dir($sourcePath)) {
                if (mkdir($destPath, 0777, true)) {
                    echo "<p>📁 Criado diretório: $f</p>";
                    copyFolder($sourcePath, $destPath);
                } else {
                    echo "<p>⚠️ Erro ao criar diretório: $f</p>";
                }
            }
        }
    }
    closedir($handle);
}

// Copiar todos os arquivos
copyFolder($sourceDir, $backupDir);

// Verificar se a pasta foi criada
if (is_dir($backupDir)) {
    $fileCount = count(glob($backupDir . '/**/*', GLOB_BRACE));
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2 style='color: #155724; margin-top: 0;'>✅ Backup Simples Criado com Sucesso!</h2>";
    echo "<p><strong>📁 Pasta:</strong> " . basename($backupDir) . "</p>";
    echo "<p><strong>📏 Total de arquivos:</strong> $fileCount</p>";
    echo "<p><strong>📍 Localização:</strong> " . dirname($backupDir) . "</p>";
    echo "</div>";
    
    // Link para acessar a pasta
    echo "<div style='text-align: center; margin: 30px 0;'>";
    echo "<a href='" . basename($backupDir) . "/' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px;'>";
    echo "📁 Acessar Pasta de Backup";
    echo "</a>";
    echo "</div>";
    
    // Informações do sistema
    echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>📊 Informações do Sistema</h3>";
    echo "<p><strong>📅 Data do backup:</strong> " . date('d/m/Y H:i:s') . "</p>";
    echo "<p><strong>🖥️ Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
    echo "<p><strong>🐘 PHP:</strong> " . phpversion() . "</p>";
    echo "<p><strong>📁 Total de arquivos copiados:</strong> $fileCount</p>";
    echo "</div>";
    
} else {
    echo "<p style='color: red;'>❌ Erro: Falha ao criar a pasta de backup!</p>";
}

echo "<div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 8px;'>";
echo "<h3>📋 Conteúdo do Backup:</h3>";
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
echo "<a href='./' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🏠 Voltar ao Sistema</a>";
echo "<a href='login.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Ir para Login</a>";
echo "</div>";
?>








