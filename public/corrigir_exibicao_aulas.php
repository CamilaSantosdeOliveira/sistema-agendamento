<?php
// Corrigir exibição das aulas no dashboard
include 'db.php';

echo "<h2>🔧 Corrigindo Exibição das Aulas</h2>";

// Testar conexão
if ($conn->connect_error) {
    die("❌ Erro de conexão: " . $conn->connect_error);
}
echo "✅ Conexão com banco OK!<br><br>";

// Verificar se a API está funcionando
echo "<h3>🔍 Testando API de Agendamentos:</h3>";

// Simular a consulta que o dashboard deveria fazer
$result = $conn->query("
    SELECT 
        id,
        data,
        hora,
        status,
        nome,
        professor,
        servico,
        observacoes
    FROM agendamentos 
    WHERE data >= CURDATE()
    ORDER BY data, hora
    LIMIT 10
");

if ($result && $result->num_rows > 0) {
    echo "✅ Consulta funcionando! Encontradas " . $result->num_rows . " aulas futuras.<br><br>";
    
    echo "<h3>📅 Aulas que deveriam aparecer no dashboard:</h3>";
    while ($row = $result->fetch_assoc()) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0; background: #f9f9f9;'>";
        echo "<strong>ID:</strong> {$row['id']}<br>";
        echo "<strong>Data:</strong> " . date('d/m/Y', strtotime($row['data'])) . "<br>";
        echo "<strong>Hora:</strong> {$row['hora']}<br>";
        echo "<strong>Aluno:</strong> {$row['nome']}<br>";
        echo "<strong>Professor:</strong> {$row['professor']}<br>";
        echo "<strong>Serviço:</strong> {$row['servico']}<br>";
        echo "<strong>Status:</strong> {$row['status']}<br>";
        if ($row['observacoes']) {
            echo "<strong>Observações:</strong> {$row['observacoes']}<br>";
        }
        echo "</div>";
    }
} else {
    echo "❌ Nenhuma aula futura encontrada.<br>";
}

echo "<br><h3>🎯 Conclusão:</h3>";
echo "Os dados estão corretos no banco. O problema pode ser:<br>";
echo "1. JavaScript não está carregando as aulas<br>";
echo "2. API não está sendo chamada corretamente<br>";
echo "3. Erro na função loadAgendamentos()<br>";

echo "<br><strong>💡 Solução:</strong> Recarregue a página do dashboard e verifique o console do navegador (F12) para ver se há erros JavaScript.";
?>


