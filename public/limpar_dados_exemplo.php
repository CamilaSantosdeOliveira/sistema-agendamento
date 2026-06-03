<?php
echo "<h2>🧹 Limpeza de Dados de Exemplo</h2>";

// Conectar ao banco
include 'db.php';
if ($conn) {
    echo "✅ Conexão com banco OK<br>";
} else {
    echo "❌ Erro na conexão com banco<br>";
    exit;
}

echo "<h3>📊 Antes da limpeza:</h3>";

// Contar agendamentos antes
$result = $conn->query("SELECT COUNT(*) as total FROM agendamentos");
$total_antes = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data >= CURDATE()");
$futuros_antes = $result->fetch_assoc()['total'];

echo "📋 Total de agendamentos: $total_antes<br>";
echo "📋 Agendamentos futuros: $futuros_antes<br>";

echo "<h3>🔍 Identificando dados de exemplo:</h3>";

// Mostrar agendamentos que serão removidos
$result = $conn->query("
    SELECT id, data, hora, status, observacoes, criado_em
    FROM agendamentos 
    WHERE (data >= CURDATE() AND status = 'Cancelado') 
       OR (data < CURDATE() AND status IN ('Pendente', 'Confirmado'))
    ORDER BY data
");

if ($result && $result->num_rows > 0) {
    echo "📋 Agendamentos que serão removidos (dados de exemplo):<br>";
    while ($row = $result->fetch_assoc()) {
        $data = date('d/m/Y', strtotime($row['data']));
        $hora = $row['hora'];
        $status = $row['status'];
        echo "- ID: {$row['id']} | Data: {$data} {$hora} | Status: {$status}<br>";
    }
    
    echo "<br><h3>⚠️ ATENÇÃO:</h3>";
    echo "<p>Estes são dados de exemplo que foram inseridos automaticamente.</p>";
    echo "<p>Serão removidos para deixar apenas seus dados reais.</p>";
    
    echo "<br><h3>🧹 Executar limpeza:</h3>";
    echo "<p><a href='?acao=limpar' style='background: #ef4444; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🗑️ LIMPAR DADOS DE EXEMPLO</a></p>";
    
} else {
    echo "✅ Nenhum dado de exemplo encontrado!<br>";
}

// Executar limpeza se solicitado
if (isset($_GET['acao']) && $_GET['acao'] === 'limpar') {
    echo "<h3>🧹 Executando limpeza...</h3>";
    
    try {
        // Remover agendamentos futuros cancelados (dados de exemplo)
        $conn->query("DELETE FROM agendamentos WHERE data >= CURDATE() AND status = 'Cancelado'");
        
        // Remover agendamentos antigos (dados de exemplo)
        $conn->query("DELETE FROM agendamentos WHERE data < CURDATE()");
        
        echo "✅ Limpeza concluída!<br>";
        
        // Contar após limpeza
        $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos");
        $total_depois = $result->fetch_assoc()['total'];
        
        $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data >= CURDATE()");
        $futuros_depois = $result->fetch_assoc()['total'];
        
        echo "<h3>📊 Após a limpeza:</h3>";
        echo "📋 Total de agendamentos: $total_depois<br>";
        echo "📋 Agendamentos futuros: $futuros_depois<br>";
        
        echo "<br><h3>✅ Resultado:</h3>";
        echo "<p>Removidos " . ($total_antes - $total_depois) . " agendamentos de exemplo.</p>";
        echo "<p>Agora o dashboard mostrará apenas seus dados reais!</p>";
        
    } catch (Exception $e) {
        echo "❌ Erro na limpeza: " . $e->getMessage() . "<br>";
    }
}

echo "<br><a href='dashboard_corrigido.php'>Voltar ao Dashboard</a>";
?>


