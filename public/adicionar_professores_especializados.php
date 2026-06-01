<?php
// Script para adicionar professores especializados para todos os cursos
include 'db.php';

echo "<h1>👨‍🏫 Adicionando Professores Especializados</h1>";
echo "<p>Expandindo o quadro de professores para cobrir todos os cursos disponíveis...</p>";

try {
    // Verificar se o banco está conectado
    if (!$conn) {
        throw new Exception("Banco de dados não está disponível");
    }
    
    // Lista de novos professores especializados
    $novos_professores = [
        [
            'nome' => 'Prof. Ricardo Silva',
            'email' => 'ricardo.silva@educonnect.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 99999-1111',
            'formacao' => 'Engenharia de Computação - ITA',
            'experiencia' => '10+',
            'valor_hora' => 90.00,
            'descricao' => 'Especialista em DevOps, Docker e automação de infraestrutura. 8 anos de experiência em empresas de tecnologia.',
            'materias' => ['devops', 'docker', 'kubernetes']
        ],
        [
            'nome' => 'Profa. Fernanda Costa',
            'email' => 'fernanda.costa@educonnect.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 99999-2222',
            'formacao' => 'Ciência da Computação - UFMG',
            'experiencia' => '6-10',
            'valor_hora' => 85.00,
            'descricao' => 'Desenvolvedora mobile com experiência em React Native e Flutter. Especialista em apps multiplataforma.',
            'materias' => ['mobile', 'react_native', 'flutter']
        ],
        [
            'nome' => 'Prof. Diego Santos',
            'email' => 'diego.santos@educonnect.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 99999-3333',
            'formacao' => 'Estatística e Ciência de Dados - USP',
            'experiencia' => '8-12',
            'valor_hora' => 95.00,
            'descricao' => 'Cientista de dados com mestrado em Machine Learning. Especialista em Python, pandas, scikit-learn e visualização.',
            'materias' => ['python', 'data_science', 'machine_learning']
        ],
        [
            'nome' => 'Profa. Juliana Lima',
            'email' => 'juliana.lima@educonnect.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 99999-4444',
            'formacao' => 'Design Digital - PUC-Rio',
            'experiencia' => '5-8',
            'valor_hora' => 75.00,
            'descricao' => 'UX/UI Designer certificada com experiência em Figma, Adobe XD e design systems. Especialista em experiência do usuário.',
            'materias' => ['ux_ui', 'figma', 'design']
        ],
        [
            'nome' => 'Prof. André Oliveira',
            'email' => 'andre.oliveira@educonnect.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 99999-5555',
            'formacao' => 'Sistemas de Informação - UFSCar',
            'experiencia' => '7-10',
            'valor_hora' => 80.00,
            'descricao' => 'Desenvolvedor full-stack especializado em React.js e Node.js. Experiência em aplicações web modernas e APIs.',
            'materias' => ['react', 'nodejs', 'javascript']
        ],
        [
            'nome' => 'Profa. Camila Rodrigues',
            'email' => 'camila.rodrigues@educonnect.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 99999-6666',
            'formacao' => 'Ciência da Computação - UNICAMP',
            'experiencia' => '4-7',
            'valor_hora' => 70.00,
            'descricao' => 'Especialista em desenvolvimento web front-end. Experiência em HTML5, CSS3, JavaScript moderno e frameworks.',
            'materias' => ['html', 'css', 'javascript', 'web']
        ],
        [
            'nome' => 'Prof. Marcelo Ferreira',
            'email' => 'marcelo.ferreira@educonnect.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 99999-7777',
            'formacao' => 'Engenharia de Software - UFPE',
            'experiencia' => '9-12',
            'valor_hora' => 85.00,
            'descricao' => 'Especialista em PHP avançado, Laravel e arquitetura de software. Experiência em sistemas empresariais.',
            'materias' => ['php', 'laravel', 'backend']
        ],
        [
            'nome' => 'Profa. Patrícia Alves',
            'email' => 'patricia.alves@educonnect.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 99999-8888',
            'formacao' => 'Sistemas de Informação - UFRGS',
            'experiencia' => '6-9',
            'valor_hora' => 75.00,
            'descricao' => 'Especialista em banco de dados e SQL. Experiência em MySQL, PostgreSQL e modelagem de dados.',
            'materias' => ['sql', 'mysql', 'database']
        ]
    ];
    
    $sucessos = 0;
    $erros = 0;
    
    foreach ($novos_professores as $professor) {
        // Verificar se o professor já existe
        $check_sql = "SELECT id FROM usuarios WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $professor['email']);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<p>⚠️ Professor <strong>{$professor['nome']}</strong> já existe no sistema</p>";
            $erros++;
            continue;
        }
        
        // Inserir novo professor
        $sql = "INSERT INTO usuarios (nome, email, senha, telefone, tipo_usuario, formacao, experiencia, valor_hora, descricao, ativo, criado_em) 
                VALUES (?, ?, ?, ?, 'professor', ?, ?, ?, ?, 1, NOW())";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssssssds", 
                $professor['nome'],
                $professor['email'],
                $professor['senha'],
                $professor['telefone'],
                $professor['formacao'],
                $professor['experiencia'],
                $professor['valor_hora'],
                $professor['descricao']
            );
            
            if ($stmt->execute()) {
                echo "<p>✅ <strong>{$professor['nome']}</strong> adicionado:</p>";
                echo "<ul>";
                echo "<li>Formação: <strong>{$professor['formacao']}</strong></li>";
                echo "<li>Valor/Hora: <strong>R$ " . number_format($professor['valor_hora'], 2, ',', '.') . "/h</strong></li>";
                echo "<li>Especialidades: <strong>" . implode(', ', $professor['materias']) . "</strong></li>";
                echo "</ul>";
                $sucessos++;
            } else {
                echo "<p>❌ Erro ao adicionar <strong>{$professor['nome']}</strong>: " . $stmt->error . "</p>";
                $erros++;
            }
            $stmt->close();
        } else {
            echo "<p>❌ Erro na preparação da query para <strong>{$professor['nome']}</strong></p>";
            $erros++;
        }
    }
    
    echo "<hr>";
    echo "<h2>📊 Resumo da Adição:</h2>";
    echo "<p>✅ Professores adicionados com sucesso: <strong>$sucessos</strong></p>";
    echo "<p>❌ Erros encontrados: <strong>$erros</strong></p>";
    
    if ($sucessos > 0) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>🎉 Professores Adicionados!</h3>";
        echo "<p>Agora o sistema tem <strong>" . ($sucessos + 3) . " professores</strong> para cobrir todos os cursos.</p>";
        echo "<p><a href='sistema_usuarios.php' style='color: #155724; font-weight: bold;'>👥 Ver Lista Completa de Professores</a></p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Erro na Adição</h3>";
    echo "<p>Erro: " . $e->getMessage() . "</p>";
    echo "<p>Verifique se o MySQL está rodando no XAMPP Control Panel.</p>";
    echo "</div>";
}

// Mostrar todos os professores
echo "<hr>";
echo "<h2>👨‍🏫 Todos os Professores no Sistema:</h2>";

try {
    $sql = "SELECT nome, formacao, valor_hora, email FROM usuarios WHERE tipo_usuario = 'professor' ORDER BY nome";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<table style='width: 100%; border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Nome</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Email</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Formação</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Valor/Hora</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'><strong>" . htmlspecialchars($row['nome']) . "</strong></td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'>" . htmlspecialchars($row['formacao']) . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'>R$ " . number_format($row['valor_hora'], 2, ',', '.') . "/h</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Nenhum professor encontrado no sistema.</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro ao buscar professores: " . $e->getMessage() . "</p>";
}

$conn->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
}
h1, h2, h3 {
    color: #333;
}
p {
    margin: 10px 0;
}
ul {
    margin: 5px 0;
    padding-left: 20px;
}
hr {
    border: none;
    border-top: 1px solid #ddd;
    margin: 20px 0;
}
</style>















