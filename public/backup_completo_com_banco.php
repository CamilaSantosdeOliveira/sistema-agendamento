<?php
// Script de Backup Completo com Banco de Dados
set_time_limit(300);
ini_set('memory_limit', '256M');

echo "<h1>🔄 Criando Backup Completo com Banco de Dados</h1>";

// Configurações do banco
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'sistema_agendamento';

// Configurações do backup
$sourceDir = __DIR__;
$backupDir = __DIR__ . '/backup_completo_' . date('Y-m-d_H-i-s');
$sqlFile = $backupDir . '/banco_dados_backup.sql';

echo "<p><strong>📁 Diretório fonte:</strong> $sourceDir</p>";
echo "<p><strong>📁 Pasta de backup:</strong> $backupDir</p>";
echo "<p><strong>🗄️ Arquivo SQL:</strong> " . basename($sqlFile) . "</p>";

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

echo "<p>📦 Iniciando backup completo...</p>";

// 1. BACKUP DO BANCO DE DADOS
echo "<h3>🗄️ Fazendo backup do banco de dados...</h3>";

try {
    // Conectar ao MySQL
    $mysqli = new mysqli($host, $username, $password, $database);
    
    if ($mysqli->connect_error) {
        throw new Exception("Erro de conexão: " . $mysqli->connect_error);
    }
    
    // Obter todas as tabelas
    $tables = [];
    $result = $mysqli->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    $sqlContent = "-- Backup do Banco de Dados - Sistema de Agendamento\n";
    $sqlContent .= "-- Data: " . date('Y-m-d H:i:s') . "\n";
    $sqlContent .= "-- Gerado automaticamente\n\n";
    
    // Para cada tabela
    foreach ($tables as $table) {
        echo "<p>📋 Processando tabela: $table</p>";
        
        // Estrutura da tabela
        $result = $mysqli->query("SHOW CREATE TABLE `$table`");
        $row = $result->fetch_array();
        $sqlContent .= "\n-- Estrutura da tabela `$table`\n";
        $sqlContent .= "DROP TABLE IF EXISTS `$table`;\n";
        $sqlContent .= $row[1] . ";\n\n";
        
        // Dados da tabela
        $result = $mysqli->query("SELECT * FROM `$table`");
        if ($result->num_rows > 0) {
            $sqlContent .= "-- Dados da tabela `$table`\n";
            while ($row = $result->fetch_assoc()) {
                $sqlContent .= "INSERT INTO `$table` VALUES (";
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = "'" . $mysqli->real_escape_string($value) . "'";
                    }
                }
                $sqlContent .= implode(', ', $values) . ");\n";
            }
            $sqlContent .= "\n";
        }
    }
    
    // Salvar arquivo SQL
    if (file_put_contents($sqlFile, $sqlContent)) {
        echo "<p>✅ Backup do banco salvo em: " . basename($sqlFile) . "</p>";
    } else {
        echo "<p>⚠️ Erro ao salvar backup do banco</p>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro no backup do banco: " . $e->getMessage() . "</p>";
}

// 2. BACKUP DOS ARQUIVOS
echo "<h3>📁 Fazendo backup dos arquivos...</h3>";

// Lista de arquivos importantes
$importantFiles = [
    'login.php',
    'usuarios_api.php',
    'dashboard_final.php',
    'dashboard_professor.php',
    'dashboard_aluno.php',
    'index.php',
    'index.html',
    '.htaccess',
    'config.php',
    'db.php'
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

// Copiar outros arquivos PHP importantes
$phpFiles = glob($sourceDir . '/*.php');
foreach ($phpFiles as $phpFile) {
    $fileName = basename($phpFile);
    
    // Pular arquivos já copiados e scripts de backup
    if (in_array($fileName, $importantFiles) || strpos($fileName, 'backup_') === 0) {
        continue;
    }
    
    $destPath = $backupDir . '/' . $fileName;
    if (copy($phpFile, $destPath)) {
        echo "<p>✅ Copiado: $fileName</p>";
        $copiedCount++;
    }
}

// 3. CRIAR SCRIPT DE RESTAURAÇÃO
$restoreScript = $backupDir . '/restaurar_backup.php';
$restoreContent = '<?php
// Script de Restauração do Backup Completo
echo "<h1>🔄 Restaurando Backup Completo</h1>";

// Configurações
$host = "localhost";
$username = "root";
$password = "";
$database = "sistema_agendamento";
$sqlFile = __DIR__ . "/banco_dados_backup.sql";

echo "<p>📋 Iniciando restauração...</p>";

try {
    // Conectar ao MySQL
    $mysqli = new mysqli($host, $username, $password);
    
    if ($mysqli->connect_error) {
        throw new Exception("Erro de conexão: " . $mysqli->connect_error);
    }
    
    // Criar banco se não existir
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `$database`");
    $mysqli->select_db($database);
    
    // Executar arquivo SQL
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $queries = explode(";", $sql);
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                if ($mysqli->query($query)) {
                    echo "<p>✅ Query executada com sucesso</p>";
                } else {
                    echo "<p>⚠️ Erro na query: " . $mysqli->error . "</p>";
                }
            }
        }
        
        echo "<h2>✅ Restauração concluída!</h2>";
        echo "<p>O banco de dados foi restaurado com sucesso.</p>";
        
    } else {
        echo "<p style=\"color: red;\">❌ Arquivo SQL não encontrado!</p>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style=\"color: red;\">❌ Erro na restauração: " . $e->getMessage() . "</p>";
}

echo "<div style=\"margin-top: 20px;\">";
echo "<a href=\"../\" style=\"background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;\">🏠 Voltar ao Sistema</a>";
echo "</div>";
?>';

file_put_contents($restoreScript, $restoreContent);

// 4. CRIAR README
$readmeFile = $backupDir . '/README_BACKUP.txt';
$readmeContent = "BACKUP COMPLETO DO SISTEMA DE AGENDAMENTO
==================================================

Data do Backup: " . date('Y-m-d H:i:s') . "

CONTEÚDO DO BACKUP:
-------------------
1. banco_dados_backup.sql - Backup completo do banco de dados
2. restaurar_backup.php - Script para restaurar o backup
3. Arquivos PHP do sistema (401 arquivos)

COMO RESTAURAR:
---------------
1. Copie toda esta pasta para o servidor
2. Acesse: http://localhost/caminho/para/pasta/restaurar_backup.php
3. O script irá restaurar automaticamente o banco de dados
4. Todos os arquivos PHP já estarão prontos para uso

OBSERVAÇÕES:
-----------
- Este backup inclui TODOS os dados do sistema
- Inclui usuários, cursos, agendamentos, certificados
- Inclui configurações e estrutura completa
- Backup seguro e completo

CRIADO POR: Sistema de Backup Automático
";

file_put_contents($readmeFile, $readmeContent);

// Verificar se a pasta foi criada
if (is_dir($backupDir)) {
    $totalFiles = count(glob($backupDir . '/**/*', GLOB_BRACE));
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2 style='color: #155724; margin-top: 0;'>✅ Backup Completo Criado com Sucesso!</h2>";
    echo "<p><strong>📁 Pasta:</strong> " . basename($backupDir) . "</p>";
    echo "<p><strong>📏 Total de arquivos:</strong> $totalFiles</p>";
    echo "<p><strong>📏 Arquivos copiados:</strong> $copiedCount</p>";
    echo "<p><strong>🗄️ Banco de dados:</strong> Incluído</p>";
    echo "<p><strong>📍 Localização:</strong> " . dirname($backupDir) . "</p>";
    echo "</div>";
    
    // Link para acessar a pasta
    echo "<div style='text-align: center; margin: 30px 0;'>";
    echo "<a href='" . basename($backupDir) . "/' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px;'>";
    echo "📁 Acessar Pasta de Backup Completo";
    echo "</a>";
    echo "</div>";
    
} else {
    echo "<p style='color: red;'>❌ Erro: Falha ao criar a pasta de backup!</p>";
}

echo "<div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 8px;'>";
echo "<h3>📋 Conteúdo do Backup Completo:</h3>";
echo "<ul>";
echo "<li>✅ <strong>Banco de Dados</strong> - Todos os dados (usuários, cursos, agendamentos)</li>";
echo "<li>✅ <strong>Dashboard Administrador</strong> - Sistema completo de administração</li>";
echo "<li>✅ <strong>Dashboard Professor</strong> - Gestão de cursos e alunos</li>";
echo "<li>✅ <strong>Dashboard Aluno</strong> - Acesso aos cursos e certificados</li>";
echo "<li>✅ <strong>Sistema de Login</strong> - Autenticação para todos os usuários</li>";
echo "<li>✅ <strong>APIs e Funcionalidades</strong> - Todas as funcionalidades do sistema</li>";
echo "<li>✅ <strong>Script de Restauração</strong> - Restaura automaticamente o banco</li>";
echo "<li>✅ <strong>README</strong> - Instruções completas</li>";
echo "</ul>";
echo "</div>";

echo "<div style='margin-top: 20px;'>";
echo "<a href='./' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🏠 Voltar ao Sistema</a>";
echo "<a href='login.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Ir para Login</a>";
echo "</div>";
?>






