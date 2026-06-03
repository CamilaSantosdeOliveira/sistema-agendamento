<?php
// Conectar ao banco de dados
include 'db.php';

echo "<h2>🔍 DEBUG: Verificando por que os dados não estão sendo inseridos</h2>";

try {
    // 1. VERIFICAR SE A TABELA CURSOS EXISTE E ESTÁ VAZIA
    echo "<h3>📚 Verificando tabela cursos...</h3>";
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos");
    if ($result) {
        $count = $result->fetch_assoc()['total'];
        echo "✅ Tabela cursos existe com $count registros<br>";
    } else {
        echo "❌ Erro ao contar cursos: " . $conn->error . "<br>";
    }

    // 2. VERIFICAR ESTRUTURA DA TABELA
    echo "<h3>🔧 Estrutura da tabela cursos:</h3>";
    $structure = $conn->query("DESCRIBE cursos");
    if ($structure) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th><th>Extra</th></tr>";
        while ($row = $structure->fetch_assoc()) {
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
    }

    // 3. TENTAR INSERIR UM CURSO SIMPLES
    echo "<h3>🎯 Tentando inserir um curso de teste...</h3>";
    
    $sql_teste = "INSERT INTO cursos (nome, descricao, duracao_horas, nivel, categoria, preco, status, alunos_inscritos, avaliacao, progresso_percentual) 
                   VALUES ('Curso Teste', 'Descrição teste', 10, 'Iniciante', 'Teste', 99.90, 'ativo', 5, 4.5, 50)";
    
    if ($conn->query($sql_teste)) {
        echo "✅ Curso teste inserido com sucesso!<br>";
        
        // Verificar se foi inserido
        $result = $conn->query("SELECT COUNT(*) as total FROM cursos");
        if ($result) {
            $count = $result->fetch_assoc()['total'];
            echo "✅ Agora temos $count cursos na tabela<br>";
        }
    } else {
        echo "❌ Erro ao inserir curso teste: " . $conn->error . "<br>";
    }

    // 4. VERIFICAR SE HÁ PROBLEMAS COM PREPARED STATEMENTS
    echo "<h3>🔍 Testando prepared statement...</h3>";
    
    $sql_prepared = "INSERT INTO cursos (nome, descricao, duracao_horas, nivel, categoria, preco, status, alunos_inscritos, avaliacao, progresso_percentual) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql_prepared);
    if (!$stmt) {
        echo "❌ Erro ao preparar query: " . $conn->error . "<br>";
    } else {
        echo "✅ Prepared statement criado com sucesso<br>";
        
        // Tentar inserir
        $nome = "Curso Prepared";
        $descricao = "Teste com prepared statement";
        $duracao = 20;
        $nivel = "Intermediário";
        $categoria = "Teste";
        $preco = 149.90;
        $status = "ativo";
        $alunos = 10;
        $avaliacao = 4.8;
        $progresso = 60;
        
        $stmt->bind_param("ssisssddsi", $nome, $descricao, $duracao, $nivel, $categoria, $preco, $status, $alunos, $avaliacao, $progresso);
        
        if ($stmt->execute()) {
            echo "✅ Curso com prepared statement inserido com sucesso!<br>";
        } else {
            echo "❌ Erro ao executar prepared statement: " . $stmt->error . "<br>";
        }
        
        $stmt->close();
    }

    // 5. VERIFICAR TOTAL FINAL
    echo "<h3>📊 Total final de cursos:</h3>";
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos");
    if ($result) {
        $count = $result->fetch_assoc()['total'];
        echo "✅ Total de cursos na tabela: <strong>$count</strong><br>";
    }

    // 6. MOSTRAR CURSOS INSERIDOS
    if ($count > 0) {
        echo "<h3>📋 Cursos na tabela:</h3>";
        $cursos = $conn->query("SELECT id, nome, categoria, preco FROM cursos ORDER BY id");
        if ($cursos) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Preço</th></tr>";
            while ($curso = $cursos->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$curso['id']}</td>";
                echo "<td>{$curso['nome']}</td>";
                echo "<td>{$curso['categoria']}</td>";
                echo "<td>R$ {$curso['preco']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }

    echo "<br><h2>🎯 PRÓXIMOS PASSOS:</h2>";
    if ($count > 0) {
        echo "<p>✅ Dados inseridos com sucesso! Agora você pode:</p>";
        echo "<ol>";
        echo "<li><a href='cursos.php'>Ver a página de cursos</a></li>";
        echo "<li><a href='dashboard_corrigido.php'>Acessar o dashboard</a></li>";
        echo "</ol>";
    } else {
        echo "<p>❌ Ainda há problemas. Vamos tentar uma abordagem diferente.</p>";
    }

} catch (Exception $e) {
    echo "❌ Erro durante o debug: " . $e->getMessage();
}
?>





































