<?php
echo "<h1>💻 INSERINDO CURSOS FINAL</h1>";
include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>1️⃣ Limpando cursos antigos...</h3>";
$conn->query("DELETE FROM cursos");
echo "<p style='color: green;'>✅ Tabela cursos limpa</p>";

echo "<h3>2️⃣ Inserindo 6 Cursos de Tecnologia...</h3>";

$cursos = [
    [
        'nome' => 'Desenvolvimento Web Full Stack',
        'descricao' => 'Aprenda HTML, CSS, JavaScript, PHP e MySQL para criar sites completos',
        'preco' => 599.90,
        'duracao' => '6 meses',
        'status' => 'ativo'
    ],
    [
        'nome' => 'Python para Data Science',
        'descricao' => 'Domine Python, Pandas, NumPy e Machine Learning para análise de dados',
        'preco' => 799.90,
        'duracao' => '8 meses',
        'status' => 'ativo'
    ],
    [
        'nome' => 'Java Enterprise',
        'descricao' => 'Desenvolva aplicações empresariais com Java, Spring Boot e JPA',
        'preco' => 899.90,
        'duracao' => '10 meses',
        'status' => 'ativo'
    ],
    [
        'nome' => 'React & Node.js',
        'descricao' => 'Crie aplicações web modernas com React no frontend e Node.js no backend',
        'preco' => 699.90,
        'duracao' => '7 meses',
        'status' => 'ativo'
    ],
    [
        'nome' => 'Banco de Dados SQL & NoSQL',
        'descricao' => 'Aprenda MySQL, PostgreSQL, MongoDB e Redis para gerenciar dados',
        'preco' => 499.90,
        'duracao' => '5 meses',
        'status' => 'ativo'
    ],
    [
        'nome' => 'DevOps & Cloud Computing',
        'descricao' => 'Docker, Kubernetes, AWS e CI/CD para deploy automatizado',
        'preco' => 999.90,
        'duracao' => '12 meses',
        'status' => 'ativo'
    ]
];

$sucessos = 0;
foreach ($cursos as $curso) {
    $sql = "INSERT INTO cursos (nome, descricao, preco, duracao, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssdss', 
        $curso['nome'],
        $curso['descricao'],
        $curso['preco'],
        $curso['duracao'],
        $curso['status']
    );
    
    if ($stmt->execute()) {
        $sucessos++;
        echo "<p style='color: green;'>✅ Curso inserido: {$curso['nome']}</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao inserir curso: " . $stmt->error . "</p>";
    }
}

echo "<h3>🎉 RESULTADO:</h3>";
echo "<p style='color: green;'>✅ {$sucessos} cursos inseridos com sucesso!</p>";

echo "<br><h3>🔗 PRÓXIMOS PASSOS:</h3>";
echo "<p><a href='inserir_agendamentos_teste.php' style='color: blue;'>📅 Inserir Agendamentos</a></p>";
echo "<p><a href='dashboard_final.php' style='color: blue;'>📊 Ver Dashboard</a></p>";
echo "<p><a href='verificar_dados_completos.php' style='color: blue;'>🔍 Verificar Dados</a></p>";
?>











