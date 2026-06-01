<?php
echo "<h2>🧪 Teste de Inscrição Simples</h2>";

// Testar conexão com banco
echo "<h3>1. Testando conexão com banco:</h3>";
include 'db.php';
if ($conn) {
    echo "✅ Conexão com banco OK<br>";
} else {
    echo "❌ Erro na conexão com banco<br>";
}

// Testar estrutura da tabela inscricoes
echo "<h3>2. Estrutura da tabela inscricoes:</h3>";
try {
    $result = $conn->query("DESCRIBE inscricoes");
    if ($result) {
        echo "✅ Estrutura da tabela inscricoes:<br>";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Field']}: {$row['Type']}<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar estrutura: " . $e->getMessage() . "<br>";
}

// Testar estrutura da tabela usuarios
echo "<h3>3. Estrutura da tabela usuarios:</h3>";
try {
    $result = $conn->query("DESCRIBE usuarios");
    if ($result) {
        echo "✅ Estrutura da tabela usuarios:<br>";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Field']}: {$row['Type']}<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar estrutura: " . $e->getMessage() . "<br>";
}

// Testar inserção manual
echo "<h3>4. Testando inserção manual:</h3>";
try {
    // Primeiro, criar um aluno
    $nome = "Teste Aluno";
    $email = "teste@teste.com";
    $senha = "senha123";
    $telefone = "11999999999";
    
    $insert_aluno = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, telefone, ativo, criado_em) VALUES (?, ?, ?, 'aluno', ?, 1, NOW())";
    $stmt = $conn->prepare($insert_aluno);
    $stmt->bind_param('ssss', $nome, $email, $senha, $telefone);
    
    if ($stmt->execute()) {
        $aluno_id = $conn->insert_id;
        echo "✅ Aluno criado com ID: $aluno_id<br>";
        
        // Agora criar inscrição
        $curso_id = 1;
        $inscricao_query = "INSERT INTO inscricoes (curso_id, aluno_id, data_inicio, observacoes, status, criado_em) VALUES (?, ?, NOW(), ?, 'ativa', NOW())";
        $inscricao_stmt = $conn->prepare($inscricao_query);
        $inscricao_stmt->bind_param('iis', $curso_id, $aluno_id, $telefone);
        
        if ($inscricao_stmt->execute()) {
            echo "✅ Inscrição criada com sucesso!<br>";
        } else {
            echo "❌ Erro ao criar inscrição: " . $inscricao_stmt->error . "<br>";
        }
    } else {
        echo "❌ Erro ao criar aluno: " . $stmt->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>";
}

echo "<br><a href='cursos_completo.php'>Voltar para Cursos</a>";
?>
