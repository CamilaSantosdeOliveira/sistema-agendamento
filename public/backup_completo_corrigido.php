<?php
// Backup Completo Corrigido - Todo o Sistema
session_start();
include 'db.php';

echo "<h1>💾 Backup Completo Corrigido do Sistema</h1>";
echo "<p>Gerando backup de todos os dados e configurações...</p>";

// Data e hora do backup
$data_backup = date('Y-m-d_H-i-s');
$nome_arquivo = "backup_completo_corrigido_{$data_backup}.sql";

// Cabeçalho do backup
$backup_content = "-- Backup Completo Corrigido do Sistema EduConnect\n";
$backup_content .= "-- Data: " . date('d/m/Y H:i:s') . "\n";
$backup_content .= "-- Sistema: EduConnect - Sistema de Agendamento\n";
$backup_content .= "-- Versão: 1.0.0 - CORRIGIDO\n\n";

// Lista CORRIGIDA de todas as tabelas
$tabelas = [
    'usuarios',
    'cursos',
    'agendamentos',
    'atribuicoes_cursos',  // CORRIGIDO: era 'atribuicoes'
    'inscricoes',
    'certificados',
    'configuracoes_sistema',
    'logs_sistema',
    'logs_seguranca',
    'tentativas_login'
];

$total_registros = 0;

foreach ($tabelas as $tabela) {
    echo "<p>📋 Backup da tabela: <strong>$tabela</strong></p>";

    // Verificar se a tabela existe
    $result = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($result && $result->num_rows > 0) {

        // Estrutura da tabela
        $backup_content .= "\n-- ===========================================\n";
        $backup_content .= "-- Estrutura da tabela: $tabela\n";
        $backup_content .= "-- ===========================================\n\n";

        $create_table = $conn->query("SHOW CREATE TABLE $tabela")->fetch_assoc();
        $backup_content .= $create_table['Create Table'] . ";\n\n";

        // Dados da tabela
        $backup_content .= "-- Dados da tabela: $tabela\n";
        $backup_content .= "-- ===========================================\n\n";

        $dados = $conn->query("SELECT * FROM $tabela");
        $registros_tabela = 0;

        while ($row = $dados->fetch_assoc()) {
            $campos = array_keys($row);
            $valores = array_values($row);

            // Escapar valores
            $valores_escaped = array_map(function($valor) use ($conn) {
                if ($valor === null) return 'NULL';
                return "'" . $conn->real_escape_string($valor) . "'";
            }, $valores);

            $backup_content .= "INSERT INTO `$tabela` (`" . implode('`, `', $campos) . "`) VALUES (" . implode(', ', $valores_escaped) . ");\n";
            $registros_tabela++;
        }

        $total_registros += $registros_tabela;
        echo "<p>✅ $tabela: $registros_tabela registros</p>";

    } else {
        echo "<p>⚠️ Tabela $tabela não existe</p>";
    }
}

// Estatísticas do sistema
$backup_content .= "\n-- ===========================================\n";
$backup_content .= "-- Estatísticas do Sistema\n";
$backup_content .= "-- ===========================================\n\n";

$backup_content .= "-- Total de registros: $total_registros\n";
$backup_content .= "-- Data do backup: " . date('d/m/Y H:i:s') . "\n";
$backup_content .= "-- Sistema: EduConnect\n";
$backup_content .= "-- Status: Completo e Corrigido\n\n";

// Salvar arquivo
$caminho_backup = "backups/" . $nome_arquivo;
if (file_put_contents($caminho_backup, $backup_content)) {
    echo "<h2>✅ Backup Corrigido Concluído com Sucesso!</h2>";
    echo "<p><strong>Arquivo:</strong> $nome_arquivo</p>";
    echo "<p><strong>Localização:</strong> $caminho_backup</p>";
    echo "<p><strong>Tamanho:</strong> " . number_format(filesize($caminho_backup) / 1024, 2) . " KB</p>";
    echo "<p><strong>Total de registros:</strong> $total_registros</p>";

    echo "<h3>📊 Resumo do Backup Corrigido:</h3>";
    echo "<ul>";
    foreach ($tabelas as $tabela) {
        $result = $conn->query("SHOW TABLES LIKE '$tabela'");
        if ($result && $result->num_rows > 0) {
            $count = $conn->query("SELECT COUNT(*) as total FROM $tabela")->fetch_assoc()['total'];
            echo "<li>✅ $tabela: $count registros</li>";
        } else {
            echo "<li>⚠️ $tabela: Não existe</li>";
        }
    }
    echo "</ul>";

    echo "<h3>🔗 Links:</h3>";
    echo "<p><a href='$caminho_backup' download>📥 Download do Backup Corrigido</a></p>";
    echo "<p><a href='download_backup.php'>📋 Gerenciar Backups</a></p>";
    echo "<p><a href='configuracoes.php'>🏠 Voltar às Configurações</a></p>";

} else {
    echo "<h2>❌ Erro ao Salvar Backup</h2>";
    echo "<p>Não foi possível salvar o arquivo de backup.</p>";
}

// Registrar log do backup
try {
    $sql = "INSERT INTO logs_sistema (acao, tabela_afetada, ip_address) VALUES ('BACKUP_CORRIGIDO', 'sistema', '127.0.0.1')";
    $conn->query($sql);
} catch (Exception $e) {
    // Ignorar erro de log
}
?>









