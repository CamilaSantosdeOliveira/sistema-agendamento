<?php
// Script para verificar dados AGORA no banco
include 'db.php';

echo "<h1>🔍 VERIFICAÇÃO IMEDIATA DOS DADOS</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { color: #10b981; font-weight: bold; }
    .error { color: #ef4444; font-weight: bold; }
    .info { color: #3b82f6; font-weight: bold; }
    .btn { background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
    .btn:hover { background: #2563eb; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f8f9fa; font-weight: bold; }
</style>";

try {
    if (!$conn) {
        throw new Exception("❌ Banco de dados não está disponível");
    }
    
    echo "<div class='section'>";
    echo "<h2>✅ Status da Conexão</h2>";
    echo "<p class='success'>Conexão estabelecida com sucesso!</p>";
    echo "</div>";
    
    // VERIFICAR TABELAS
    echo "<div class='section'>";
    echo "<h2>📋 Tabelas no Banco</h2>";
    $result = $conn->query("SHOW TABLES");
    if ($result && $result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_array()) {
            echo "<li>📄 " . $row[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='error'>Nenhuma tabela encontrada!</p>";
    }
    echo "</div>";
    
    // VERIFICAR USUÁRIOS
    echo "<div class='section'>";
    echo "<h2>👥 Usuários no Sistema</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    $total_usuarios = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>Total de usuários: <strong>$total_usuarios</strong></p>";
    
    if ($total_usuarios > 0) {
        $result = $conn->query("SELECT tipo_usuario, COUNT(*) as count FROM usuarios GROUP BY tipo_usuario");
        echo "<table>";
        echo "<tr><th>Tipo</th><th>Quantidade</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['tipo_usuario']}</td><td>{$row['count']}</td></tr>";
        }
        echo "</table>";
    }
    echo "</div>";
    
    // VERIFICAR PROFESSORES
    echo "<div class='section'>";
    echo "<h2>👨‍🏫 Professores</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'");
    $total_professores = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>Total de professores: <strong>$total_professores</strong></p>";
    
    if ($total_professores > 0) {
        $result = $conn->query("SELECT id, nome, email, formacao, valor_hora, ativo FROM usuarios WHERE tipo_usuario = 'professor' ORDER BY nome");
        echo "<table>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Formação</th><th>Valor/Hora</th><th>Ativo</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $ativo = $row['ativo'] ? '✅' : '❌';
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nome']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['formacao']}</td>";
            echo "<td>R$ {$row['valor_hora']}</td>";
            echo "<td>$ativo</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ Nenhum professor encontrado!</p>";
    }
    echo "</div>";
    
    // VERIFICAR ALUNOS
    echo "<div class='section'>";
    echo "<h2>👨‍🎓 Alunos</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $total_alunos = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>Total de alunos: <strong>$total_alunos</strong></p>";
    
    if ($total_alunos > 0) {
        $result = $conn->query("SELECT id, nome, email, ativo, criado_em FROM usuarios WHERE tipo_usuario = 'aluno' ORDER BY nome");
        echo "<table>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Ativo</th><th>Data Criação</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $ativo = $row['ativo'] ? '✅' : '❌';
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nome']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>$ativo</td>";
            echo "<td>{$row['criado_em']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ Nenhum aluno encontrado!</p>";
    }
    echo "</div>";
    
    // VERIFICAR CURSOS
    echo "<div class='section'>";
    echo "<h2>📚 Cursos</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos");
    $total_cursos = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>Total de cursos: <strong>$total_cursos</strong></p>";
    
    if ($total_cursos > 0) {
        $result = $conn->query("SELECT id, nome, categoria, nivel, duracao_horas, preco, status FROM cursos ORDER BY nome");
        echo "<table>";
        echo "<tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Nível</th><th>Duração</th><th>Preço</th><th>Status</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nome']}</td>";
            echo "<td>{$row['categoria']}</td>";
            echo "<td>{$row['nivel']}</td>";
            echo "<td>{$row['duracao_horas']}h</td>";
            echo "<td>R$ {$row['preco']}</td>";
            echo "<td>{$row['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ Nenhum curso encontrado!</p>";
    }
    echo "</div>";
    
    // RESUMO FINAL
    echo "<div class='section'>";
    echo "<h2>📊 Resumo do Sistema</h2>";
    echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; text-align: center;'>";
    echo "<div style='background: #f0f9ff; padding: 20px; border-radius: 8px;'>";
    echo "<div style='font-size: 32px; font-weight: bold; color: #3b82f6;'>$total_usuarios</div>";
    echo "<div>👥 Usuários</div>";
    echo "</div>";
    echo "<div style='background: #f0f9ff; padding: 20px; border-radius: 8px;'>";
    echo "<div style='font-size: 32px; font-weight: bold; color: #3b82f6;'>$total_professores</div>";
    echo "<div>👨‍🏫 Professores</div>";
    echo "</div>";
    echo "<div style='background: #f0fdf4; padding: 20px; border-radius: 8px;'>";
    echo "<div style='font-size: 32px; font-weight: bold; color: #10b981;'>$total_alunos</div>";
    echo "<div>👨‍🎓 Alunos</div>";
    echo "</div>";
    echo "<div style='background: #fef3c7; padding: 20px; border-radius: 8px;'>";
    echo "<div style='font-size: 32px; font-weight: bold; color: #f59e0b;'>$total_cursos</div>";
    echo "<div>📚 Cursos</div>";
    echo "</div>";
    echo "</div>";
    
    if ($total_professores > 0 && $total_alunos > 0 && $total_cursos > 0) {
        echo "<p class='success'>🎉 Sistema com dados completos!</p>";
    } else {
        echo "<p class='error'>⚠️ Sistema incompleto - faltam dados!</p>";
    }
    echo "</div>";
    
    // LINKS PARA ACESSO
    echo "<div class='section'>";
    echo "<h2>🚀 Acessar o Sistema</h2>";
    echo "<p>Clique nos links abaixo para acessar o sistema:</p>";
    echo "<a href='dashboard_final.php' class='btn'>📊 Dashboard</a>";
    echo "<a href='login.html' class='btn'>🔐 Login</a>";
    echo "<a href='carregar_dados_completos.php' class='btn'>🔄 Recarregar Dados</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='section'>";
    echo "<h2>❌ Erro</h2>";
    echo "<p class='error'>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>










