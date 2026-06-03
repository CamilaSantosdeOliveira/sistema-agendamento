<?php
echo "<h1>📝 INSERINDO DADOS SIMPLES</h1>";

include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>1️⃣ Inserindo usuários...</h3>";

// Inserir usuário admin
$admin_sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo) VALUES 
('Administrador', 'admin@educonnect.com', 'admin123', 'admin', 1)";

if ($conn->query($admin_sql)) {
    echo "<p style='color: green;'>✅ Admin criado</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao criar admin: " . $conn->error . "</p>";
}

// Inserir professor
$prof_sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo) VALUES 
('João Silva', 'joao@educonnect.com', 'prof123', 'professor', 1)";

if ($conn->query($prof_sql)) {
    echo "<p style='color: green;'>✅ Professor criado</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao criar professor: " . $conn->error . "</p>";
}

// Inserir aluno
$aluno_sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo) VALUES 
('Maria Santos', 'maria@educonnect.com', 'aluno123', 'aluno', 1)";

if ($conn->query($aluno_sql)) {
    echo "<p style='color: green;'>✅ Aluno criado</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao criar aluno: " . $conn->error . "</p>";
}

echo "<h3>2️⃣ Inserindo cursos...</h3>";

// Inserir cursos
$cursos = [
    "INSERT INTO cursos (nome, descricao, preco, duracao, status) VALUES ('Programação Web', 'Curso completo de HTML, CSS e JavaScript', 299.90, '40 horas', 'ativo')",
    "INSERT INTO cursos (nome, descricao, preco, duracao, status) VALUES ('Design Gráfico', 'Aprenda Photoshop, Illustrator e InDesign', 199.90, '30 horas', 'ativo')",
    "INSERT INTO cursos (nome, descricao, preco, duracao, status) VALUES ('Marketing Digital', 'Estratégias de marketing para redes sociais', 159.90, '25 horas', 'ativo')",
    "INSERT INTO cursos (nome, descricao, preco, duracao, status) VALUES ('Python Básico', 'Introdução à programação com Python', 249.90, '35 horas', 'ativo')"
];

foreach ($cursos as $curso_sql) {
    if ($conn->query($curso_sql)) {
        echo "<p style='color: green;'>✅ Curso inserido</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao inserir curso: " . $conn->error . "</p>";
    }
}

echo "<h3>3️⃣ Verificando dados inseridos...</h3>";

// Verificar usuários
$usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios");
if ($usuarios) {
    $total = $usuarios->fetch_assoc()['total'];
    echo "<p style='color: green;'>✅ Total de usuários: $total</p>";
}

// Verificar cursos
$cursos_count = $conn->query("SELECT COUNT(*) as total FROM cursos");
if ($cursos_count) {
    $total = $cursos_count->fetch_assoc()['total'];
    echo "<p style='color: green;'>✅ Total de cursos: $total</p>";
}

echo "<h3>🎉 DADOS INSERIDOS COM SUCESSO!</h3>";
echo "<p><a href='dashboard_final.php' style='background: #28a745; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🚀 Acessar Dashboard</a></p>";

$conn->close();
?>











