<?php
// Conectar ao banco de dados
include 'db.php';

echo "<h2>🔍 Verificando Estrutura da Tabela Cursos</h2>";

try {
    // Verificar se a tabela existe
    $result = $conn->query("SHOW TABLES LIKE 'cursos'");
    if ($result->num_rows > 0) {
        echo "✅ Tabela 'cursos' existe<br>";
        
        // Verificar estrutura da tabela
        echo "<h3>📋 Estrutura atual da tabela:</h3>";
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
        
        // Verificar se a coluna status existe
        $status_check = $conn->query("SHOW COLUMNS FROM cursos LIKE 'status'");
        if ($status_check->num_rows == 0) {
            echo "<h3>⚠️ Coluna 'status' não existe! Adicionando...</h3>";
            
            // Adicionar coluna status
            $add_status = "ALTER TABLE cursos ADD COLUMN status ENUM('ativo', 'em_breve', 'inativo') DEFAULT 'ativo' AFTER preco";
            if ($conn->query($add_status)) {
                echo "✅ Coluna 'status' adicionada com sucesso!<br>";
            } else {
                echo "❌ Erro ao adicionar coluna 'status': " . $conn->error . "<br>";
            }
        } else {
            echo "✅ Coluna 'status' já existe<br>";
        }
        
        // Verificar outras colunas que podem estar faltando
        $required_columns = [
            'alunos_inscritos' => "ALTER TABLE cursos ADD COLUMN alunos_inscritos INT DEFAULT 0 AFTER status",
            'avaliacao' => "ALTER TABLE cursos ADD COLUMN avaliacao DECIMAL(3,2) DEFAULT 0.00 AFTER alunos_inscritos",
            'progresso_percentual' => "ALTER TABLE cursos ADD COLUMN progresso_percentual INT DEFAULT 0 AFTER avaliacao"
        ];
        
        foreach ($required_columns as $column => $sql) {
            $check = $conn->query("SHOW COLUMNS FROM cursos LIKE '$column'");
            if ($check->num_rows == 0) {
                echo "<h3>⚠️ Coluna '$column' não existe! Adicionando...</h3>";
                if ($conn->query($sql)) {
                    echo "✅ Coluna '$column' adicionada com sucesso!<br>";
                } else {
                    echo "❌ Erro ao adicionar coluna '$column': " . $conn->error . "<br>";
                }
            } else {
                echo "✅ Coluna '$column' já existe<br>";
            }
        }
        
        // Agora tentar inserir os cursos novamente
        echo "<h3>🎯 Tentando inserir cursos novamente...</h3>";
        
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

        $sucessos = 0;
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
                $sucessos++;
            } else {
                echo "❌ Erro ao inserir curso '{$curso['nome']}': " . $stmt->error . "<br>";
            }
            
            $stmt->close();
        }
        
        echo "<br><h2>🎉 Processo Concluído!</h2>";
        echo "<p>Total de cursos inseridos com sucesso: <strong>$sucessos</strong></p>";
        
        if ($sucessos > 0) {
            echo "<p>Agora você pode:</p>";
            echo "<ol>";
            echo "<li><a href='cursos.php'>Ver a página de cursos funcionando</a></li>";
            echo "<li><a href='dashboard_corrigido.php'>Voltar ao dashboard</a></li>";
            echo "</ol>";
        }
        
    } else {
        echo "❌ Tabela 'cursos' não existe!<br>";
        echo "<p><a href='criar_tabela_cursos.php'>Clique aqui para criar a tabela</a></p>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>





































