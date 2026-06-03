<?php
session_start();
include 'db.php';

echo "<h1>🧪 Teste de Inscrição</h1>";

// Verificar se o aluno está logado
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'aluno') {
    echo "<p style='color: red;'>❌ Aluno não está logado!</p>";
    echo "<a href='login.php'>Fazer Login</a>";
    exit();
}

$aluno_id = $_SESSION['user_id'];
$curso_id = 1; // Testar com o curso ID 1

echo "<h2>🔍 Verificando Dados</h2>";

// 1. Verificar se o aluno existe
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'aluno'");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$aluno = $stmt->get_result()->fetch_assoc();

if ($aluno) {
    echo "<p style='color: green;'>✅ Aluno encontrado: " . $aluno['nome'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ Aluno não encontrado!</p>";
    exit();
}

// 2. Verificar se o curso existe
$stmt = $conn->prepare("SELECT * FROM cursos WHERE id = ?");
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();

if ($curso) {
    echo "<p style='color: green;'>✅ Curso encontrado: " . $curso['nome'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ Curso não encontrado!</p>";
    exit();
}

// 3. Verificar se já está inscrito
$stmt = $conn->prepare("SELECT COUNT(*) as ja_inscrito FROM agendamentos WHERE curso_id = ? AND aluno_id = ?");
$stmt->bind_param("ii", $curso_id, $aluno_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['ja_inscrito'] > 0) {
    echo "<p style='color: orange;'>⚠️ Aluno já está inscrito neste curso!</p>";
} else {
    echo "<p style='color: green;'>✅ Aluno não está inscrito - pode prosseguir</p>";
}

// 4. Verificar professores disponíveis
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1");
$stmt->execute();
$professores = $stmt->get_result();

if ($professores->num_rows > 0) {
    echo "<p style='color: green;'>✅ Encontrados " . $professores->num_rows . " professores disponíveis</p>";
    while ($prof = $professores->fetch_assoc()) {
        echo "<p>👨‍🏫 Professor: " . $prof['nome'] . " (ID: " . $prof['id'] . ")</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Nenhum professor disponível!</p>";
    exit();
}

// 5. Verificar estrutura da tabela agendamentos
echo "<h2>📋 Estrutura da Tabela Agendamentos</h2>";
$result = $conn->query("DESCRIBE agendamentos");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Erro ao verificar estrutura da tabela!</p>";
}

// 6. Testar inserção
echo "<h2>🧪 Testando Inserção</h2>";

try {
    // Buscar primeiro professor
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1 LIMIT 1");
    $stmt->execute();
    $professor = $stmt->get_result()->fetch_assoc();
    
    if (!$professor) {
        throw new Exception("Nenhum professor disponível");
    }
    
    $professor_id = $professor['id'];
    $data_teste = date('Y-m-d', strtotime('+7 days'));
    $hora_inicio = '14:00:00';
    $duracao_horas = 2;
    $status = 'Agendado';
    $observacoes = 'Teste de inscrição';
    
    echo "<p>📅 Data de teste: " . $data_teste . "</p>";
    echo "<p>⏰ Hora: " . $hora_inicio . "</p>";
    echo "<p>👨‍🏫 Professor ID: " . $professor_id . "</p>";
    
         // Tentar inserir
     $stmt = $conn->prepare("INSERT INTO agendamentos (aluno_id, professor_id, curso_id, data_agendamento, hora_inicio, hora_fim, status, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
     $hora_fim = '16:00:00'; // 2 horas depois
     $status = 'agendado'; // Usar o valor correto do enum
     $stmt->bind_param("iiisssss", $aluno_id, $professor_id, $curso_id, $data_teste, $hora_inicio, $hora_fim, $status, $observacoes);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Inserção realizada com sucesso!</p>";
        echo "<p>🆔 ID do agendamento criado: " . $conn->insert_id . "</p>";
        
        // Verificar se foi criado
        $stmt = $conn->prepare("SELECT * FROM agendamentos WHERE id = ?");
        $stmt->bind_param("i", $conn->insert_id);
        $stmt->execute();
        $agendamento = $stmt->get_result()->fetch_assoc();
        
        if ($agendamento) {
            echo "<p style='color: green;'>✅ Agendamento confirmado no banco!</p>";
            echo "<p>📋 Dados: Aluno " . $agendamento['aluno_id'] . ", Curso " . $agendamento['curso_id'] . ", Data " . $agendamento['data_agendamento'] . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Erro na inserção: " . $stmt->error . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exceção: " . $e->getMessage() . "</p>";
}

echo "<h2>🔧 Ações</h2>";
echo "<p><a href='minhas_aulas_aluno.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📅 Ver Minhas Aulas</a></p>";
echo "<p><a href='meus_cursos_aluno.php' style='background: #8b5cf6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📚 Ver Meus Cursos</a></p>";
echo "<p><a href='buscar_cursos_aluno.php' style='background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔍 Buscar Cursos</a></p>";
?>


