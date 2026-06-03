<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>🔍 Teste dos Dados do Formulário</h2>";

try {
    include 'db.php';
    echo "✅ Conexão com banco OK!<br><br>";
    
    // Testar professores
    echo "<h3>👨‍🏫 Professores:</h3>";
    $result = $conn->query("SELECT nome FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1 ORDER BY nome");
    if ($result && $result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . $row['nome'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "❌ Nenhum professor encontrado<br>";
        echo "Query executada: SELECT nome FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1<br>";
    }
    
    // Testar cursos
    echo "<h3>📚 Cursos:</h3>";
    $result = $conn->query("SELECT nome FROM cursos WHERE status = 'ativo' ORDER BY nome");
    if ($result && $result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . $row['nome'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "❌ Nenhum curso encontrado<br>";
        echo "Query executada: SELECT nome FROM cursos WHERE status = 'ativo'<br>";
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




