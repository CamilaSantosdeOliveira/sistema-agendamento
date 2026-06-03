<?php
echo "<h2>🎯 Teste Final Simples</h2>";

// Testar conexão com banco
echo "<h3>🔌 Testando conexão com banco:</h3>";

try {
    include 'db.php';
    echo "✅ <strong>SUCESSO!</strong> Conexão com banco OK!<br>";
    
    // Testar consulta simples
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos");
    if ($result) {
        $total = $result->fetch_assoc()['total'];
        echo "📊 Total de agendamentos: $total<br>";
    }
    
    // Testar consulta com data (que estava dando erro)
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data >= CURDATE()");
    if ($result) {
        $futuros = $result->fetch_assoc()['total'];
        echo "📅 Agendamentos futuros: $futuros<br>";
        echo "✅ <strong>SUCESSO!</strong> Consulta com 'data' funcionando!<br>";
    } else {
        echo "❌ Erro na consulta: " . $conn->error . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<br><h3>🎉 Resultado:</h3>";
echo "<p>Se todas as consultas funcionaram, o sistema está pronto!</p>";
echo "<p><a href='dashboard_corrigido.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 IR PARA O DASHBOARD</a></p>";
?>




