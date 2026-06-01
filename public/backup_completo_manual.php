<?php
// Backup Completo Manual - Todo o Sistema
echo "<h1>🔄 Backup Completo de Todo o Sistema</h1>";
echo "<p>Data: " . date('Y-m-d H:i:s') . "</p>";

// Conectar ao banco
include 'db.php';
if (!$conn) {
    die("❌ Erro de conexão com banco de dados");
}
echo "<p>✅ Conexão com banco OK</p>";

// Criar arquivo de backup
$backup_file = 'backups/backup_completo_todo_sistema_' . date('Y-m-d_H-i-s') . '.sql';
$backup_content = "-- Backup Completo de Todo o Sistema de Agendamento\n";
$backup_content .= "-- Data: " . date('Y-m-d H:i:s') . "\n\n";

$total_records = 0;

// Lista de todas as tabelas
$tables = [
    'usuarios' => 'Usuários (Alunos, Professores, Admin)',
    'cursos' => 'Cursos',
    'certificados' => 'Certificados',
    'agendamentos' => 'Agendamentos',
    'atribuicoes_cursos' => 'Atribuições de Cursos',
    'inscricoes' => 'Inscrições'
];

foreach ($tables as $table => $description) {
    echo "<h3>📊 Backup da tabela: $description</h3>";
    
    // Verificar se a tabela existe
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check && $check->num_rows > 0) {
        
        // Obter estrutura
        $structure = $conn->query("SHOW CREATE TABLE $table");
        if ($structure) {
            $row = $structure->fetch_assoc();
            $backup_content .= "-- Estrutura da tabela $table\n";
            $backup_content .= "DROP TABLE IF EXISTS `$table`;\n";
            $backup_content .= $row['Create Table'] . ";\n\n";
            echo "<p>✅ Estrutura exportada</p>";
        }
        
        // Obter dados
        $data = $conn->query("SELECT * FROM $table");
        if ($data && $data->num_rows > 0) {
            $backup_content .= "-- Dados da tabela $table\n";
            $count = 0;
            
            while ($row = $data->fetch_assoc()) {
                $backup_content .= "INSERT INTO `$table` VALUES (";
                $values = [];
                
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = "'" . addslashes($value) . "'";
                    }
                }
                
                $backup_content .= implode(', ', $values) . ");\n";
                $count++;
            }
            
            $total_records += $count;
            echo "<p>✅ $count registros exportados</p>";
        } else {
            echo "<p>⚠️ Tabela vazia</p>";
        }
        
    } else {
        echo "<p>⚠️ Tabela não existe</p>";
    }
    
    $backup_content .= "\n";
}

// Salvar arquivo
if (file_put_contents($backup_file, $backup_content)) {
    echo "<h2>✅ Backup Completo Criado com Sucesso!</h2>";
    echo "<p><strong>Arquivo:</strong> $backup_file</p>";
    echo "<p><strong>Tamanho:</strong> " . number_format(filesize($backup_file)) . " bytes</p>";
    echo "<p><strong>Total de Registros:</strong> " . number_format($total_records) . "</p>";
    
    // Estatísticas
    echo "<h3>📊 Estatísticas:</h3>";
    
    $stats = $conn->query("SELECT tipo_usuario, COUNT(*) as total FROM usuarios GROUP BY tipo_usuario");
    echo "<h4>👥 Usuários:</h4><ul>";
    while ($stat = $stats->fetch_assoc()) {
        echo "<li><strong>" . ucfirst($stat['tipo_usuario']) . ":</strong> " . $stat['total'] . "</li>";
    }
    echo "</ul>";
    
    $cursos = $conn->query("SELECT COUNT(*) as total FROM cursos");
    $cursos_count = $cursos->fetch_assoc()['total'];
    echo "<p><strong>📚 Cursos:</strong> $cursos_count</p>";
    
    $certificados = $conn->query("SELECT COUNT(*) as total FROM certificados");
    $cert_count = $certificados->fetch_assoc()['total'];
    echo "<p><strong>🏆 Certificados:</strong> $cert_count</p>";
    
    $inscricoes = $conn->query("SELECT COUNT(*) as total FROM inscricoes");
    $insc_count = $inscricoes->fetch_assoc()['total'];
    echo "<p><strong>📝 Inscrições:</strong> $insc_count</p>";
    
    // Download
    echo "<h3>📥 Download:</h3>";
    echo "<p><a href='download_backup.php?file=" . basename($backup_file) . "' style='background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📥 Download do Backup Completo</a></p>";
    
} else {
    echo "<h2>❌ Erro ao Criar Backup</h2>";
    echo "<p>Não foi possível salvar o arquivo.</p>";
}

$conn->close();
echo "<hr>";
echo "<p><a href='dashboard_final.php'>← Voltar para Dashboard</a></p>";
?>







