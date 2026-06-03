<?php
echo "<h2>🧪 Teste de Remoção Automática de Aulas</h2>";

// Conectar ao banco
include 'db.php';
if ($conn) {
    echo "✅ Conexão com banco OK<br>";
} else {
    echo "❌ Erro na conexão com banco<br>";
    exit;
}

echo "<h3>📊 Status atual:</h3>";

// Verificar agendamentos futuros
$result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data >= CURDATE()");
$futuros = $result->fetch_assoc()['total'];

echo "📋 Agendamentos futuros: $futuros<br>";

// Listar agendamentos futuros
$result = $conn->query("
    SELECT id, data, hora, status, nome, professor, servico
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
        echo "  Aluno: {$aluno} | Professor: {$professor} | Serviço: {$servico}<br>";
        echo "  <a href='#' onclick='testarRemocao({$row['id']})' style='color: red; text-decoration: none;'>🗑️ Testar Remoção</a><br><br>";
    }
} else {
    echo "✅ Nenhum agendamento futuro encontrado!<br>";
}

echo "<h3>🎯 Como funciona agora:</h3>";
echo "✅ Quando você clicar em 'Cancelar' no dashboard, a aula será <strong>removida permanentemente</strong><br>";
echo "✅ O contador de 'Aulas Agendadas' será atualizado automaticamente<br>";
echo "✅ Não haverá mais aulas 'canceladas' no sistema - elas serão excluídas<br>";

echo "<br><h3>🧪 Teste da API:</h3>";
echo "<p><a href='api/dashboard_stats.php' target='_blank'>📊 Ver Estatísticas da API</a></p>";

echo "<br><a href='dashboard_corrigido.php'>Voltar ao Dashboard</a>";

?>

<script>
async function testarRemocao(id) {
    if (confirm('Testar remoção da aula ID ' + id + '?')) {
        try {
            const response = await fetch(`api/agendamentos.php/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();
            
            if (result.success) {
                alert('✅ Aula removida com sucesso!');
                location.reload(); // Recarregar página
            } else {
                alert('❌ Erro: ' + (result.error || 'Erro desconhecido'));
            }
        } catch (error) {
            alert('❌ Erro de conexão: ' + error.message);
        }
    }
}
</script>


