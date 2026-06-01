<?php
echo "<h2>🔍 Verificação do Dashboard Atual</h2>";

// Conectar ao banco
include 'db.php';
if ($conn) {
    echo "✅ Conexão com banco OK<br>";
} else {
    echo "❌ Erro na conexão com banco<br>";
    exit;
}

echo "<h3>📊 Dados atuais no banco:</h3>";

// Verificar agendamentos futuros
$result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data >= CURDATE()");
$futuros = $result->fetch_assoc()['total'];

echo "📋 Agendamentos futuros: $futuros<br>";

// Listar todos os agendamentos futuros
$result = $conn->query("
    SELECT id, data, hora, status, observacoes, criado_em, nome, professor, servico
    FROM agendamentos 
    WHERE data >= CURDATE()
    ORDER BY data, hora
");

if ($result && $result->num_rows > 0) {
    echo "<h3>📅 Agendamentos futuros encontrados:</h3>";
    while ($row = $result->fetch_assoc()) {
        $data = date('d/m/Y', strtotime($row['data']));
        $hora = $row['hora'];
        $status = $row['status'];
        $aluno = $row['nome'] ?: 'Não informado';
        $professor = $row['professor'] ?: 'Não informado';
        $servico = $row['servico'] ?: 'Não informado';
        
        echo "- ID: {$row['id']} | Data: {$data} {$hora} | Status: {$status}<br>";
        echo "  Aluno: {$aluno} | Professor: {$professor} | Serviço: {$servico}<br><br>";
    }
} else {
    echo "✅ Nenhum agendamento futuro encontrado!<br>";
}

// Verificar agendamentos antigos
$result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data < CURDATE()");
$antigos = $result->fetch_assoc()['total'];

echo "<h3>📊 Resumo:</h3>";
echo "📋 Agendamentos futuros: $futuros<br>";
echo "📋 Agendamentos antigos: $antigos<br>";
echo "📋 Total de agendamentos: " . ($futuros + $antigos) . "<br>";

// Verificar se há dados de exemplo restantes
$result = $conn->query("
    SELECT COUNT(*) as total 
    FROM agendamentos 
    WHERE (data >= CURDATE() AND status = 'Cancelado') 
       OR (data < CURDATE())
");
$exemplo_restantes = $result->fetch_assoc()['total'];

if ($exemplo_restantes > 0) {
    echo "<br><h3>⚠️ DADOS DE EXEMPLO AINDA PRESENTES:</h3>";
    echo "📋 Ainda existem $exemplo_restantes agendamentos de exemplo no banco.<br>";
    echo "<p><a href='limpar_dados_exemplo.php' style='background: #ef4444; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🧹 EXECUTAR LIMPEZA NOVAMENTE</a></p>";
} else {
    echo "<br><h3>✅ Nenhum dado de exemplo encontrado!</h3>";
}

echo "<br><a href='dashboard_corrigido.php'>Ver Dashboard</a>";
?>
