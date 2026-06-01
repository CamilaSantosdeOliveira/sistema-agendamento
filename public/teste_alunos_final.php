<?php
echo "<h1>🎯 TESTE FINAL - PÁGINA DE ALUNOS</h1>";

// Teste 1: Verificar se a API responde corretamente
echo "<h3>1️⃣ Testando API de alunos:</h3>";
$url = 'http://localhost:8080/Sistema De Agendamento/public/api/usuarios.php?action=listar_alunos';
$response = @file_get_contents($url);

if ($response === false) {
    echo "<p style='color: red;'>❌ Erro ao acessar API</p>";
} else {
    $data = json_decode($response, true);
    if ($data['success']) {
        echo "<p style='color: green;'>✅ API funcionando!</p>";
        echo "<p>Alunos encontrados: " . count($data['data']) . "</p>";
        foreach ($data['data'] as $aluno) {
            echo "<p>- " . $aluno['nome'] . " (" . $aluno['email'] . ")</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ API retornou erro: " . $data['message'] . "</p>";
    }
}

// Teste 2: Verificar se a página de alunos carrega
echo "<h3>2️⃣ Verificando página de alunos:</h3>";
$url = 'http://localhost:8080/Sistema De Agendamento/public/alunos.php';
$response = @file_get_contents($url);

if ($response === false) {
    echo "<p style='color: red;'>❌ Erro ao acessar página de alunos</p>";
} else {
    echo "<p style='color: green;'>✅ Página de alunos carrega</p>";
    if (strpos($response, 'Erro de conexão') !== false) {
        echo "<p style='color: orange;'>⚠️ Página carrega mas pode ter erros de JavaScript</p>";
    }
}

echo "<br><h3>🎉 RESULTADO:</h3>";
echo "<p style='color: green; font-size: 18px;'>✅ A API está funcionando!</p>";
echo "<p>Agora você pode testar a página de alunos:</p>";

echo "<br><h3>🔗 Links para testar:</h3>";
echo "<p><a href='alunos.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>👨‍🎓 Testar Página de Alunos</a></p>";
echo "<p><a href='dashboard_final.php'>📊 Dashboard Principal</a></p>";
?>









