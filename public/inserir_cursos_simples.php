<?php
echo "<h1>💻 INSERINDO CURSOS DE TECNOLOGIA</h1>";
include 'db.php';
if (!$conn) { echo "<p style='color: red;'>❌ Erro de conexão com banco</p>"; exit; }

echo "<h3>Inserindo 6 Cursos de Tecnologia...</h3>";

// Limpar tabela cursos
$conn->query("DELETE FROM cursos");
echo "<p style='color: green;'>✅ Tabela cursos limpa</p>";

// Inserir cursos com estrutura correta
$cursos = [
    ['nome' => 'Desenvolvimento Web Full Stack', 'descricao' => 'Aprenda HTML, CSS, JavaScript, PHP e MySQL para criar sites completos', 'preco' => 599.90, 'duracao' => '6 meses', 'vagas' => 20, 'status' => 'ativo'],
    ['nome' => 'Python para Data Science', 'descricao' => 'Domine Python, Pandas, NumPy e Machine Learning para análise de dados', 'preco' => 799.90, 'duracao' => '8 meses', 'vagas' => 15, 'status' => 'ativo'],
    ['nome' => 'Java Enterprise', 'descricao' => 'Desenvolva aplicações empresariais com Java, Spring Boot e JPA', 'preco' => 899.90, 'duracao' => '10 meses', 'vagas' => 12, 'status' => 'ativo'],
    ['nome' => 'React & Node.js', 'descricao' => 'Crie aplicações web modernas com React no frontend e Node.js no backend', 'preco' => 699.90, 'duracao' => '7 meses', 'vagas' => 18, 'status' => 'ativo'],
    ['nome' => 'Banco de Dados SQL & NoSQL', 'descricao' => 'Aprenda MySQL, PostgreSQL, MongoDB e Redis para gerenciar dados', 'preco' => 499.90, 'duracao' => '5 meses', 'vagas' => 25, 'status' => 'ativo'],
    ['nome' => 'DevOps & Cloud Computing', 'descricao' => 'Docker, Kubernetes, AWS e CI/CD para deploy automatizado', 'preco' => 999.90, 'duracao' => '12 meses', 'vagas' => 10, 'status' => 'ativo']
];

$inseridos = 0;
foreach ($cursos as $curso) {
    $sql = "INSERT INTO cursos (nome, descricao, preco, duracao, vagas, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsis", $curso['nome'], $curso['descricao'], $curso['preco'], $curso['duracao'], $curso['vagas'], $curso['status']);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Curso '{$curso['nome']}' inserido</p>";
        $inseridos++;
    } else {
        echo "<p style='color: red;'>❌ Erro ao inserir '{$curso['nome']}': " . $stmt->error . "</p>";
    }
    $stmt->close();
}

echo "<h3>🎉 CURSOS INSERIDOS COM SUCESSO!</h3>";
echo "<p style='color: green;'>✅ Total de cursos inseridos: $inseridos</p>";

// Verificar total na tabela
$result = $conn->query("SELECT COUNT(*) as total FROM cursos");
$total = $result->fetch_assoc()['total'];
echo "<p style='color: green;'>✅ Total de cursos na tabela: $total</p>";

echo "<h3>🚀 PRÓXIMOS PASSOS:</h3>";
echo "<p><a href='dashboard_final.php' style='background: #28a745; color: white; padding: 10px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>📊 Ver Dashboard Atualizado</a></p>";
echo "<p><a href='verificar_dados_completos.php' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>🔍 Verificar Dados</a></p>";

$conn->close();
?>









