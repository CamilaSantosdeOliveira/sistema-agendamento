<?php
// Conectar ao banco de dados
include 'db.php';

echo "<h2>🔧 Criando Tabela de Cursos</h2>";

try {
    // Criar tabela cursos
    echo "<h3>📚 Criando tabela cursos...</h3>";
    $sql_cursos = "CREATE TABLE IF NOT EXISTS cursos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(200) NOT NULL,
        descricao TEXT,
        duracao_horas INT,
        nivel ENUM('Iniciante', 'Intermediário', 'Avançado', 'Iniciante ao Avançado') DEFAULT 'Intermediário',
        categoria VARCHAR(100),
        preco DECIMAL(10,2) DEFAULT 0.00,
        status ENUM('ativo', 'em_breve', 'inativo') DEFAULT 'ativo',
        alunos_inscritos INT DEFAULT 0,
        avaliacao DECIMAL(3,2) DEFAULT 0.00,
        progresso_percentual INT DEFAULT 0,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql_cursos)) {
        echo "✅ Tabela cursos criada com sucesso!<br>";
    } else {
        echo "❌ Erro ao criar tabela: " . $conn->error . "<br>";
    }

    // Inserir cursos de teste
    echo "<h3>🎯 Inserindo cursos...</h3>";
    
    $cursos = [
        [
            'nome' => 'DevOps com Docker',
            'descricao' => 'Docker, Kubernetes e CI/CD',
            'duracao_horas' => 55,
            'nivel' => 'Intermediário',
            'categoria' => 'DevOps',
            'preco' => 329.90,
            'status' => 'ativo',
            'alunos_inscritos' => 45,
            'avaliacao' => 4.8,
            'progresso_percentual' => 75
        ],
        [
            'nome' => 'Flutter Mobile',
            'descricao' => 'Desenvolvimento mobile multiplataforma',
            'duracao_horas' => 65,
            'nivel' => 'Intermediário',
            'categoria' => 'Mobile',
            'preco' => 379.90,
            'status' => 'ativo',
            'alunos_inscritos' => 38,
            'avaliacao' => 4.7,
            'progresso_percentual' => 68
        ],
        [
            'nome' => 'Java Enterprise',
            'descricao' => 'Java com Spring Boot e microserviços',
            'duracao_horas' => 90,
            'nivel' => 'Avançado',
            'categoria' => 'Desenvolvimento Backend',
            'preco' => 449.90,
            'status' => 'ativo',
            'alunos_inscritos' => 52,
            'avaliacao' => 4.9,
            'progresso_percentual' => 82
        ],
        [
            'nome' => 'JavaScript Completo',
            'descricao' => 'Do básico ao avançado em JavaScript moderno',
            'duracao_horas' => 60,
            'nivel' => 'Iniciante ao Avançado',
            'categoria' => 'Programação Web',
            'preco' => 299.90,
            'status' => 'ativo',
            'alunos_inscritos' => 156,
            'avaliacao' => 4.8,
            'progresso_percentual' => 78
        ],
        [
            'nome' => 'Python para Data Science',
            'descricao' => 'Python com pandas, numpy e machine learning',
            'duracao_horas' => 80,
            'nivel' => 'Intermediário',
            'categoria' => 'Data Science',
            'preco' => 399.90,
            'status' => 'ativo',
            'alunos_inscritos' => 89,
            'avaliacao' => 4.9,
            'progresso_percentual' => 85
        ],
        [
            'nome' => 'React + Node.js',
            'descricao' => 'Full-stack com React e Node.js',
            'duracao_horas' => 70,
            'nivel' => 'Intermediário',
            'categoria' => 'Desenvolvimento Web',
            'preco' => 349.90,
            'status' => 'ativo',
            'alunos_inscritos' => 67,
            'avaliacao' => 4.7,
            'progresso_percentual' => 71
        ]
    ];

    foreach ($cursos as $curso) {
        $sql = "INSERT INTO cursos (nome, descricao, duracao_horas, nivel, categoria, preco, status, alunos_inscritos, avaliacao, progresso_percentual) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo "❌ Erro ao preparar query: " . $conn->error . "<br>";
            continue;
        }
        
        $stmt->bind_param("ssisssddsi",
            $curso['nome'],
            $curso['descricao'],
            $curso['duracao_horas'],
            $curso['nivel'],
            $curso['categoria'],
            $curso['preco'],
            $curso['status'],
            $curso['alunos_inscritos'],
            $curso['avaliacao'],
            $curso['progresso_percentual']
        );
        
        if ($stmt->execute()) {
            echo "✅ Curso '{$curso['nome']}' inserido com sucesso!<br>";
        } else {
            echo "❌ Erro ao inserir curso '{$curso['nome']}': " . $stmt->error . "<br>";
        }
        
        $stmt->close();
    }

    echo "<br><h2>🎉 Cursos Criados com Sucesso!</h2>";
    echo "<p>Agora você pode:</p>";
    echo "<ol>";
    echo "<li><a href='cursos.php'>Ver a página de cursos funcionando</a></li>";
    echo "<li><a href='dashboard_corrigido.php'>Voltar ao dashboard</a></li>";
    echo "</ol>";

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>





































