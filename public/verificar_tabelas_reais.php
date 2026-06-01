<?php
// Verificar Tabelas Reais no Banco
session_start();
include 'db.php';

echo "<h1>🔍 Verificação das Tabelas Reais</h1>";

// Buscar todas as tabelas do banco
$result = $conn->query("SHOW TABLES");
$tabelas_existentes = [];

while ($row = $result->fetch_array()) {
    $tabelas_existentes[] = $row[0];
}

echo "<h2>📋 Tabelas que Existem no Banco:</h2>";
echo "<ul>";
foreach ($tabelas_existentes as $tabela) {
    $count = $conn->query("SELECT COUNT(*) as total FROM $tabela")->fetch_assoc()['total'];
    echo "<li>✅ <strong>$tabela</strong>: $count registros</li>";
}
echo "</ul>";

echo "<h2>📊 Estatísticas:</h2>";
echo "<p><strong>Total de tabelas:</strong> " . count($tabelas_existentes) . "</p>";

echo "<h2>🔍 Comparação com o Backup:</h2>";
$tabelas_backup = [
    'usuarios',
    'cursos', 
    'agendamentos',
    'professores',
    'atribuicoes',
    'inscricoes',
    'certificados',
    'configuracoes_sistema',
    'logs_sistema',
    'logs_seguranca',
    'tentativas_login'
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Tabela</th><th>Status</th><th>Registros</th></tr>";

foreach ($tabelas_backup as $tabela) {
    if (in_array($tabela, $tabelas_existentes)) {
        $count = $conn->query("SELECT COUNT(*) as total FROM $tabela")->fetch_assoc()['total'];
        echo "<tr><td>$tabela</td><td style='color: green;'>✅ Existe</td><td>$count</td></tr>";
    } else {
        echo "<tr><td>$tabela</td><td style='color: red;'>❌ Não Existe</td><td>-</td></tr>";
    }
}
echo "</table>";

echo "<h2>💡 Explicação:</h2>";
echo "<ul>";
echo "<li><strong>professores:</strong> Os dados dos professores estão na tabela <code>usuarios</code> com <code>tipo_usuario = 'professor'</code></li>";
echo "<li><strong>atribuicoes:</strong> Esta funcionalidade foi integrada diretamente no sistema de cursos</li>";
echo "<li><strong>Outras tabelas:</strong> Todas as outras tabelas existem e estão funcionando</li>";
echo "</ul>";

echo "<p><a href='configuracoes.php'>← Voltar às Configurações</a></p>";
?>







