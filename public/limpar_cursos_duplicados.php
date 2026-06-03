<?php
echo "<h2>🧹 Limpeza de Cursos Duplicados</h2>";

// Conectar ao banco
include 'db.php';
if ($conn) {
    echo "✅ Conexão com banco OK<br>";
} else {
    echo "❌ Erro na conexão com banco<br>";
    exit;
}

echo "<h3>📊 Antes da limpeza:</h3>";

// Contar cursos antes
$result = $conn->query("SELECT COUNT(*) as total FROM cursos");
$total_antes = $result->fetch_assoc()['total'];

echo "📋 Total de cursos: $total_antes<br>";

// Mostrar cursos que existem
$result = $conn->query("SELECT id, nome, categoria, nivel, preco FROM cursos ORDER BY nome");
if ($result && $result->num_rows > 0) {
    echo "<h3>📚 Cursos existentes:</h3>";
    while ($row = $result->fetch_assoc()) {
        $preco = number_format($row['preco'], 2, ',', '.');
        echo "- ID: {$row['id']} | {$row['nome']} | {$row['categoria']} | {$row['nivel']} | R$ {$preco}<br>";
    }
}

echo "<br><h3>🔍 Identificando duplicados:</h3>";

// Encontrar cursos duplicados por nome
$result = $conn->query("
    SELECT nome, COUNT(*) as quantidade
    FROM cursos 
    GROUP BY nome 
    HAVING COUNT(*) > 1
    ORDER BY nome
");

if ($result && $result->num_rows > 0) {
    echo "📋 Cursos duplicados encontrados:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['nome']} (aparece {$row['quantidade']} vezes)<br>";
    }
    
    echo "<br><h3>⚠️ ATENÇÃO:</h3>";
    echo "<p>Serão mantidos apenas os primeiros registros de cada curso duplicado.</p>";
    
    echo "<br><h3>🧹 Executar limpeza:</h3>";
    echo "<p><a href='?acao=limpar' style='background: #ef4444; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🗑️ LIMPAR DUPLICADOS</a></p>";
    
} else {
    echo "✅ Nenhum curso duplicado encontrado!<br>";
}

// Executar limpeza se solicitado
if (isset($_GET['acao']) && $_GET['acao'] === 'limpar') {
    echo "<h3>🧹 Executando limpeza...</h3>";
    
    try {
        // Remover duplicados mantendo apenas o primeiro de cada nome
        $conn->query("
            DELETE c1 FROM cursos c1
            INNER JOIN cursos c2 
            WHERE c1.id > c2.id 
            AND c1.nome = c2.nome
        ");
        
        echo "✅ Limpeza concluída!<br>";
        
        // Contar após limpeza
        $result = $conn->query("SELECT COUNT(*) as total FROM cursos");
        $total_depois = $result->fetch_assoc()['total'];
        
        echo "<h3>📊 Após a limpeza:</h3>";
        echo "📋 Total de cursos: $total_depois<br>";
        echo "📋 Removidos: " . ($total_antes - $total_depois) . " duplicados<br>";
        
        // Mostrar cursos restantes
        $result = $conn->query("SELECT id, nome, categoria, nivel, preco FROM cursos ORDER BY nome");
        if ($result && $result->num_rows > 0) {
            echo "<h3>📚 Cursos restantes:</h3>";
            while ($row = $result->fetch_assoc()) {
                $preco = number_format($row['preco'], 2, ',', '.');
                echo "- ID: {$row['id']} | {$row['nome']} | {$row['categoria']} | {$row['nivel']} | R$ {$preco}<br>";
            }
        }
        
        echo "<br><h3>✅ Resultado:</h3>";
        echo "<p>Duplicados removidos com sucesso!</p>";
        echo "<p>Agora o dashboard mostrará apenas cursos únicos.</p>";
        
    } catch (Exception $e) {
        echo "❌ Erro na limpeza: " . $e->getMessage() . "<br>";
    }
}

echo "<br><a href='dashboard_corrigido.php'>Voltar ao Dashboard</a>";
?>


