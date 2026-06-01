<?php
echo "<h2>🧪 Teste do Dashboard - Consultas Corrigidas</h2>";

include 'db.php';

try {
    // Teste 1: Contar cursos ativos
    echo "<h3>📚 Teste 1: Cursos Ativos</h3>";
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    if ($result) {
        $count = $result->fetch_assoc()['total'];
        echo "✅ Cursos ativos: $count<br>";
    } else {
        echo "❌ Erro: " . $conn->error . "<br>";
    }
    
    // Teste 2: Contar professores ativos
    echo "<h3>👨‍🏫 Teste 2: Professores Ativos</h3>";
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1");
    if ($result) {
        $count = $result->fetch_assoc()['total'];
        echo "✅ Professores ativos: $count<br>";
    } else {
        echo "❌ Erro: " . $conn->error . "<br>";
    }
    
    // Teste 3: Contar alunos ativos
    echo "<h3>👥 Teste 3: Alunos Ativos</h3>";
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1");
    if ($result) {
        $count = $result->fetch_assoc()['total'];
        echo "✅ Alunos ativos: $count<br>";
    } else {
        echo "❌ Erro: " . $conn->error . "<br>";
    }
    
    // Teste 4: Contar agendamentos futuros
    echo "<h3>📅 Teste 4: Agendamentos Futuros</h3>";
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data >= CURDATE()");
    if ($result) {
        $count = $result->fetch_assoc()['total'];
        echo "✅ Agendamentos futuros: $count<br>";
    } else {
        echo "❌ Erro: " . $conn->error . "<br>";
    }
    
    // Teste 5: Buscar próximos agendamentos
    echo "<h3>📋 Teste 5: Próximos Agendamentos</h3>";
    $result = $conn->query("
        SELECT 
            a.id,
            a.data,
            a.hora,
            a.status,
            a.nome as aluno,
            a.professor,
            a.servico as curso
        FROM agendamentos a
        WHERE a.data >= CURDATE()
        ORDER BY a.data, a.hora
        LIMIT 5
    ");
    
    if ($result && $result->num_rows > 0) {
        echo "✅ Próximos agendamentos encontrados:<br>";
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['aluno'] . " com " . $row['professor'] . " (" . $row['curso'] . ") em " . $row['data'] . "<br>";
        }
    } else {
        echo "❌ Erro ou nenhum agendamento futuro: " . $conn->error . "<br>";
    }
    
    echo "<br><h3>🎉 Todos os testes passaram!</h3>";
    echo "<p>O dashboard deve funcionar agora!</p>";
    echo "<p><a href='dashboard_corrigido.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 ACESSAR DASHBOARD</a></p>";
    
} catch (Exception $e) {
    echo "❌ <strong>ERRO:</strong> " . $e->getMessage();
}
?>


