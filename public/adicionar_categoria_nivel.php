<?php
echo "<h1>🔧 ADICIONANDO CATEGORIA E NÍVEL À TABELA CURSOS</h1>";
include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>1️⃣ Verificando estrutura atual...</h3>";
$result = $conn->query("DESCRIBE cursos");
if ($result) {
    echo "<p>📋 Colunas atuais da tabela cursos:</p>";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']}: {$row['Type']}<br>";
    }
}

echo "<h3>2️⃣ Adicionando coluna 'categoria'...</h3>";
$sql = "ALTER TABLE cursos ADD COLUMN categoria VARCHAR(50) DEFAULT 'Tecnologia' AFTER descricao";
if ($conn->query($sql)) {
    echo "<p style='color: green;'>✅ Coluna 'categoria' adicionada com sucesso!</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao adicionar categoria: " . $conn->error . "</p>";
}

echo "<h3>3️⃣ Adicionando coluna 'nivel'...</h3>";
$sql = "ALTER TABLE cursos ADD COLUMN nivel VARCHAR(30) DEFAULT 'Intermediário' AFTER categoria";
if ($conn->query($sql)) {
    echo "<p style='color: green;'>✅ Coluna 'nivel' adicionada com sucesso!</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao adicionar nivel: " . $conn->error . "</p>";
}

echo "<h3>4️⃣ Atualizando dados dos cursos existentes...</h3>";

$categorias = [
    'Desenvolvimento Web Full Stack' => 'Programação Web',
    'Python para Data Science' => 'Data Science',
    'Java Enterprise' => 'Desenvolvimento Backend',
    'React & Node.js' => 'Programação Web',
    'Banco de Dados SQL & NoSQL' => 'Data Science',
    'DevOps & Cloud Computing' => 'DevOps'
];

$niveis = [
    'Desenvolvimento Web Full Stack' => 'Iniciante ao Avançado',
    'Python para Data Science' => 'Intermediário ao Avançado',
    'Java Enterprise' => 'Avançado',
    'React & Node.js' => 'Intermediário ao Avançado',
    'Banco de Dados SQL & NoSQL' => 'Intermediário',
    'DevOps & Cloud Computing' => 'Avançado'
];

$sucessos = 0;
foreach ($categorias as $nome_curso => $categoria) {
    $sql = "UPDATE cursos SET categoria = ?, nivel = ? WHERE nome = ?";
    $stmt = $conn->prepare($sql);
    $nivel = $niveis[$nome_curso];
    $stmt->bind_param('sss', $categoria, $nivel, $nome_curso);
    
    if ($stmt->execute()) {
        $sucessos++;
        echo "<p style='color: green;'>✅ {$nome_curso}: Categoria = {$categoria}, Nível = {$nivel}</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao atualizar {$nome_curso}: " . $stmt->error . "</p>";
    }
}

echo "<h3>🎉 RESULTADO:</h3>";
echo "<p style='color: green;'>✅ {$sucessos} cursos atualizados com sucesso!</p>";

echo "<h3>5️⃣ Verificando estrutura final...</h3>";
$result = $conn->query("DESCRIBE cursos");
if ($result) {
    echo "<p>📋 Colunas finais da tabela cursos:</p>";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']}: {$row['Type']}<br>";
    }
}

echo "<br><h3>🔗 PRÓXIMOS PASSOS:</h3>";
echo "<p><a href='cursos.php' style='color: blue;'>📚 Ver Página de Cursos</a></p>";
echo "<p><a href='dashboard_final.php' style='color: blue;'>📊 Ver Dashboard</a></p>";
?>









