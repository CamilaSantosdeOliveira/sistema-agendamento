<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Emissão Certificado v2</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .step { margin: 10px 0; padding: 10px; border-left: 3px solid #ccc; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🔍 Debug Emissão Certificado v2</h1>
    <p>Investigando o problema de conexão na emissão de certificados</p>

    <?php
    include 'db.php';
    
    echo "<div class='step'>";
    echo "<h3>📋 Passo 1: Verificar Conexão com Banco</h3>";
    if ($conn) {
        echo "<p class='success'>✅ Conexão com banco estabelecida</p>";
    } else {
        echo "<p class='error'>❌ Erro na conexão com banco</p>";
        exit;
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 2: Verificar Alunos Disponíveis</h3>";
    $alunos = $conn->query("SELECT id, nome, email FROM usuarios WHERE tipo_usuario = 'aluno' LIMIT 3");
    if ($alunos && $alunos->num_rows > 0) {
        echo "<p class='success'>✅ Alunos encontrados: {$alunos->num_rows}</p>";
        while ($aluno = $alunos->fetch_assoc()) {
            echo "<p>• ID: {$aluno['id']} - {$aluno['nome']} ({$aluno['email']})</p>";
        }
    } else {
        echo "<p class='error'>❌ Nenhum aluno encontrado</p>";
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 3: Verificar Cursos Disponíveis</h3>";
    $cursos = $conn->query("SELECT id, nome FROM cursos WHERE status = 'ativo' LIMIT 3");
    if ($cursos && $cursos->num_rows > 0) {
        echo "<p class='success'>✅ Cursos encontrados: {$cursos->num_rows}</p>";
        while ($curso = $cursos->fetch_assoc()) {
            echo "<p>• ID: {$curso['id']} - {$curso['nome']}</p>";
        }
    } else {
        echo "<p class='error'>❌ Nenhum curso encontrado</p>";
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 4: Testar API de Cursos</h3>";
    $url = 'http://localhost:8080/Sistema%20De%20Agendamento/public/api/cursos.php?action=listar';
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response !== false) {
        echo "<p class='success'>✅ API de cursos respondeu</p>";
        $data = json_decode($response, true);
        if ($data && isset($data['success'])) {
            echo "<p class='success'>✅ Resposta JSON válida</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        } else {
            echo "<p class='error'>❌ Resposta JSON inválida</p>";
            echo "<pre>$response</pre>";
        }
    } else {
        echo "<p class='error'>❌ Erro ao acessar API de cursos</p>";
        echo "<p>URL: $url</p>";
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 5: Testar API de Certificados</h3>";
    $url = 'http://localhost:8080/Sistema%20De%20Agendamento/public/api/certificados.php';
    $postData = json_encode([
        'action' => 'emitir_certificado_individual',
        'aluno_id' => 10,
        'curso_id' => 1,
        'data_conclusao' => date('Y-m-d')
    ]);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $postData,
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response !== false) {
        echo "<p class='success'>✅ API de certificados respondeu</p>";
        $data = json_decode($response, true);
        if ($data && isset($data['success'])) {
            echo "<p class='success'>✅ Resposta JSON válida</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        } else {
            echo "<p class='error'>❌ Resposta JSON inválida</p>";
            echo "<pre>$response</pre>";
        }
    } else {
        echo "<p class='error'>❌ Erro ao acessar API de certificados</p>";
        echo "<p>URL: $url</p>";
        echo "<p>Dados enviados: $postData</p>";
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 6: Verificar Estrutura da Tabela Certificados</h3>";
    $estrutura = $conn->query("DESCRIBE certificados");
    if ($estrutura) {
        echo "<p class='success'>✅ Estrutura da tabela certificados:</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th></tr>";
        while ($row = $estrutura->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ Erro ao verificar estrutura da tabela</p>";
    }
    echo "</div>";
    ?>

    <div class='step'>
        <h3>🎯 Próximos Passos</h3>
        <p>Com base nos resultados acima, vamos identificar e corrigir o problema.</p>
        <a href="certificados.php" class="btn btn-primary">📜 Voltar para Certificados</a>
        <a href="dashboard_final.php" class="btn btn-secondary">🏠 Dashboard</a>
    </div>
</body>
</html>







