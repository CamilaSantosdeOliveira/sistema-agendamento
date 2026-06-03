<?php
echo "<h2>🧪 Teste de Agendamento Simples</h2>";

include 'db.php';

// Testar inserção direta
echo "<h3>📝 Teste de Inserção no Banco:</h3>";

try {
    $sql = "INSERT INTO agendamentos (nome, email, telefone, professor, data, hora, servico, observacoes, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pendente')";
    
    $stmt = $conn->prepare($sql);
    $nome = 'Teste Aluno';
    $email = 'teste@email.com';
    $telefone = '(11) 99999-9999';
    $professor = 'Professor Teste';
    $data = '2024-12-25';
    $hora = '14:00:00';
    $servico = 'Curso de Teste';
    $observacoes = 'Teste de agendamento';
    
    $stmt->bind_param('ssssssss', $nome, $email, $telefone, $professor, $data, $hora, $servico, $observacoes);
    
    if ($stmt->execute()) {
        echo "✅ Inserção funcionou! ID: " . $conn->insert_id . "<br>";
        
        // Verificar se foi inserido
        $result = $conn->query("SELECT * FROM agendamentos WHERE id = " . $conn->insert_id);
        if ($result && $result->num_rows > 0) {
            $agendamento = $result->fetch_assoc();
            echo "✅ Agendamento encontrado: " . $agendamento['nome'] . " - " . $agendamento['data'] . "<br>";
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
echo "<p>Se o teste passou, o agendamento no dashboard deve funcionar!</p>";
echo "<p><a href='dashboard_corrigido.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 TESTAR DASHBOARD</a></p>";
?>




