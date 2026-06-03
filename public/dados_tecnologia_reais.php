<?php
echo "<h1>💻 INSERINDO DADOS REAIS DE TECNOLOGIA</h1>";
include 'db.php';
if (!$conn) { echo "<p style='color: red;'>❌ Erro de conexão com banco</p>"; exit; }

echo "<h3>1️⃣ Limpando dados antigos...</h3>";
$conn->query("DELETE FROM usuarios WHERE tipo_usuario != 'admin'");
$conn->query("DELETE FROM cursos");
$conn->query("DELETE FROM agendamentos");
$conn->query("DELETE FROM avaliacoes");
$conn->query("DELETE FROM certificados");
$conn->query("DELETE FROM inscricoes");
$conn->query("DELETE FROM notificacoes");
$conn->query("DELETE FROM pagamentos");
echo "<p style='color: green;'>✅ Dados antigos removidos</p>";

echo "<h3>2️⃣ Inserindo 6 Professores Especialistas...</h3>";
$professores = [
    ['nome' => 'Carlos Mendes', 'email' => 'carlos.mendes@edutech.com', 'senha' => password_hash('123456', PASSWORD_DEFAULT), 'tipo_usuario' => 'professor', 'telefone' => '(11) 99999-1111', 'endereco' => 'Rua das Tecnologias, 123 - São Paulo'],
    ['nome' => 'Ana Costa', 'email' => 'ana.costa@edutech.com', 'senha' => password_hash('123456', PASSWORD_DEFAULT), 'tipo_usuario' => 'professor', 'telefone' => '(11) 99999-2222', 'endereco' => 'Av. da Inovação, 456 - São Paulo'],
    ['nome' => 'Roberto Almeida', 'email' => 'roberto.almeida@edutech.com', 'senha' => password_hash('123456', PASSWORD_DEFAULT), 'tipo_usuario' => 'professor', 'telefone' => '(11) 99999-3333', 'endereco' => 'Rua do Código, 789 - São Paulo'],
    ['nome' => 'Fernanda Lima', 'email' => 'fernanda.lima@edutech.com', 'senha' => password_hash('123456', PASSWORD_DEFAULT), 'tipo_usuario' => 'professor', 'telefone' => '(11) 99999-4444', 'endereco' => 'Av. dos Frameworks, 321 - São Paulo'],
    ['nome' => 'Marcos Oliveira', 'email' => 'marcos.oliveira@edutech.com', 'senha' => password_hash('123456', PASSWORD_DEFAULT), 'tipo_usuario' => 'professor', 'telefone' => '(11) 99999-5555', 'endereco' => 'Rua do Backend, 654 - São Paulo'],
    ['nome' => 'Juliana Santos', 'email' => 'juliana.santos@edutech.com', 'senha' => password_hash('123456', PASSWORD_DEFAULT), 'tipo_usuario' => 'professor', 'telefone' => '(11) 99999-6666', 'endereco' => 'Av. dos Bancos, 987 - São Paulo']
];

foreach ($professores as $prof) {
    $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, telefone, endereco) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $prof['nome'], $prof['email'], $prof['senha'], $prof['tipo_usuario'], $prof['telefone'], $prof['endereco']);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Professor {$prof['nome']} inserido</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao inserir {$prof['nome']}: " . $stmt->error . "</p>";
    }
}

echo "<h3>3️⃣ Inserindo 4 Alunos...</h3>";
$alunos = [
    ['nome' => 'Lucas Ferreira', 'email' => 'lucas.ferreira@email.com', 'senha' => password_hash('123456', PASSWORD_DEFAULT), 'tipo_usuario' => 'aluno', 'telefone' => '(11) 88888-1111', 'endereco' => 'Rua dos Estudantes, 111 - São Paulo'],
    ['nome' => 'Camila Rodrigues', 'email' => 'camila.rodrigues@email.com', 'senha' => password_hash('123456', PASSWORD_DEFAULT), 'tipo_usuario' => 'aluno', 'telefone' => '(11) 88888-2222', 'endereco' => 'Av. da Aprendizagem, 222 - São Paulo'],
    ['nome' => 'Pedro Silva', 'email' => 'pedro.silva@email.com', 'senha' => password_hash('123456', PASSWORD_DEFAULT), 'tipo_usuario' => 'aluno', 'telefone' => '(11) 88888-3333', 'endereco' => 'Rua do Conhecimento, 333 - São Paulo'],
    ['nome' => 'Beatriz Costa', 'email' => 'beatriz.costa@email.com', 'senha' => password_hash('123456', PASSWORD_DEFAULT), 'tipo_usuario' => 'aluno', 'telefone' => '(11) 88888-4444', 'endereco' => 'Av. do Futuro, 444 - São Paulo']
];

foreach ($alunos as $aluno) {
    $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, telefone, endereco) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $aluno['nome'], $aluno['email'], $aluno['senha'], $aluno['tipo_usuario'], $aluno['telefone'], $aluno['endereco']);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Aluno {$aluno['nome']} inserido</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao inserir {$aluno['nome']}: " . $stmt->error . "</p>";
    }
}

echo "<h3>4️⃣ Inserindo 6 Cursos de Tecnologia...</h3>";
$cursos = [
    ['nome' => 'Desenvolvimento Web Full Stack', 'descricao' => 'Aprenda HTML, CSS, JavaScript, PHP e MySQL para criar sites completos', 'preco' => 599.90, 'duracao' => '6 meses', 'vagas' => 20],
    ['nome' => 'Python para Data Science', 'descricao' => 'Domine Python, Pandas, NumPy e Machine Learning para análise de dados', 'preco' => 799.90, 'duracao' => '8 meses', 'vagas' => 15],
    ['nome' => 'Java Enterprise', 'descricao' => 'Desenvolva aplicações empresariais com Java, Spring Boot e JPA', 'preco' => 899.90, 'duracao' => '10 meses', 'vagas' => 12],
    ['nome' => 'React & Node.js', 'descricao' => 'Crie aplicações modernas com React no frontend e Node.js no backend', 'preco' => 699.90, 'duracao' => '7 meses', 'vagas' => 18],
    ['nome' => 'Banco de Dados SQL & NoSQL', 'descricao' => 'Aprenda MySQL, PostgreSQL, MongoDB e Redis para projetos escaláveis', 'preco' => 499.90, 'duracao' => '5 meses', 'vagas' => 25],
    ['nome' => 'DevOps & Cloud Computing', 'descricao' => 'Domine Docker, Kubernetes, AWS e CI/CD para deploy automatizado', 'preco' => 999.90, 'duracao' => '9 meses', 'vagas' => 10]
];

foreach ($cursos as $curso) {
    $sql = "INSERT INTO cursos (nome, descricao, preco, duracao, vagas) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $curso['nome'], $curso['descricao'], $curso['preco'], $curso['duracao'], $curso['vagas']);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Curso {$curso['nome']} inserido</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao inserir {$curso['nome']}: " . $stmt->error . "</p>";
    }
}

echo "<h3>5️⃣ Criando alguns agendamentos de exemplo...</h3>";
$agendamentos = [
    ['data' => '2025-09-15', 'hora' => '14:00:00', 'duracao' => 120, 'status' => 'confirmado'],
    ['data' => '2025-09-20', 'hora' => '16:00:00', 'duracao' => 90, 'status' => 'pendente'],
    ['data' => '2025-09-25', 'hora' => '10:00:00', 'duracao' => 120, 'status' => 'confirmado']
];

foreach ($agendamentos as $i => $agend) {
    $sql = "INSERT INTO agendamentos (data, hora, duracao, status, curso_id, professor_id, aluno_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $curso_id = $i + 1;
    $professor_id = $i + 1;
    $aluno_id = $i + 1;
    $stmt->bind_param("ssisiii", $agend['data'], $agend['hora'], $agend['duracao'], $agend['status'], $curso_id, $professor_id, $aluno_id);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Agendamento para {$agend['data']} às {$agend['hora']} criado</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao criar agendamento: " . $stmt->error . "</p>";
    }
}

echo "<h3>🎉 DADOS DE TECNOLOGIA INSERIDOS COM SUCESSO!</h3>";
echo "<h4>📊 RESUMO:</h4>";
echo "<p>• 👨‍🏫 <strong>6 Professores</strong> especialistas em tecnologia</p>";
echo "<p>• 👨‍🎓 <strong>4 Alunos</strong> com dados realistas</p>";
echo "<p>• 💻 <strong>6 Cursos</strong> de tecnologia modernos</p>";
echo "<p>• 📅 <strong>3 Agendamentos</strong> de exemplo</p>";

echo "<h4>🔑 DADOS DE LOGIN:</h4>";
echo "<p><strong>Admin:</strong> admin@edutech.com / 123456</p>";
echo "<p><strong>Professores:</strong> carlos.mendes@edutech.com / 123456</p>";
echo "<p><strong>Alunos:</strong> lucas.ferreira@email.com / 123456</p>";

echo "<p><a href='dashboard_final.php' style='background: #28a745; color: white; padding: 15px; text-decoration: none; border-radius: 5px; font-size: 18px; margin: 10px; display: inline-block;'>🚀 VER DASHBOARD ATUALIZADO</a></p>";
echo "<p><a href='verificar_dados_completos.php' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>📊 VERIFICAR DADOS</a></p>";

$conn->close();
?>











