<?php
echo "<h2>🧪 Teste da API de Cursos</h2>";

// Testar conexão com banco
echo "<h3>1. Testando conexão com banco:</h3>";
include 'db.php';
if ($conn) {
    echo "✅ Conexão com banco OK<br>";
} else {
    echo "❌ Erro na conexão com banco<br>";
}

// Testar consulta de cursos
echo "<h3>2. Testando consulta de cursos:</h3>";
try {
    $query = "SELECT id, nome, categoria, nivel FROM cursos LIMIT 3";
    $result = $conn->query($query);
    
    if ($result) {
        echo "✅ Consulta executada com sucesso<br>";
        echo "Cursos encontrados: " . $result->num_rows . "<br>";
        
        while ($row = $result->fetch_assoc()) {
            echo "- ID: {$row['id']}, Nome: {$row['nome']}, Categoria: {$row['categoria']}<br>";
        }
    } else {
        echo "❌ Erro na consulta: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>";
}

// Testar estrutura da tabela
echo "<h3>3. Estrutura da tabela cursos:</h3>";
try {
    $result = $conn->query("DESCRIBE cursos");
    if ($result) {
        echo "✅ Estrutura da tabela:<br>";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Field']}: {$row['Type']}<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar estrutura: " . $e->getMessage() . "<br>";
}

echo "<br><a href='cursos_completo.php'>Voltar para Cursos</a>";
?>
