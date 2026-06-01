<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>🔍 Teste - Verificar Alunos no Banco</h2>";

try {
    include 'db.php';
    echo "✅ Conexão com banco OK!<br><br>";
    
    // Verificar todos os usuários
    echo "<h3>👥 Todos os Usuários:</h3>";
    $result = $conn->query("SELECT id, nome, tipo_usuario, ativo FROM usuarios ORDER BY tipo_usuario, nome");
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Tipo</th><th>Ativo</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['nome'] . "</td>";
            echo "<td>" . $row['tipo_usuario'] . "</td>";
            echo "<td>" . $row['ativo'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Nenhum usuário encontrado<br>";
    }
    
    // Verificar especificamente alunos
    echo "<h3>👨‍🎓 Alunos (tipo_usuario = 'aluno'):</h3>";
    $result = $conn->query("SELECT nome FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1 ORDER BY nome");
    if ($result && $result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . $row['nome'] . "</li>";
        }
        echo "</ul>";
        echo "<p><strong>Total de alunos:</strong> " . $result->num_rows . "</p>";
    } else {
        echo "❌ Nenhum aluno encontrado<br>";
        echo "<p>Query executada: SELECT nome FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1</p>";
    }
    
    // Testar o arquivo get_dados_agendamento.php
    echo "<h3>🔧 Testando get_dados_agendamento.php:</h3>";
    ob_start();
    include 'get_dados_agendamento.php';
    $output = ob_get_clean();
    
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    $data = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($data['success']) && $data['success']) {
            echo "<h4>✅ API funcionando!</h4>";
            echo "<p>Professores: " . count($data['data']['professores']) . "</p>";
            echo "<p>Cursos: " . count($data['data']['cursos']) . "</p>";
            echo "<p>Alunos: " . count($data['data']['alunos']) . "</p>";
        } else {
            echo "<h4>❌ Erro na API</h4>";
            echo "<p>" . ($data['message'] ?? 'Erro desconhecido') . "</p>";
        }
    } else {
        echo "<h4>❌ Erro JSON</h4>";
        echo "<p>" . json_last_error_msg() . "</p>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<br><h3>🎯 Próximo passo:</h3>";
echo "<p><a href='dashboard_corrigido.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 TESTAR DASHBOARD</a></p>";
?>


