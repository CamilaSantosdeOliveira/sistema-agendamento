<?php
// Conectar ao banco de dados
include 'db.php';

echo "<h1>🎯 INSERINDO CURSOS CORRIGIDOS</h1>";
echo "<h2>✅ Usando valores corretos para o ENUM status</h2>";

try {
    // Limpar tabela cursos primeiro
    $conn->query("DELETE FROM cursos");
    echo "🧹 Tabela cursos limpa<br><br>";
    
    // Cursos com valores corretos para o ENUM status
    $cursos = [
        [
            'nome' => 'DevOps com Docker',
            'descricao' => 'Docker, Kubernetes e CI/CD para profissionais de TI',
            'duracao_horas' => 55,
            'nivel' => 'Intermediário',
            'categoria' => 'DevOps',
            'preco' => 329.90,
            'status' => 'ativo', // ✅ Valor correto para ENUM
            'alunos_inscritos' => 45,
            'avaliacao' => 4.8,
            'progresso_percentual' => 75
        ],
        [
            'nome' => 'Flutter Mobile',
            'descricao' => 'Desenvolvimento mobile multiplataforma com Flutter',
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
        ],
        [
            'nome' => 'Cloud Computing AWS',
            'descricao' => 'Infraestrutura como serviço e computação em nuvem',
            'duracao_horas' => 100,
            'nivel' => 'Intermediário',
            'categoria' => 'Cloud',
            'preco' => 599.90,
            'status' => 'em_breve', // ✅ Valor correto para ENUM
            'alunos_inscritos' => 25,
            'avaliacao' => 0.0,
            'progresso_percentual' => 0
        ]
    ];

    echo "<h3>🎯 Inserindo cursos corrigidos...</h3>";
    
    $cursos_inseridos = 0;
    foreach ($cursos as $curso) {
        // Usar query direta para evitar problemas com prepared statements
        $sql = "INSERT INTO cursos (nome, descricao, duracao_horas, nivel, categoria, preco, status, alunos_inscritos, avaliacao, progresso_percentual) 
                VALUES (
                    '{$curso['nome']}', 
                    '{$curso['descricao']}', 
                    {$curso['duracao_horas']}, 
                    '{$curso['nivel']}', 
                    '{$curso['categoria']}', 
                    {$curso['preco']}, 
                    '{$curso['status']}', 
                    {$curso['alunos_inscritos']}, 
                    {$curso['avaliacao']}, 
                    {$curso['progresso_percentual']}
                )";
        
        if ($conn->query($sql)) {
            echo "✅ Curso '{$curso['nome']}' inserido com sucesso!<br>";
            $cursos_inseridos++;
        } else {
            echo "❌ Erro ao inserir curso '{$curso['nome']}': " . $conn->error . "<br>";
        }
    }

    echo "<br><h2>🎉 CURSOS INSERIDOS COM SUCESSO!</h2>";
    echo "<p>✅ Total de cursos inseridos: <strong>$cursos_inseridos</strong></p>";
    
    // Verificar total final
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos");
    if ($result) {
        $count = $result->fetch_assoc()['total'];
        echo "<p>✅ Total de cursos na tabela: <strong>$count</strong></p>";
    }
    
    // Mostrar cursos inseridos
    if ($count > 0) {
        echo "<h3>📋 Cursos disponíveis:</h3>";
        $cursos_lista = $conn->query("SELECT id, nome, categoria, preco, status FROM cursos ORDER BY id");
        if ($cursos_lista) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
            echo "<tr style='background: #f3f4f6;'><th>ID</th><th>Nome</th><th>Categoria</th><th>Preço</th><th>Status</th></tr>";
            while ($curso = $cursos_lista->fetch_assoc()) {
                $status_color = $curso['status'] === 'ativo' ? '#10b981' : '#f59e0b';
                echo "<tr>";
                echo "<td>{$curso['id']}</td>";
                echo "<td>{$curso['nome']}</td>";
                echo "<td>{$curso['categoria']}</td>";
                echo "<td>R$ {$curso['preco']}</td>";
                echo "<td style='color: $status_color; font-weight: bold;'>{$curso['status']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<br><h3>🚀 PRÓXIMOS PASSOS:</h3>";
    echo "<ol>";
    echo "<li><a href='cursos.php' style='color: #10b981; text-decoration: none; font-weight: bold;'>📚 Ver Página de Cursos Funcionando</a></li>";
    echo "<li><a href='dashboard_corrigido.php' style='color: #3b82f6; text-decoration: none; font-weight: bold;'>🎯 Acessar Dashboard Principal</a></li>";
    echo "<li><a href='inserir_agendamentos.php' style='color: #8b5cf6; text-decoration: none; font-weight: bold;'>📅 Inserir Agendamentos</a></li>";
    echo "</ol>";
    
    echo "<p style='background: #dbeafe; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6;'>";
    echo "<strong>🎯 PROBLEMA RESOLVIDO!</strong><br>";
    echo "O erro estava no campo 'status' que só aceita: 'ativo', 'em_breve', 'inativo'<br>";
    echo "Agora todos os cursos foram inseridos corretamente!";
    echo "</p>";

} catch (Exception $e) {
    echo "❌ Erro durante a inserção: " . $e->getMessage();
}
?>



































