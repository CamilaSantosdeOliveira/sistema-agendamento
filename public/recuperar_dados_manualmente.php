<?php
// Script para inserir dados manualmente
include 'db.php';

echo "<h1>📝 Inserir Dados Manualmente</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { color: #10b981; font-weight: bold; }
    .error { color: #ef4444; font-weight: bold; }
    .info { color: #3b82f6; font-weight: bold; }
    .btn { background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
    .btn:hover { background: #2563eb; }
    .btn-success { background: #10b981; }
    .form-group { margin: 15px 0; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
    .form-group textarea { height: 80px; }
</style>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!$conn) {
            throw new Exception("❌ Banco de dados não está disponível");
        }
        
        $tipo = $_POST['tipo'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        
        if ($tipo === 'professor') {
            $formacao = $_POST['formacao'];
            $valor_hora = $_POST['valor_hora'];
            
            $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, formacao, valor_hora, ativo, criado_em) 
                    VALUES (?, ?, ?, 'professor', ?, ?, 1, NOW())";
            $stmt = $conn->prepare($sql);
            $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
            $stmt->bind_param("ssssd", $nome, $email, $senha_hash, $formacao, $valor_hora);
            
            if ($stmt->execute()) {
                echo "<div class='section'>";
                echo "<p class='success'>✅ Professor '$nome' inserido com sucesso!</p>";
                echo "</div>";
            } else {
                echo "<div class='section'>";
                echo "<p class='error'>❌ Erro ao inserir professor: " . $stmt->error . "</p>";
                echo "</div>";
            }
        } elseif ($tipo === 'aluno') {
            $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo, criado_em) 
                    VALUES (?, ?, ?, 'aluno', 1, NOW())";
            $stmt = $conn->prepare($sql);
            $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
            $stmt->bind_param("sss", $nome, $email, $senha_hash);
            
            if ($stmt->execute()) {
                echo "<div class='section'>";
                echo "<p class='success'>✅ Aluno '$nome' inserido com sucesso!</p>";
                echo "</div>";
            } else {
                echo "<div class='section'>";
                echo "<p class='error'>❌ Erro ao inserir aluno: " . $stmt->error . "</p>";
                echo "</div>";
            }
        } elseif ($tipo === 'curso') {
            $categoria = $_POST['categoria'];
            $nivel = $_POST['nivel'];
            $duracao_horas = $_POST['duracao_horas'];
            $preco = $_POST['preco'];
            $descricao = $_POST['descricao'];
            
            $sql = "INSERT INTO cursos (nome, categoria, nivel, duracao_horas, preco, descricao, status, alunos_inscritos, avaliacao) 
                    VALUES (?, ?, ?, ?, ?, ?, 'ativo', 0, 0.00)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssids", $nome, $categoria, $nivel, $duracao_horas, $preco, $descricao);
            
            if ($stmt->execute()) {
                echo "<div class='section'>";
                echo "<p class='success'>✅ Curso '$nome' inserido com sucesso!</p>";
                echo "</div>";
            } else {
                echo "<div class='section'>";
                echo "<p class='error'>❌ Erro ao inserir curso: " . $stmt->error . "</p>";
                echo "</div>";
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='section'>";
        echo "<p class='error'>❌ Erro: " . $e->getMessage() . "</p>";
        echo "</div>";
    }
}

// Mostrar dados atuais
echo "<div class='section'>";
echo "<h2>📊 Dados Atuais no Sistema</h2>";

$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'");
$total_professores = $result ? $result->fetch_assoc()['total'] : 0;

$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
$total_alunos = $result ? $result->fetch_assoc()['total'] : 0;

$result = $conn->query("SELECT COUNT(*) as total FROM cursos");
$total_cursos = $result ? $result->fetch_assoc()['total'] : 0;

echo "<p>👨‍🏫 Professores: <strong>$total_professores</strong></p>";
echo "<p>👨‍🎓 Alunos: <strong>$total_alunos</strong></p>";
echo "<p>📚 Cursos: <strong>$total_cursos</strong></p>";
echo "</div>";

// Formulário para inserir professor
echo "<div class='section'>";
echo "<h2>👨‍🏫 Adicionar Professor</h2>";
echo "<form method='POST'>";
echo "<input type='hidden' name='tipo' value='professor'>";
echo "<div class='form-group'>";
echo "<label>Nome:</label>";
echo "<input type='text' name='nome' required placeholder='Ex: Prof. João Silva'>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label>Email:</label>";
echo "<input type='email' name='email' required placeholder='joao.silva@email.com'>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label>Formação:</label>";
echo "<input type='text' name='formacao' required placeholder='Ex: Engenharia de Computação - USP'>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label>Valor por Hora (R$):</label>";
echo "<input type='number' name='valor_hora' step='0.01' required placeholder='80.00'>";
echo "</div>";
echo "<button type='submit' class='btn btn-success'>✅ Adicionar Professor</button>";
echo "</form>";
echo "</div>";

// Formulário para inserir aluno
echo "<div class='section'>";
echo "<h2>👨‍🎓 Adicionar Aluno</h2>";
echo "<form method='POST'>";
echo "<input type='hidden' name='tipo' value='aluno'>";
echo "<div class='form-group'>";
echo "<label>Nome:</label>";
echo "<input type='text' name='nome' required placeholder='Ex: Maria Santos'>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label>Email:</label>";
echo "<input type='email' name='email' required placeholder='maria.santos@email.com'>";
echo "</div>";
echo "<button type='submit' class='btn btn-success'>✅ Adicionar Aluno</button>";
echo "</form>";
echo "</div>";

// Formulário para inserir curso
echo "<div class='section'>";
echo "<h2>📚 Adicionar Curso</h2>";
echo "<form method='POST'>";
echo "<input type='hidden' name='tipo' value='curso'>";
echo "<div class='form-group'>";
echo "<label>Nome do Curso:</label>";
echo "<input type='text' name='nome' required placeholder='Ex: Desenvolvimento Web Full Stack'>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label>Categoria:</label>";
echo "<select name='categoria' required>";
echo "<option value=''>Selecione...</option>";
echo "<option value='Programação'>Programação</option>";
echo "<option value='Design'>Design</option>";
echo "<option value='Data Science'>Data Science</option>";
echo "<option value='Mobile'>Mobile</option>";
echo "<option value='DevOps'>DevOps</option>";
echo "<option value='Marketing'>Marketing</option>";
echo "</select>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label>Nível:</label>";
echo "<select name='nivel' required>";
echo "<option value=''>Selecione...</option>";
echo "<option value='Básico'>Básico</option>";
echo "<option value='Intermediário'>Intermediário</option>";
echo "<option value='Avançado'>Avançado</option>";
echo "</select>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label>Duração (horas):</label>";
echo "<input type='number' name='duracao_horas' required placeholder='80'>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label>Preço (R$):</label>";
echo "<input type='number' name='preco' step='0.01' required placeholder='299.90'>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label>Descrição:</label>";
echo "<textarea name='descricao' required placeholder='Descreva o curso...'></textarea>";
echo "</div>";
echo "<button type='submit' class='btn btn-success'>✅ Adicionar Curso</button>";
echo "</form>";
echo "</div>";

// Links para acesso
echo "<div class='section'>";
echo "<h2>🚀 Acessar o Sistema</h2>";
echo "<p>Depois de adicionar os dados, acesse:</p>";
echo "<a href='dashboard_final.php' class='btn btn-success'>📊 Acessar Dashboard</a>";
echo "<a href='carregar_dados_agora.php' class='btn'>🔄 Carregar Dados Padrão</a>";
echo "</div>";
?>










