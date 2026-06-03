<?php
// Backup do Sistema de Certificados
// Data: <?php echo date('Y-m-d H:i:s'); ?>

// Conectar ao banco de dados
include 'db.php';

// Verificar conexão
if (!$conn) {
    die("❌ Erro de conexão com banco de dados");
}

echo "<h1>🔄 Backup do Sistema de Certificados</h1>";
echo "<p>Data: " . date('Y-m-d H:i:s') . "</p>";

// Criar diretório de backup se não existir
$backup_dir = 'backups/';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

// Nome do arquivo de backup
$backup_file = $backup_dir . 'backup_certificados_' . date('Y-m-d_H-i-s') . '.sql';

echo "<h2>📋 Backup das Tabelas</h2>";

// Backup da tabela certificados
$certificados_sql = "SELECT * FROM certificados";
$certificados_result = $conn->query($certificados_sql);

$backup_content = "-- Backup do Sistema de Certificados\n";
$backup_content .= "-- Data: " . date('Y-m-d H:i:s') . "\n\n";

// Estrutura da tabela certificados
$backup_content .= "-- Estrutura da tabela certificados\n";
$backup_content .= "DROP TABLE IF EXISTS `certificados`;\n";
$backup_content .= "CREATE TABLE `certificados` (\n";
$backup_content .= "  `id` int(11) NOT NULL AUTO_INCREMENT,\n";
$backup_content .= "  `aluno_id` int(11) NOT NULL,\n";
$backup_content .= "  `curso_id` int(11) NOT NULL,\n";
$backup_content .= "  `codigo_verificacao` varchar(50) NOT NULL,\n";
$backup_content .= "  `data_emissao` date NOT NULL,\n";
$backup_content .= "  `data_conclusao` date NOT NULL,\n";
$backup_content .= "  `status` enum('pendente','emitido','validado','revogado') DEFAULT 'pendente',\n";
$backup_content .= "  `carga_horaria` int(11) DEFAULT 0,\n";
$backup_content .= "  `observacoes` text,\n";
$backup_content .= "  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),\n";
$backup_content .= "  PRIMARY KEY (`id`),\n";
$backup_content .= "  UNIQUE KEY `codigo_verificacao` (`codigo_verificacao`),\n";
$backup_content .= "  KEY `aluno_id` (`aluno_id`),\n";
$backup_content .= "  KEY `curso_id` (`curso_id`)\n";
$backup_content .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

// Dados da tabela certificados
$backup_content .= "-- Dados da tabela certificados\n";
if ($certificados_result && $certificados_result->num_rows > 0) {
    while ($row = $certificados_result->fetch_assoc()) {
        $backup_content .= "INSERT INTO `certificados` VALUES (";
        $backup_content .= $row['id'] . ", ";
        $backup_content .= $row['aluno_id'] . ", ";
        $backup_content .= $row['curso_id'] . ", ";
        $backup_content .= "'" . addslashes($row['codigo_verificacao']) . "', ";
        $backup_content .= "'" . $row['data_emissao'] . "', ";
        $backup_content .= "'" . $row['data_conclusao'] . "', ";
        $backup_content .= "'" . $row['status'] . "', ";
        $backup_content .= ($row['carga_horaria'] ? $row['carga_horaria'] : 'NULL') . ", ";
        $backup_content .= ($row['observacoes'] ? "'" . addslashes($row['observacoes']) . "'" : 'NULL') . ", ";
        $backup_content .= "'" . $row['created_at'] . "');\n";
    }
    echo "<p>✅ " . $certificados_result->num_rows . " certificados exportados</p>";
} else {
    echo "<p>⚠️ Nenhum certificado encontrado</p>";
}

// Backup da tabela usuarios (apenas alunos)
$backup_content .= "\n-- Dados dos alunos (usuários)\n";
$alunos_sql = "SELECT * FROM usuarios WHERE tipo_usuario = 'aluno'";
$alunos_result = $conn->query($alunos_sql);

if ($alunos_result && $alunos_result->num_rows > 0) {
    while ($row = $alunos_result->fetch_assoc()) {
        $backup_content .= "INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo_usuario`, `ativo`, `criado_em`) VALUES (";
        $backup_content .= $row['id'] . ", ";
        $backup_content .= "'" . addslashes($row['nome']) . "', ";
        $backup_content .= "'" . addslashes($row['email']) . "', ";
        $backup_content .= "'" . addslashes($row['senha']) . "', ";
        $backup_content .= "'aluno', ";
        $backup_content .= $row['ativo'] . ", ";
        $backup_content .= "'" . $row['criado_em'] . "');\n";
    }
    echo "<p>✅ " . $alunos_result->num_rows . " alunos exportados</p>";
}

// Backup da tabela cursos
$backup_content .= "\n-- Dados dos cursos\n";
$cursos_sql = "SELECT * FROM cursos";
$cursos_result = $conn->query($cursos_sql);

if ($cursos_result && $cursos_result->num_rows > 0) {
    while ($row = $cursos_result->fetch_assoc()) {
        $backup_content .= "INSERT INTO `cursos` (`id`, `nome`, `descricao`, `categoria`, `nivel`, `duracao_horas`, `preco`, `status`, `alunos_inscritos`, `avaliacao`) VALUES (";
        $backup_content .= $row['id'] . ", ";
        $backup_content .= "'" . addslashes($row['nome']) . "', ";
        $backup_content .= "'" . addslashes($row['descricao']) . "', ";
        $backup_content .= "'" . addslashes($row['categoria']) . "', ";
        $backup_content .= "'" . addslashes($row['nivel']) . "', ";
        $backup_content .= $row['duracao_horas'] . ", ";
        $backup_content .= $row['preco'] . ", ";
        $backup_content .= "'" . $row['status'] . "', ";
        $backup_content .= $row['alunos_inscritos'] . ", ";
        $backup_content .= $row['avaliacao'] . ");\n";
    }
    echo "<p>✅ " . $cursos_result->num_rows . " cursos exportados</p>";
}

// Salvar arquivo de backup
if (file_put_contents($backup_file, $backup_content)) {
    echo "<h2>✅ Backup Criado com Sucesso!</h2>";
    echo "<p><strong>Arquivo:</strong> " . $backup_file . "</p>";
    echo "<p><strong>Tamanho:</strong> " . number_format(filesize($backup_file)) . " bytes</p>";
    
    // Estatísticas do backup
    echo "<h3>📊 Estatísticas do Backup</h3>";
    
    // Contar certificados por status
    $stats_sql = "SELECT status, COUNT(*) as total FROM certificados GROUP BY status";
    $stats_result = $conn->query($stats_sql);
    
    echo "<ul>";
    while ($stat = $stats_result->fetch_assoc()) {
        echo "<li><strong>" . ucfirst($stat['status']) . ":</strong> " . $stat['total'] . "</li>";
    }
    echo "</ul>";
    
    // Link para download
    echo "<p><a href='download_backup.php?file=" . basename($backup_file) . "' class='btn btn-primary'>📥 Download do Backup</a></p>";
    
} else {
    echo "<h2>❌ Erro ao Criar Backup</h2>";
    echo "<p>Não foi possível salvar o arquivo de backup.</p>";
}

// Fechar conexão
$conn->close();

echo "<hr>";
echo "<p><a href='validacao_certificados.php'>← Voltar para Validação de Certificados</a></p>";
echo "<p><a href='certificados.php'>← Voltar para Sistema de Certificados</a></p>";
echo "<p><a href='dashboard_final.php'>← Voltar para Dashboard</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
h1 { color: #2c3e50; }
h2 { color: #34495e; }
h3 { color: #7f8c8d; }
.btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
.btn-primary { background: #3498db; }
.btn:hover { opacity: 0.8; }
ul { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
li { margin: 5px 0; }
</style>









