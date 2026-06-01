<?php
echo "<h2>🧪 Teste do agendar_direto.php</h2>";

// Simular dados de teste
$dados_teste = [
    'nome' => 'João Silva',
    'email' => 'joao@teste.com',
    'telefone' => '(11) 99999-9999',
    'professor' => 'Maria Santos',
    'data' => '2024-12-25',
    'hora' => '14:00',
    'servico' => 'Curso de PHP',
    'observacoes' => 'Teste de agendamento'
];

echo "<h3>📝 Dados de teste:</h3>";
echo "<pre>" . json_encode($dados_teste, JSON_PRETTY_PRINT) . "</pre>";

// Testar inserção direta no banco
echo "<h3>🔌 Teste direto no banco:</h3>";

include 'db.php';

try {
    $sql = "INSERT INTO agendamentos (nome, email, telefone, professor, data, hora, servico, observacoes, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pendente')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssss', 
        $dados_teste['nome'],
        $dados_teste['email'],
        $dados_teste['telefone'],
        $dados_teste['professor'],
        $dados_teste['data'],
        $dados_teste['hora'],
        $dados_teste['servico'],
        $dados_teste['observacoes']
    );
    
    if ($stmt->execute()) {
        echo "✅ Inserção funcionou! ID: " . $conn->insert_id . "<br>";
        
        // Verificar se foi inserido
        $result = $conn->query("SELECT * FROM agendamentos WHERE id = " . $conn->insert_id);
        if ($result && $result->num_rows > 0) {
            $agendamento = $result->fetch_assoc();
            echo "✅ Agendamento encontrado:<br>";
            echo "- Nome: " . $agendamento['nome'] . "<br>";
            echo "- Email: " . $agendamento['email'] . "<br>";
            echo "- Data: " . $agendamento['data'] . "<br>";
            echo "- Hora: " . $agendamento['hora'] . "<br>";
            echo "- Serviço: " . $agendamento['servico'] . "<br>";
        }
        
        // Limpar teste
        $conn->query("DELETE FROM agendamentos WHERE id = " . $conn->insert_id);
        echo "🧹 Teste removido do banco<br>";
        
    } else {
        echo "❌ Erro na inserção: " . $stmt->error . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<br><h3>🎯 Próximo passo:</h3>";
echo "<p>Se o teste passou, o problema está no JavaScript do dashboard</p>";
echo "<p><a href='dashboard_corrigido.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 TESTAR DASHBOARD</a></p>";
?>


