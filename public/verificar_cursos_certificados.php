<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Cursos para Certificados</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        .step { margin: 15px 0; padding: 15px; border-left: 4px solid #ccc; background: #f9f9f9; }
        .btn { padding: 10px 20px; margin: 5px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block; }
        .btn:hover { background: #0056b3; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>🔍 Verificar Cursos para Certificados</h1>
    <p>Investigando por que não há cursos disponíveis</p>

    <?php
    include 'db.php';
    
    echo "<div class='step'>";
    echo "<h3>📋 Passo 1: Verificar Todos os Cursos no Banco</h3>";
    $todos_cursos = $conn->query("SELECT id, nome, status, categoria, nivel FROM cursos ORDER BY id");
    if ($todos_cursos && $todos_cursos->num_rows > 0) {
        echo "<p class='success'>✅ Total de cursos no banco: {$todos_cursos->num_rows}</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Nome</th><th>Status</th><th>Categoria</th><th>Nível</th></tr>";
        while ($curso = $todos_cursos->fetch_assoc()) {
            $status_class = ($curso['status'] == 'ativo') ? 'success' : 'error';
            echo "<tr>";
            echo "<td>{$curso['id']}</td>";
            echo "<td>{$curso['nome']}</td>";
            echo "<td class='{$status_class}'>{$curso['status']}</td>";
            echo "<td>{$curso['categoria']}</td>";
            echo "<td>{$curso['nivel']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ Nenhum curso encontrado no banco!</p>";
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 2: Verificar Cursos Ativos</h3>";
    $cursos_ativos = $conn->query("SELECT id, nome, status FROM cursos WHERE status = 'ativo'");
    if ($cursos_ativos && $cursos_ativos->num_rows > 0) {
        echo "<p class='success'>✅ Cursos ativos encontrados: {$cursos_ativos->num_rows}</p>";
        while ($curso = $cursos_ativos->fetch_assoc()) {
            echo "<p>• ID: {$curso['id']} - {$curso['nome']} ({$curso['status']})</p>";
        }
    } else {
        echo "<p class='error'>❌ Nenhum curso ativo encontrado!</p>";
        echo "<p>Vamos ativar alguns cursos...</p>";
        
        // Ativar todos os cursos
        $ativar = $conn->query("UPDATE cursos SET status = 'ativo' WHERE status != 'ativo'");
        if ($ativar) {
            echo "<p class='success'>✅ Cursos ativados com sucesso!</p>";
            
            // Verificar novamente
            $cursos_ativos = $conn->query("SELECT id, nome, status FROM cursos WHERE status = 'ativo'");
            if ($cursos_ativos && $cursos_ativos->num_rows > 0) {
                echo "<p class='success'>✅ Agora temos {$cursos_ativos->num_rows} cursos ativos:</p>";
                while ($curso = $cursos_ativos->fetch_assoc()) {
                    echo "<p>• ID: {$curso['id']} - {$curso['nome']}</p>";
                }
            }
        } else {
            echo "<p class='error'>❌ Erro ao ativar cursos: " . $conn->error . "</p>";
        }
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 3: Testar API de Cursos</h3>";
    $url = 'http://localhost:8080/Sistema%20De%20Agendamento/public/api/cursos.php?action=listar';
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
            if ($data['success']) {
                echo "<p class='success'>✅ API retornou {$data['data']} cursos</p>";
                if (count($data['data']) > 0) {
                    echo "<p>Primeiros cursos:</p>";
                    foreach (array_slice($data['data'], 0, 3) as $curso) {
                        echo "<p>• ID: {$curso['id']} - {$curso['nome']} ({$curso['status']})</p>";
                    }
                } else {
                    echo "<p class='error'>❌ API retornou lista vazia</p>";
                }
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
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 4: Testar API Sem Ação</h3>";
    $url = 'http://localhost:8080/Sistema%20De%20Agendamento/public/api/cursos.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response !== false) {
        echo "<p class='success'>✅ API respondeu sem ação</p>";
        $data = json_decode($response, true);
        if ($data && isset($data['success'])) {
            if ($data['success']) {
                echo "<p class='success'>✅ API retornou " . count($data['data']) . " cursos (todos)</p>";
            } else {
                echo "<p class='error'>❌ API retornou erro: " . $data['message'] . "</p>";
            }
        } else {
            echo "<p class='error'>❌ Resposta JSON inválida</p>";
            echo "<pre>$response</pre>";
        }
    } else {
        echo "<p class='error'>❌ Erro ao acessar API</p>";
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 5: Criar Cursos de Teste (se necessário)</h3>";
    $cursos_ativos = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    $total = $cursos_ativos->fetch_assoc()['total'];
    
    if ($total == 0) {
        echo "<p class='info'>Nenhum curso ativo encontrado. Criando cursos de teste...</p>";
        
        $cursos_teste = [
            ['nome' => 'Desenvolvimento Web Full Stack', 'descricao' => 'Curso completo de desenvolvimento web', 'categoria' => 'Programação', 'nivel' => 'Intermediário', 'duracao_horas' => 80, 'preco' => 299.99],
            ['nome' => 'Python para Data Science', 'descricao' => 'Análise de dados com Python', 'categoria' => 'Data Science', 'nivel' => 'Avançado', 'duracao_horas' => 60, 'preco' => 399.99],
            ['nome' => 'React.js e Node.js', 'descricao' => 'Desenvolvimento de aplicações modernas', 'categoria' => 'Programação', 'nivel' => 'Intermediário', 'duracao_horas' => 70, 'preco' => 349.99]
        ];
        
        foreach ($cursos_teste as $curso) {
            $stmt = $conn->prepare("INSERT INTO cursos (nome, descricao, categoria, nivel, duracao_horas, preco, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'ativo', NOW())");
            $stmt->bind_param('ssssid', $curso['nome'], $curso['descricao'], $curso['categoria'], $curso['nivel'], $curso['duracao_horas'], $curso['preco']);
            
            if ($stmt->execute()) {
                echo "<p class='success'>✅ Curso criado: {$curso['nome']}</p>";
            } else {
                echo "<p class='error'>❌ Erro ao criar curso: " . $stmt->error . "</p>";
            }
        }
    } else {
        echo "<p class='success'>✅ Já existem {$total} cursos ativos</p>";
    }
    echo "</div>";
    ?>

    <div class='step'>
        <h3>🎯 Próximos Passos</h3>
        <p>Com base nos resultados acima, o problema deve estar resolvido.</p>
        <a href="certificados.php" class="btn">📜 Testar Certificados</a>
        <a href="dashboard_final.php" class="btn">🏠 Dashboard</a>
        <a href="debug_conexao_certificados.php" class="btn">🔍 Debug Completo</a>
    </div>
</body>
</html>







