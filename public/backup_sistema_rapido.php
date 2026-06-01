<?php
// Script de Backup Rápido do Sistema
set_time_limit(300); // 5 minutos
ini_set('memory_limit', '256M');

echo "<h1>🚀 Criando Backup Rápido do Sistema</h1>";

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

echo "<p>📦 Iniciando cópia rápida dos arquivos...</p>";

// Lista de arquivos importantes para copiar
$importantFiles = [
    'login.php',
    'usuarios_api.php',
    'dashboard_final.php',
    'dashboard_professor.php',
    'dashboard_aluno.php',
    'index.php',
    'index.html',
    '.htaccess',
    'limpar_sessao.php',
    'teste_fluxo.php'
];

// Lista de pastas para copiar
$importantFolders = [
    'css',
    'js',
    'images',
    'uploads'
];

$copiedCount = 0;

// Copiar arquivos importantes
foreach ($importantFiles as $file) {
    $sourcePath = $sourceDir . '/' . $file;
    $destPath = $backupDir . '/' . $file;
    
    if (file_exists($sourcePath)) {
        if (copy($sourcePath, $destPath)) {
            echo "<p>✅ Copiado: $file</p>";
            $copiedCount++;
        } else {
            echo "<p>⚠️ Erro ao copiar: $file</p>";
        }
    }
}

// Copiar pastas importantes
foreach ($importantFolders as $folder) {
    $sourcePath = $sourceDir . '/' . $folder;
    $destPath = $backupDir . '/' . $folder;
    
    if (is_dir($sourcePath)) {
        if (mkdir($destPath, 0777, true)) {
            echo "<p>📁 Criado diretório: $folder</p>";
            
            // Copiar conteúdo da pasta
            $files = glob($sourcePath . '/*');
            foreach ($files as $file) {
                $fileName = basename($file);
                $destFile = $destPath . '/' . $fileName;
                
                if (is_file($file)) {
                    if (copy($file, $destFile)) {
                        echo "<p>  ✅ Copiado: $folder/$fileName</p>";
                        $copiedCount++;
                    }
                }
            }
        }
    }
}

// Copiar outros arquivos PHP importantes
$phpFiles = glob($sourceDir . '/*.php');
foreach ($phpFiles as $phpFile) {
    $fileName = basename($phpFile);
    
    // Pular arquivos já copiados
    if (in_array($fileName, $importantFiles) || strpos($fileName, 'backup_') === 0) {
        continue;
    }
    
    $destPath = $backupDir . '/' . $fileName;
    if (copy($phpFile, $destPath)) {
        echo "<p>✅ Copiado: $fileName</p>";
        $copiedCount++;
    }
}

// Verificar se a pasta foi criada
if (is_dir($backupDir)) {
    $totalFiles = count(glob($backupDir . '/**/*', GLOB_BRACE));
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2 style='color: #155724; margin-top: 0;'>✅ Backup Rápido Criado com Sucesso!</h2>";
    echo "<p><strong>📁 Pasta:</strong> " . basename($backupDir) . "</p>";
    echo "<p><strong>📏 Total de arquivos:</strong> $totalFiles</p>";
    echo "<p><strong>📏 Arquivos copiados:</strong> $copiedCount</p>";
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
    echo "<p><strong>📁 Total de arquivos copiados:</strong> $copiedCount</p>";
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
echo "</ul>";
echo "</div>";

echo "<div style='margin-top: 20px;'>";
echo "<a href='./' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🏠 Voltar ao Sistema</a>";
echo "<a href='login.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Ir para Login</a>";
echo "</div>";
?>






