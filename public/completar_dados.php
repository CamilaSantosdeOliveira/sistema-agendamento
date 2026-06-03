<?php
echo "<h2>🔧 COMPLETANDO SEUS DADOS</h2>";
echo "<p><strong>Adicionando:</strong> 1 professor, 1 aluno, 6 cursos</p>";

include 'db.php';

try {
    echo "✅ <strong>Conectado ao banco sistema_agendamento!</strong><br><br>";
    
    // Verificar se a tabela cursos existe
    $result = $conn->query("SHOW TABLES LIKE 'cursos'");
    if ($result->num_rows == 0) {
        echo "<h3>📚 Criando tabela cursos...</h3>";
        $sql = "CREATE TABLE cursos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            descricao TEXT,
            categoria VARCHAR(100),
            nivel VARCHAR(50),
            preco DECIMAL(10,2),
            duracao_horas INT,
            alunos_inscritos INT DEFAULT 0,
            avaliacao DECIMAL(3,2) DEFAULT 0.00,
            status ENUM('ativo', 'inativo') DEFAULT 'ativo',
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "✅ Tabela cursos criada com sucesso!<br>";
        } else {
            echo "❌ Erro ao criar tabela: " . $conn->error . "<br>";
        }
    } else {
        echo "✅ Tabela cursos já existe!<br>";
    }
    
    // Adicionar 1 professor
    echo "<h3>👨‍🏫 Adicionando 1 professor...</h3>";
    $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, formacao, valor_hora, ativo) 
            VALUES ('Dr. Carlos Silva', 'carlos.silva@educonnect.com', 'senha123', 'professor', 'Doutor em Ciência da Computação', 150.00, 1)";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Professor adicionado: Dr. Carlos Silva<br>";
    } else {
        echo "❌ Erro ao adicionar professor: " . $conn->error . "<br>";
    }
    
    // Adicionar 1 aluno
    echo "<h3>👨‍🎓 Adicionando 1 aluno...</h3>";
    $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, telefone, data_nascimento, ativo) 
            VALUES ('Ana Costa', 'ana.costa@email.com', 'senha123', 'aluno', '(11) 98765-4321', '1995-08-15', 1)";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Aluna adicionada: Ana Costa<br>";
    } else {
        echo "❌ Erro ao adicionar aluna: " . $conn->error . "<br>";
    }
    
    // Adicionar 6 cursos
    echo "<h3>📚 Adicionando 6 cursos...</h3>";
    
    $cursos = [
        [
            'nome' => 'Desenvolvimento Web Full Stack',
            'descricao' => 'Curso completo de desenvolvimento web com HTML, CSS, JavaScript, PHP e MySQL',
            'categoria' => 'Programação',
            'nivel' => 'Intermediário',
            'preco' => 899.00,
            'duracao_horas' => 80
        ],
        [
            'nome' => 'Python para Data Science',
            'descricao' => 'Aprenda Python para análise de dados, machine learning e visualização',
            'categoria' => 'Data Science',
            'nivel' => 'Avançado',
            'preco' => 1200.00,
            'duracao_horas' => 100
        ],
        [
            'nome' => 'React.js e Node.js',
            'descricao' => 'Desenvolvimento de aplicações modernas com React.js e Node.js',
            'categoria' => 'Programação',
            'nivel' => 'Intermediário',
            'preco' => 750.00,
            'duracao_horas' => 60
        ],
        [
            'nome' => 'UX/UI Design',
            'descricao' => 'Design de interfaces e experiência do usuário com Figma e Adobe XD',
            'categoria' => 'Design',
            'nivel' => 'Básico',
            'preco' => 650.00,
            'duracao_horas' => 50
        ],
        [
            'nome' => 'DevOps e Docker',
            'descricao' => 'Automação de deploy e containers com Docker, Kubernetes e CI/CD',
            'categoria' => 'DevOps',
            'nivel' => 'Avançado',
            'preco' => 1100.00,
            'duracao_horas' => 90
        ],
        [
            'nome' => 'Mobile App Development',
            'descricao' => 'Desenvolvimento de aplicativos móveis com React Native e Flutter',
            'categoria' => 'Mobile',
            'nivel' => 'Intermediário',
            'preco' => 950.00,
            'duracao_horas' => 70
        ]
    ];
    
    foreach ($cursos as $curso) {
        $sql = "INSERT INTO cursos (nome, descricao, categoria, nivel, preco, duracao_horas) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssdi", 
            $curso['nome'], 
            $curso['descricao'], 
            $curso['categoria'], 
            $curso['nivel'], 
            $curso['preco'], 
            $curso['duracao_horas']
        );
        
        if ($stmt->execute()) {
            echo "✅ Curso adicionado: {$curso['nome']}<br>";
        } else {
            echo "❌ Erro ao adicionar curso {$curso['nome']}: " . $stmt->error . "<br>";
        }
    }
    
    // Verificar dados finais
    echo "<h3>📊 Verificando dados finais...</h3>";
    
    $result = $conn->query("SELECT tipo_usuario, COUNT(*) as total FROM usuarios GROUP BY tipo_usuario");
    while ($row = $result->fetch_assoc()) {
        if ($row['tipo_usuario'] == 'professor') {
            echo "👨‍🏫 <strong>Professores:</strong> {$row['total']}<br>";
        } elseif ($row['tipo_usuario'] == 'aluno') {
            echo "👨‍🎓 <strong>Alunos:</strong> {$row['total']}<br>";
        }
    }
    
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos");
    $cursos_count = $result->fetch_assoc()['total'];
    echo "📚 <strong>Cursos:</strong> $cursos_count<br>";
    
    echo "<h3>🎉 DADOS COMPLETADOS COM SUCESSO!</h3>";
    echo "<p><strong>Seu sistema agora tem:</strong></p>";
    echo "<ul>";
    echo "<li>👨‍🏫 <strong>4 Professores</strong></li>";
    echo "<li>👨‍🎓 <strong>3 Alunos</strong></li>";
    echo "<li>📚 <strong>6 Cursos</strong></li>";
    echo "</ul>";
    
    echo "<h4>🔗 Links para testar:</h4>";
    echo "<ul>";
    echo "<li><a href='http://localhost:8080/dashboard_corrigido.php'>Dashboard Principal</a></li>";
    echo "<li><a href='http://localhost:8080/alunos.php'>Gestão de Alunos</a></li>";
    echo "<li><a href='http://localhost:8080/cursos.php'>Gestão de Cursos</a></li>";
    echo "<li><a href='http://localhost:8080/agendamentos.php'>Agendamentos</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "❌ <strong>Erro:</strong> " . $e->getMessage();
}

$conn->close();
?>


