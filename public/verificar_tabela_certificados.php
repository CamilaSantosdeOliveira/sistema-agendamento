<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Tabela Certificados</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .step { margin: 10px 0; padding: 10px; border-left: 3px solid #ccc; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>🔍 Verificar Tabela Certificados</h1>
    <p>Verificando e corrigindo a estrutura da tabela certificados</p>

    <?php
    include 'db.php';
    
    echo "<div class='step'>";
    echo "<h3>📋 Passo 1: Verificar se a tabela existe</h3>";
    $tabela_existe = $conn->query("SHOW TABLES LIKE 'certificados'");
    if ($tabela_existe && $tabela_existe->num_rows > 0) {
        echo "<p class='success'>✅ Tabela 'certificados' existe</p>";
    } else {
        echo "<p class='error'>❌ Tabela 'certificados' não existe</p>";
        echo "<p>Criando tabela...</p>";
        
        $sql_criar = "CREATE TABLE certificados (
            id INT(11) NOT NULL AUTO_INCREMENT,
            aluno_id INT(11) NOT NULL,
            curso_id INT(11) NOT NULL,
            codigo_verificacao VARCHAR(50) NOT NULL UNIQUE,
            data_emissao DATE NOT NULL,
            data_conclusao DATE NOT NULL,
            status ENUM('pendente', 'emitido', 'validado', 'revogado') DEFAULT 'pendente',
            carga_horaria INT(11) DEFAULT 0,
            observacoes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (aluno_id) REFERENCES usuarios(id),
            FOREIGN KEY (curso_id) REFERENCES cursos(id)
        )";
        
        if ($conn->query($sql_criar)) {
            echo "<p class='success'>✅ Tabela 'certificados' criada com sucesso</p>";
        } else {
            echo "<p class='error'>❌ Erro ao criar tabela: " . $conn->error . "</p>";
        }
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 2: Verificar estrutura da tabela</h3>";
    $estrutura = $conn->query("DESCRIBE certificados");
    if ($estrutura) {
        echo "<p class='success'>✅ Estrutura da tabela certificados:</p>";
        echo "<table>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th><th>Extra</th></tr>";
        while ($row = $estrutura->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "<td>{$row['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ Erro ao verificar estrutura</p>";
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 3: Verificar se há certificados</h3>";
    $total = $conn->query("SELECT COUNT(*) as total FROM certificados");
    if ($total) {
        $count = $total->fetch_assoc()['total'];
        echo "<p class='info'>📊 Total de certificados: $count</p>";
        
        if ($count > 0) {
            echo "<p>Últimos certificados:</p>";
            $certificados = $conn->query("SELECT c.*, u.nome as aluno_nome, cur.nome as curso_nome 
                                        FROM certificados c 
                                        JOIN usuarios u ON c.aluno_id = u.id 
                                        JOIN cursos cur ON c.curso_id = cur.id 
                                        ORDER BY c.id DESC LIMIT 5");
            echo "<table>";
            echo "<tr><th>ID</th><th>Aluno</th><th>Curso</th><th>Código</th><th>Status</th><th>Data Emissão</th></tr>";
            while ($cert = $certificados->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$cert['id']}</td>";
                echo "<td>{$cert['aluno_nome']}</td>";
                echo "<td>{$cert['curso_nome']}</td>";
                echo "<td>{$cert['codigo_verificacao']}</td>";
                echo "<td>{$cert['status']}</td>";
                echo "<td>{$cert['data_emissao']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    echo "</div>";

    echo "<div class='step'>";
    echo "<h3>📋 Passo 4: Testar inserção de certificado</h3>";
    
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
    ?>

    <div class='step'>
        <h3>🎯 Próximos Passos</h3>
        <p>Se tudo estiver funcionando, o problema pode estar na API ou na comunicação JavaScript.</p>
        <a href="certificados.php" class="btn btn-primary">📜 Voltar para Certificados</a>
        <a href="dashboard_final.php" class="btn btn-secondary">🏠 Dashboard</a>
    </div>
</body>
</html>









