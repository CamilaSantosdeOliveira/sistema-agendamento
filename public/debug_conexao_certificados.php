<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Conexão Certificados</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        .step { margin: 15px 0; padding: 15px; border-left: 4px solid #ccc; background: #f9f9f9; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .btn { padding: 10px 20px; margin: 5px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🔍 Debug Conexão Certificados</h1>
    <p>Testando cada etapa da emissão de certificados</p>

    <?php
    include 'db.php';
    
    echo "<div class='step'>";
    echo "<h3>📋 Passo 1: Verificar Conexão com Banco</h3>";
    if ($conn) {
        echo "<p class='success'>✅ Conexão com banco estabelecida</p>";
        echo "<p>Host: " . $conn->host_info . "</p>";
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
    echo "<h3>📋 Passo 4: Testar API de Cursos via HTTP</h3>";
    $url = 'http://localhost:8080/Sistema%20De%20Agendamento/public/api/cursos.php?action=listar';
    
    // Configurar contexto para requisição HTTP
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response !== false) {
        echo "<p class='success'>✅ API de cursos respondeu</p>";
        $data = json_decode($response, true);
        if ($data && isset($data['success'])) {
            echo "<p class='success'>✅ Resposta JSON válida</p>";
            if ($data['success']) {
                echo "<p>📊 Cursos retornados: " . count($data['data']) . "</p>";
            } else {
                echo "<p class='error'>❌ API retornou erro: " . $data['message'] . "</p>";
            }
        } else {
            echo "<p class='error'>❌ Resposta JSON inválida</p>";
            echo "<pre>$response</pre>";
        }
    } else {
        echo "<p class='error'>❌ Erro ao acessar API de cursos</p>";
        echo "<p>URL: $url</p>";
        echo "<p>Verifique se o servidor está rodando na porta 8080</p>";
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 5: Testar API de Certificados via HTTP</h3>";
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
            'timeout' => 10,
            'ignore_errors' => true
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
    echo "<h3>📋 Passo 6: Testar Inserção Direta no Banco</h3>";
    
    // Buscar um aluno e curso para teste
    $aluno_teste = $conn->query("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' LIMIT 1");
    $curso_teste = $conn->query("SELECT id, nome FROM cursos WHERE status = 'ativo' LIMIT 1");
    
    if ($aluno_teste && $aluno_teste->num_rows > 0 && $curso_teste && $curso_teste->num_rows > 0) {
        $aluno = $aluno_teste->fetch_assoc();
        $curso = $curso_teste->fetch_assoc();
        
        echo "<p class='info'>Testando com aluno: {$aluno['nome']} (ID: {$aluno['id']})</p>";
        echo "<p class='info'>Testando com curso: {$curso['nome']} (ID: {$curso['id']})</p>";
        
        // Gerar código único
        $codigo = 'CERT-' . strtoupper(substr(md5(uniqid()), 0, 8));
        
        // Tentar inserir
        $stmt = $conn->prepare("INSERT INTO certificados (aluno_id, curso_id, codigo_verificacao, data_emissao, data_conclusao, status, carga_horaria, observacoes) VALUES (?, ?, ?, NOW(), NOW(), 'emitido', 40, 'Teste de inserção')");
        $stmt->bind_param('iis', $aluno['id'], $curso['id'], $codigo);
        
        if ($stmt->execute()) {
            echo "<p class='success'>✅ Teste de inserção bem-sucedido!</p>";
            echo "<p>Código gerado: $codigo</p>";
            
            // Remover o certificado de teste
            $conn->query("DELETE FROM certificados WHERE codigo_verificacao = '$codigo'");
            echo "<p class='info'>Certificado de teste removido</p>";
        } else {
            echo "<p class='error'>❌ Erro na inserção: " . $stmt->error . "</p>";
        }
    } else {
        echo "<p class='error'>❌ Não foi possível encontrar aluno ou curso para teste</p>";
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 7: Verificar Estrutura da Tabela Certificados</h3>";
    $estrutura = $conn->query("DESCRIBE certificados");
    if ($estrutura) {
        echo "<p class='success'>✅ Estrutura da tabela certificados:</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
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
        <h3>🎯 Análise dos Resultados</h3>
        <p>Com base nos testes acima, podemos identificar onde está o problema:</p>
        <ul>
            <li><strong>Se o Passo 4 falhar:</strong> Problema na API de cursos</li>
            <li><strong>Se o Passo 5 falhar:</strong> Problema na API de certificados</li>
            <li><strong>Se o Passo 6 falhar:</strong> Problema na estrutura da tabela</li>
            <li><strong>Se todos passarem:</strong> Problema no JavaScript da página</li>
        </ul>
        
        <h3>🔧 Próximos Passos</h3>
        <a href="certificados.php" class="btn">📜 Voltar para Certificados</a>
        <a href="dashboard_final.php" class="btn">🏠 Dashboard</a>
        <a href="verificar_tabela_certificados.php" class="btn">🔍 Verificar Tabela</a>
    </div>
</body>
</html>









