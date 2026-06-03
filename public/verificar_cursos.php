<?php
echo "<h1>🔍 Verificação de Cursos</h1>";

try {
    include 'db.php';
    
    if ($conn && !$conn->connect_error) {
        echo "<p>✅ Conexão com banco OK</p>";
        
        // Verificar cursos existentes
        echo "<h2>📋 Cursos no Banco:</h2>";
        $result = $conn->query("SELECT id, nome, categoria, nivel, duracao, preco, ativo FROM cursos ORDER BY id");
        
        if ($result && $result->num_rows > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Nível</th><th>Duração</th><th>Preço</th><th>Ativo</th><th>Ação</th></tr>";
            
            while ($curso = $result->fetch_assoc()) {
                $status = $curso['ativo'] ? '✅ Ativo' : '❌ Inativo';
                echo "<tr>";
                echo "<td>" . $curso['id'] . "</td>";
                echo "<td>" . $curso['nome'] . "</td>";
                echo "<td>" . $curso['categoria'] . "</td>";
                echo "<td>" . $curso['nivel'] . "</td>";
                echo "<td>" . $curso['duracao'] . "</td>";
                echo "<td>R$ " . $curso['preco'] . "</td>";
                echo "<td>" . $status . "</td>";
                echo "<td><a href='?acao=excluir&id=" . $curso['id'] . "' onclick='return confirm(\"Excluir curso?\")'>🗑️ Excluir</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ Nenhum curso encontrado</p>";
        }
        
        // Processar exclusão se solicitado
        if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $stmt = $conn->prepare("DELETE FROM cursos WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                echo "<p>✅ Curso ID $id excluído com sucesso!</p>";
                echo "<script>setTimeout(() => window.location.reload(), 1000);</script>";
            } else {
                echo "<p>❌ Erro ao excluir curso</p>";
            }
        }
        
        // Botão para limpar todos os cursos
        echo "<h2>🧹 Ações:</h2>";
        echo "<p><a href='?acao=limpar_todos' onclick='return confirm(\"Excluir TODOS os cursos?\")'>🗑️ Excluir Todos os Cursos</a></p>";
        
        if (isset($_GET['acao']) && $_GET['acao'] === 'limpar_todos') {
            $conn->query("DELETE FROM cursos");
            echo "<p>✅ Todos os cursos foram excluídos!</p>";
            echo "<script>setTimeout(() => window.location.reload(), 1000);</script>";
        }
        
    } else {
        echo "<p>❌ Erro na conexão com banco</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Próximos Passos:</h2>";
echo "<ol>";
echo "<li>Verifique se os cursos listados acima são os corretos</li>";
echo "<li>Se houver cursos antigos, clique em 'Excluir'</li>";
echo "<li>Depois acesse o dashboard novamente</li>";
echo "<li>Limpe o cache do navegador (Ctrl+F5)</li>";
echo "</ol>";
?>








