<?php
// Script para verificar todos os dados do banco de dados
include 'db.php';

echo "<h1>🔍 Verificação Completa do Banco de Dados</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { color: #10b981; font-weight: bold; }
    .error { color: #ef4444; font-weight: bold; }
    .info { color: #3b82f6; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f8f9fa; font-weight: bold; }
    .count { font-size: 24px; font-weight: bold; color: #3b82f6; }
</style>";

try {
    // Verificar conexão
    if (!$conn) {
        throw new Exception("❌ Banco de dados não está disponível");
    }
    
    echo "<div class='section'>";
    echo "<h2>✅ Conexão com Banco de Dados</h2>";
    echo "<p class='success'>Conexão estabelecida com sucesso!</p>";
    echo "</div>";
    
    // Verificar tabelas
    echo "<div class='section'>";
    echo "<h2>📋 Tabelas Disponíveis</h2>";
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
    
    // Verificar usuários
    echo "<div class='section'>";
    echo "<h2>👥 Usuários no Sistema</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    $total_usuarios = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>Total de usuários: <span class='count'>$total_usuarios</span></p>";
    
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
    
    // Verificar professores
    echo "<div class='section'>";
    echo "<h2>👨‍🏫 Professores</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'");
    $total_professores = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>Total de professores: <span class='count'>$total_professores</span></p>";
    
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
        echo "<p class='error'>Nenhum professor encontrado!</p>";
    }
    echo "</div>";
    
    // Verificar alunos
    echo "<div class='section'>";
    echo "<h2>👨‍🎓 Alunos</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $total_alunos = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>Total de alunos: <span class='count'>$total_alunos</span></p>";
    
    if ($total_alunos > 0) {
        $result = $conn->query("SELECT id, nome, email, ativo, data_cadastro FROM usuarios WHERE tipo_usuario = 'aluno' ORDER BY nome");
        echo "<table>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Ativo</th><th>Data Cadastro</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $ativo = $row['ativo'] ? '✅' : '❌';
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nome']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>$ativo</td>";
            echo "<td>{$row['data_cadastro']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>Nenhum aluno encontrado!</p>";
    }
    echo "</div>";
    
    // Verificar cursos
    echo "<div class='section'>";
    echo "<h2>📚 Cursos</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos");
    $total_cursos = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>Total de cursos: <span class='count'>$total_cursos</span></p>";
    
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
        echo "<p class='error'>Nenhum curso encontrado!</p>";
    }
    echo "</div>";
    
    // Verificar agendamentos
    echo "<div class='section'>";
    echo "<h2>📅 Agendamentos</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos");
    $total_agendamentos = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>Total de agendamentos: <span class='count'>$total_agendamentos</span></p>";
    
    if ($total_agendamentos > 0) {
        $result = $conn->query("SELECT id, nome, professor, servico, data, hora, status FROM agendamentos ORDER BY data DESC LIMIT 10");
        echo "<table>";
        echo "<tr><th>ID</th><th>Aluno</th><th>Professor</th><th>Serviço</th><th>Data</th><th>Hora</th><th>Status</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nome']}</td>";
            echo "<td>{$row['professor']}</td>";
            echo "<td>{$row['servico']}</td>";
            echo "<td>{$row['data']}</td>";
            echo "<td>{$row['hora']}</td>";
            echo "<td>{$row['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>Nenhum agendamento encontrado!</p>";
    }
    echo "</div>";
    
    // Resumo final
    echo "<div class='section'>";
    echo "<h2>📊 Resumo do Sistema</h2>";
    echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; text-align: center;'>";
    echo "<div><div class='count'>$total_usuarios</div><div>Usuários</div></div>";
    echo "<div><div class='count'>$total_professores</div><div>Professores</div></div>";
    echo "<div><div class='count'>$total_alunos</div><div>Alunos</div></div>";
    echo "<div><div class='count'>$total_cursos</div><div>Cursos</div></div>";
    echo "</div>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='section'>";
    echo "<h2>❌ Erro</h2>";
    echo "<p class='error'>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>

