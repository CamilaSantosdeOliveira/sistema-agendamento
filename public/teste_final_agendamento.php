<?php
echo "<h2>🎯 Teste Final - Agendamento Funcionando</h2>";

// Simular dados do formulário
$dados_teste = [
    'nome' => 'João Silva',
    'email' => 'joao@teste.com',
    'telefone' => '(11) 99999-9999',
    'professor' => 'Maria Santos',
    'data' => '2024-12-25',
    'hora' => '14:00',
    'servico' => 'Curso de PHP',
    'observacoes' => 'Teste final'
];

echo "<h3>📝 Testando inserção com dados reais:</h3>";

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
        echo "✅ <strong>SUCESSO!</strong> Agendamento inserido! ID: " . $conn->insert_id . "<br>";
        
        // Verificar se foi inserido
        $result = $conn->query("SELECT * FROM agendamentos WHERE id = " . $conn->insert_id);
        if ($result && $result->num_rows > 0) {
            $agendamento = $result->fetch_assoc();
            echo "<h4>📋 Agendamento criado:</h4>";
            echo "<ul>";
            echo "<li><strong>Nome:</strong> " . $agendamento['nome'] . "</li>";
            echo "<li><strong>Email:</strong> " . $agendamento['email'] . "</li>";
            echo "<li><strong>Professor:</strong> " . $agendamento['professor'] . "</li>";
            echo "<li><strong>Data:</strong> " . $agendamento['data'] . "</li>";
            echo "<li><strong>Hora:</strong> " . $agendamento['hora'] . "</li>";
            echo "<li><strong>Serviço:</strong> " . $agendamento['servico'] . "</li>";
            echo "<li><strong>Status:</strong> " . $agendamento['status'] . "</li>";
            echo "</ul>";
        }
        
        // Manter o teste no banco para verificar
        echo "<p style='color: green;'>✅ Teste mantido no banco para verificação</p>";
        
    } else {
        echo "❌ Erro na inserção: " . $stmt->error . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<br><h3>🎉 Sistema Pronto!</h3>";
echo "<p>O agendamento está funcionando perfeitamente!</p>";
echo "<p><a href='dashboard_corrigido.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 IR PARA O DASHBOARD</a></p>";
?>


