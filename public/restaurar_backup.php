<?php
echo "<h1>🔄 RESTAURAR BACKUP</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} .warning{color:orange;font-weight:bold;} .danger{color:darkred;font-weight:bold;}</style>";

// Verificar se foi passado um arquivo
if (!isset($_GET['file'])) {
    echo "<div class='error'>❌ Nenhum arquivo de backup especificado!</div>";
    echo "<p><a href='backup_automatico.php'>← Voltar ao sistema de backup</a></p>";
    exit;
}

$backup_file = 'backups/' . $_GET['file'];

// Verificar se o arquivo existe
if (!file_exists($backup_file)) {
    echo "<div class='error'>❌ Arquivo de backup não encontrado: {$backup_file}</div>";
    echo "<p><a href='backup_automatico.php'>← Voltar ao sistema de backup</a></p>";
    exit;
}

// Verificar se é uma confirmação
if (!isset($_GET['confirm'])) {
    echo "<div class='warning'>⚠️ ATENÇÃO: Restaurar backup irá APAGAR todos os dados atuais!</div>";
    echo "<div class='info'>📁 Arquivo: {$_GET['file']}</div>";
    echo "<div class='info'>📊 Tamanho: " . round(filesize($backup_file) / 1024 / 1024, 2) . " MB</div>";
    
    echo "<h2>🎯 Confirmação:</h2>";
    echo "<p><strong>Esta ação irá:</strong></p>";
    echo "<ul>";
    echo "<li>❌ Apagar TODOS os dados atuais do banco</li>";
    echo "<li>✅ Restaurar os dados do backup</li>";
    echo "<li>⚠️ Esta ação NÃO pode ser desfeita!</li>";
    echo "</ul>";
    
    echo "<div style='margin:20px 0;'>";
    echo "<a href='restaurar_backup.php?file=" . urlencode($_GET['file']) . "&confirm=1' style='background:red;color:white;padding:15px;text-decoration:none;border-radius:5px;margin:5px;font-weight:bold;'>🚨 CONFIRMAR RESTAURAÇÃO</a>";
    echo "<a href='backup_automatico.php' style='background:gray;color:white;padding:15px;text-decoration:none;border-radius:5px;margin:5px;'>❌ Cancelar</a>";
    echo "</div>";
    exit;
}

// Fazer backup do estado atual antes de restaurar
echo "<div class='info'>🔄 Fazendo backup do estado atual...</div>";
$current_backup = 'backups/backup_antes_restauracao_' . date('Y-m-d_H-i-s') . '.sql';
$command_backup = "mysqldump -u root -p '' sistema_agendamento > \"{$current_backup}\" 2>&1";
exec($command_backup, $output_backup, $return_backup);

if ($return_backup === 0) {
    echo "<div class='success'>✅ Backup de segurança criado: " . basename($current_backup) . "</div>";
} else {
    echo "<div class='warning'>⚠️ Não foi possível criar backup de segurança</div>";
}

// Restaurar o backup
echo "<div class='info'>🔄 Restaurando backup...</div>";
$command_restore = "mysql -u root -p '' sistema_agendamento < \"{$backup_file}\" 2>&1";
exec($command_restore, $output_restore, $return_restore);

if ($return_restore === 0) {
    echo "<div class='success'>✅ Backup restaurado com sucesso!</div>";
    
    // Verificar se os dados foram restaurados
    $conn = new mysqli('localhost', 'root', '', 'sistema_agendamento');
    if (!$conn->connect_error) {
        $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<div class='info'>👥 Usuários restaurados: {$row['total']}</div>";
        }
        
        $result = $conn->query("SELECT COUNT(*) as total FROM cursos");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<div class='info'>📚 Cursos restaurados: {$row['total']}</div>";
        }
        
        $conn->close();
    }
    
} else {
    echo "<div class='error'>❌ Erro ao restaurar backup!</div>";
    echo "<div class='error'>Comando: {$command_restore}</div>";
    echo "<div class='error'>Saída: " . implode('<br>', $output_restore) . "</div>";
}

echo "<h2>🎯 PRÓXIMOS PASSOS:</h2>";
echo "<div style='margin:20px 0;'>";
echo "<a href='dashboard_final.php' style='background:green;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;'>📊 Ver Dashboard</a>";
echo "<a href='cursos_completo.php' style='background:blue;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;'>📚 Ver Cursos</a>";
echo "<a href='backup_automatico.php' style='background:orange;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;'>💾 Sistema de Backup</a>";
echo "</div>";
?>









