<?php
// Script SIMPLES para carregar dados AGORA
include 'db.php';

echo "<h1>🚀 CARREGANDO DADOS AGORA!</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { color: #10b981; font-weight: bold; }
    .error { color: #ef4444; font-weight: bold; }
    .info { color: #3b82f6; font-weight: bold; }
    .btn { background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
    .btn:hover { background: #2563eb; }
    .btn-success { background: #10b981; }
</style>";

try {
    if (!$conn) {
        throw new Exception("❌ Banco de dados não está disponível");
    }
    
    echo "<div class='section'>";
    echo "<h2>✅ Banco conectado - Vamos carregar tudo!</h2>";
    echo "</div>";
    
    // INSERIR PROFESSORES
    echo "<div class='section'>";
    echo "<h2>👨‍🏫 Inserindo Professores</h2>";
    
    $professores = [
        ['nome' => 'Prof. Ricardo Silva', 'email' => 'ricardo.silva@educonnect.com', 'formacao' => 'Engenharia de Computação - ITA', 'valor_hora' => 90.00],
        ['nome' => 'Profa. Fernanda Costa', 'email' => 'fernanda.costa@educonnect.com', 'formacao' => 'Ciência da Computação - UFMG', 'valor_hora' => 85.00],
        ['nome' => 'Prof. Diego Santos', 'email' => 'diego.santos@educonnect.com', 'formacao' => 'Estatística e Ciência de Dados - USP', 'valor_hora' => 95.00],
        ['nome' => 'Profa. Juliana Lima', 'email' => 'juliana.lima@educonnect.com', 'formacao' => 'Design Digital - PUC-Rio', 'valor_hora' => 75.00],
        ['nome' => 'Prof. André Oliveira', 'email' => 'andre.oliveira@educonnect.com', 'formacao' => 'Sistemas de Informação - UFSCar', 'valor_hora' => 80.00],
        ['nome' => 'Profa. Camila Rodrigues', 'email' => 'camila.rodrigues@educonnect.com', 'formacao' => 'Ciência da Computação - UNICAMP', 'valor_hora' => 70.00],
        ['nome' => 'Prof. Marcelo Ferreira', 'email' => 'marcelo.ferreira@educonnect.com', 'formacao' => 'Engenharia de Software - UFPE', 'valor_hora' => 85.00],
        ['nome' => 'Profa. Patrícia Alves', 'email' => 'patricia.alves@educonnect.com', 'formacao' => 'Sistemas de Informação - UFRGS', 'valor_hora' => 75.00]
    ];
    
    $professores_inseridos = 0;
    foreach ($professores as $prof) {
        $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, formacao, valor_hora, ativo, criado_em) 
                VALUES (?, ?, ?, 'professor', ?, ?, 1, NOW())";
        $stmt = $conn->prepare($sql);
        $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
        $stmt->bind_param("ssssd", $prof['nome'], $prof['email'], $senha_hash, $prof['formacao'], $prof['valor_hora']);
        
        if ($stmt->execute()) {
            echo "<p class='success'>✅ {$prof['nome']} - R$ {$prof['valor_hora']}/h</p>";
            $professores_inseridos++;
        } else {
            echo "<p class='error'>❌ Erro ao inserir {$prof['nome']}</p>";
        }
    }
    echo "<p class='info'>📊 Professores inseridos: $professores_inseridos</p>";
    echo "</div>";
    
    // INSERIR ALUNOS
    echo "<div class='section'>";
    echo "<h2>👨‍🎓 Inserindo Alunos</h2>";
    
    $alunos = [
        ['nome' => 'João Silva', 'email' => 'joao.silva@email.com'],
        ['nome' => 'Maria Santos', 'email' => 'maria.santos@email.com'],
        ['nome' => 'Pedro Oliveira', 'email' => 'pedro.oliveira@email.com'],
        ['nome' => 'Ana Costa', 'email' => 'ana.costa@email.com'],
        ['nome' => 'Carlos Ferreira', 'email' => 'carlos.ferreira@email.com'],
        ['nome' => 'Lucia Mendes', 'email' => 'lucia.mendes@email.com'],
        ['nome' => 'Roberto Alves', 'email' => 'roberto.alves@email.com'],
        ['nome' => 'Fernanda Lima', 'email' => 'fernanda.lima@email.com'],
        ['nome' => 'Gabriel Souza', 'email' => 'gabriel.souza@email.com'],
        ['nome' => 'Isabela Martins', 'email' => 'isabela.martins@email.com']
    ];
    
    $alunos_inseridos = 0;
    foreach ($alunos as $aluno) {
        $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo, criado_em) 
                VALUES (?, ?, ?, 'aluno', 1, NOW())";
        $stmt = $conn->prepare($sql);
        $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $aluno['nome'], $aluno['email'], $senha_hash);
        
        if ($stmt->execute()) {
            echo "<p class='success'>✅ {$aluno['nome']}</p>";
            $alunos_inseridos++;
        } else {
            echo "<p class='error'>❌ Erro ao inserir {$aluno['nome']}</p>";
        }
    }
    echo "<p class='info'>📊 Alunos inseridos: $alunos_inseridos</p>";
    echo "</div>";
    
    // INSERIR CURSOS
    echo "<div class='section'>";
    echo "<h2>📚 Inserindo Cursos</h2>";
    
    $cursos = [
        ['nome' => 'Desenvolvimento Web Full Stack', 'categoria' => 'Programação', 'nivel' => 'Intermediário', 'duracao_horas' => 80, 'preco' => 299.90, 'descricao' => 'Curso completo de desenvolvimento web'],
        ['nome' => 'Python para Data Science', 'categoria' => 'Data Science', 'nivel' => 'Avançado', 'duracao_horas' => 100, 'preco' => 399.90, 'descricao' => 'Análise de dados com Python'],
        ['nome' => 'React.js e Node.js', 'categoria' => 'Programação', 'nivel' => 'Intermediário', 'duracao_horas' => 60, 'preco' => 249.90, 'descricao' => 'Desenvolvimento full-stack com React e Node'],
        ['nome' => 'UX/UI Design', 'categoria' => 'Design', 'nivel' => 'Básico', 'duracao_horas' => 50, 'preco' => 199.90, 'descricao' => 'Design de interfaces e experiência do usuário'],
        ['nome' => 'DevOps e Docker', 'categoria' => 'DevOps', 'nivel' => 'Avançado', 'duracao_horas' => 90, 'preco' => 349.90, 'descricao' => 'DevOps e containerização com Docker'],
        ['nome' => 'Mobile App Development', 'categoria' => 'Mobile', 'nivel' => 'Intermediário', 'duracao_horas' => 70, 'preco' => 279.90, 'descricao' => 'Desenvolvimento de aplicativos móveis'],
        ['nome' => 'JavaScript Avançado', 'categoria' => 'Programação', 'nivel' => 'Avançado', 'duracao_horas' => 45, 'preco' => 189.90, 'descricao' => 'JavaScript moderno e frameworks'],
        ['nome' => 'PHP e Laravel', 'categoria' => 'Programação', 'nivel' => 'Intermediário', 'duracao_horas' => 65, 'preco' => 229.90, 'descricao' => 'Desenvolvimento backend com PHP e Laravel']
    ];
    
    $cursos_inseridos = 0;
    foreach ($cursos as $curso) {
        $sql = "INSERT INTO cursos (nome, categoria, nivel, duracao_horas, preco, descricao, status, alunos_inscritos, avaliacao) 
                VALUES (?, ?, ?, ?, ?, ?, 'ativo', 0, 0.00)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssids", $curso['nome'], $curso['categoria'], $curso['nivel'], $curso['duracao_horas'], $curso['preco'], $curso['descricao']);
        
        if ($stmt->execute()) {
            echo "<p class='success'>✅ {$curso['nome']} - R$ {$curso['preco']}</p>";
            $cursos_inseridos++;
        } else {
            echo "<p class='error'>❌ Erro ao inserir {$curso['nome']}</p>";
        }
    }
    echo "<p class='info'>📊 Cursos inseridos: $cursos_inseridos</p>";
    echo "</div>";
    
    // VERIFICAÇÃO FINAL
    echo "<div class='section'>";
    echo "<h2>🎉 DADOS CARREGADOS COM SUCESSO!</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'");
    $total_professores = $result ? $result->fetch_assoc()['total'] : 0;
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $total_alunos = $result ? $result->fetch_assoc()['total'] : 0;
    
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos");
    $total_cursos = $result ? $result->fetch_assoc()['total'] : 0;
    
    echo "<div style='display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center; margin: 20px 0;'>";
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
    
    echo "<p class='success'>🎉 Todos os dados foram carregados com sucesso!</p>";
    echo "<p>✅ $total_professores Professores</p>";
    echo "<p>✅ $total_alunos Alunos</p>";
    echo "<p>✅ $total_cursos Cursos</p>";
    echo "</div>";
    
    // LINKS PARA ACESSO
    echo "<div class='section'>";
    echo "<h2>🚀 Acessar o Sistema</h2>";
    echo "<p>Clique nos links abaixo para acessar o sistema:</p>";
    echo "<a href='dashboard_final.php' class='btn btn-success'>📊 Acessar Dashboard</a>";
    echo "<a href='login.html' class='btn'>🔐 Login</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='section'>";
    echo "<h2>❌ Erro</h2>";
    echo "<p class='error'>" . $e->getMessage() . "</p>";
    echo "<p>Verifique se o MySQL está rodando no XAMPP Control Panel.</p>";
    echo "</div>";
}
?>










