<?php
echo "<h1>🚀 CARREGANDO DADOS REAIS NO SISTEMA!</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;}</style>";

// Conectar ao banco
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento');

if ($conn->connect_error) {
    die("<div class='error'>❌ Erro na conexão: " . $conn->connect_error . "</div>");
}

echo "<div class='success'>✅ Conectado ao banco de dados!</div>";

// Garantir que os dados sejam salvos
$conn->autocommit(TRUE);

// 1. INSERIR PROFESSORES
echo "<h2>👨‍🏫 Inserindo Professores...</h2>";

$professores = [
    ['João Silva', 'joao.silva@email.com', 'Matemática'],
    ['Maria Santos', 'maria.santos@email.com', 'Português'],
    ['Pedro Costa', 'pedro.costa@email.com', 'História'],
    ['Ana Oliveira', 'ana.oliveira@email.com', 'Geografia'],
    ['Carlos Ferreira', 'carlos.ferreira@email.com', 'Física']
];

foreach ($professores as $prof) {
    $sql = "INSERT INTO usuarios (nome, email, tipo, senha) VALUES (?, ?, 'professor', ?)";
    $stmt = $conn->prepare($sql);
    $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
    $stmt->bind_param("sss", $prof[0], $prof[1], $senha_hash);
    
    if ($stmt->execute()) {
        echo "<div class='success'>✅ Professor {$prof[0]} - {$prof[2]} inserido!</div>";
    } else {
        echo "<div class='error'>❌ Erro ao inserir professor {$prof[0]}</div>";
    }
}

// 2. INSERIR ALUNOS
echo "<h2>👨‍🎓 Inserindo Alunos...</h2>";

$alunos = [
    ['Lucas Mendes', 'lucas.mendes@email.com'],
    ['Julia Lima', 'julia.lima@email.com'],
    ['Rafael Souza', 'rafael.souza@email.com'],
    ['Camila Rocha', 'camila.rocha@email.com'],
    ['Gabriel Alves', 'gabriel.alves@email.com'],
    ['Isabella Martins', 'isabella.martins@email.com'],
    ['Matheus Pereira', 'matheus.pereira@email.com'],
    ['Sofia Rodrigues', 'sofia.rodrigues@email.com']
];

foreach ($alunos as $aluno) {
    $sql = "INSERT INTO usuarios (nome, email, tipo, senha) VALUES (?, ?, 'aluno', ?)";
    $stmt = $conn->prepare($sql);
    $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
    $stmt->bind_param("sss", $aluno[0], $aluno[1], $senha_hash);
    
    if ($stmt->execute()) {
        echo "<div class='success'>✅ Aluno {$aluno[0]} inserido!</div>";
    } else {
        echo "<div class='error'>❌ Erro ao inserir aluno {$aluno[0]}</div>";
    }
}

// 3. INSERIR CURSOS
echo "<h2>📚 Inserindo Cursos...</h2>";

$cursos = [
    ['Matemática Básica', 'Fundamentos de matemática para iniciantes', 60, 150.00],
    ['Português Avançado', 'Gramática e redação avançada', 90, 200.00],
    ['História do Brasil', 'História completa do Brasil', 75, 180.00],
    ['Geografia Mundial', 'Geografia física e humana', 80, 170.00],
    ['Física Moderna', 'Conceitos modernos de física', 120, 250.00],
    ['Inglês Conversação', 'Inglês para conversação', 60, 160.00],
    ['Química Orgânica', 'Química orgânica básica', 100, 220.00],
    ['Biologia Celular', 'Biologia celular e molecular', 90, 200.00]
];

foreach ($cursos as $curso) {
    $sql = "INSERT INTO cursos (nome, descricao, duracao_minutos, preco) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssid", $curso[0], $curso[1], $curso[2], $curso[3]);
    
    if ($stmt->execute()) {
        echo "<div class='success'>✅ Curso {$curso[0]} - R$ {$curso[3]} inserido!</div>";
    } else {
        echo "<div class='error'>❌ Erro ao inserir curso {$curso[0]}</div>";
    }
}

// 4. VERIFICAR DADOS INSERIDOS
echo "<h2>📊 Verificando Dados Inseridos...</h2>";

$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'professor'");
$prof_count = $result->fetch_assoc()['total'];
echo "<div class='info'>👨‍🏫 Professores: {$prof_count}</div>";

$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'aluno'");
$aluno_count = $result->fetch_assoc()['total'];
echo "<div class='info'>👨‍🎓 Alunos: {$aluno_count}</div>";

$result = $conn->query("SELECT COUNT(*) as total FROM cursos");
$curso_count = $result->fetch_assoc()['total'];
echo "<div class='info'>📚 Cursos: {$curso_count}</div>";

$conn->close();

echo "<h2>🎉 SISTEMA CARREGADO COM SUCESSO!</h2>";
echo "<div class='success'>✅ Todos os dados foram inseridos no banco de dados!</div>";
echo "<p><a href='dashboard_final.php' style='background:green;color:white;padding:10px;text-decoration:none;border-radius:5px;'>🚀 ACESSAR DASHBOARD</a></p>";
echo "<p><strong>Dados inseridos:</strong></p>";
echo "<ul>";
echo "<li>👨‍🏫 <strong>5 Professores</strong> com especialidades diferentes</li>";
echo "<li>👨‍🎓 <strong>8 Alunos</strong> prontos para agendamentos</li>";
echo "<li>📚 <strong>8 Cursos</strong> com preços e durações</li>";
echo "</ul>";
echo "<p><strong>Senha padrão para todos:</strong> 123456</p>";
?>







