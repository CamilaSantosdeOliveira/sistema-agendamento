<?php
session_start();
include 'db.php';

echo "<h1>🎯 Criando Agendamentos de Teste</h1>";

// Verificar se está logado como professor
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
    echo "<p style='color: red;'>❌ Não está logado como professor</p>";
    exit();
}

$professor_id = $_SESSION['usuario_id'];

echo "<h2>📊 Dados do Professor</h2>";
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$professor = $stmt->get_result()->fetch_assoc();

if ($professor) {
    echo "<p><strong>Professor:</strong> {$professor['nome']} (ID: {$professor['id']})</p>";
} else {
    echo "<p style='color: red;'>❌ Professor não encontrado</p>";
    exit();
}

// Buscar cursos disponíveis
echo "<h2>🎓 Cursos Disponíveis</h2>";
$stmt = $conn->prepare("SELECT * FROM cursos LIMIT 5");
$stmt->execute();
$cursos = $stmt->get_result();

if ($cursos->num_rows == 0) {
    echo "<p style='color: red;'>❌ Nenhum curso encontrado no sistema</p>";
    exit();
}

echo "<p><strong>Total de cursos:</strong> {$cursos->num_rows}</p>";

// Buscar alunos disponíveis
echo "<h2>👥 Alunos Disponíveis</h2>";
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE tipo_usuario = 'aluno' LIMIT 3");
$stmt->execute();
$alunos = $stmt->get_result();

if ($alunos->num_rows == 0) {
    echo "<p style='color: red;'>❌ Nenhum aluno encontrado no sistema</p>";
    exit();
}

echo "<p><strong>Total de alunos:</strong> {$alunos->num_rows}</p>";

// Verificar agendamentos existentes
echo "<h2>📅 Agendamentos Existentes</h2>";
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE professor_id = ?");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$agendamentos_existentes = $stmt->get_result()->fetch_assoc()['total'];

echo "<p><strong>Agendamentos existentes:</strong> $agendamentos_existentes</p>";

if ($agendamentos_existentes > 0) {
    echo "<p style='color: orange;'>⚠️ Já existem agendamentos para este professor</p>";
    echo "<p>Você pode:</p>";
    echo "<ul>";
    echo "<li><a href='cursos_professor.php'>Ver cursos do professor</a></li>";
    echo "<li><a href='aulas_professor.php'>Ver aulas do professor</a></li>";
    echo "</ul>";
    exit();
}

// Criar agendamentos de teste
echo "<h2>➕ Criando Agendamentos de Teste</h2>";

$cursos_array = $cursos->fetch_all(MYSQLI_ASSOC);
$alunos_array = $alunos->fetch_all(MYSQLI_ASSOC);

$datas_teste = [
    date('Y-m-d', strtotime('+1 day')),
    date('Y-m-d', strtotime('+3 days')),
    date('Y-m-d', strtotime('+5 days')),
    date('Y-m-d', strtotime('+7 days')),
    date('Y-m-d', strtotime('+10 days'))
];

$horas_teste = ['09:00', '14:00', '16:00', '19:00', '20:00'];

$agendamentos_criados = 0;

for ($i = 0; $i < min(5, count($cursos_array)); $i++) {
    $curso = $cursos_array[$i];
    $aluno = $alunos_array[$i % count($alunos_array)];
    $data = $datas_teste[$i % count($datas_teste)];
    $hora = $horas_teste[$i % count($horas_teste)];
    
    // Inserir agendamento
    $stmt = $conn->prepare("INSERT INTO agendamentos (professor_id, curso_id, aluno_id, data_agendamento, hora_inicio, duracao_horas, status, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $duracao = rand(1, 3);
    $status = 'agendado';
    $observacoes = "Aula de teste - " . $curso['nome'];
    
    $stmt->bind_param("iiississ", $professor_id, $curso['id'], $aluno['id'], $data, $hora, $duracao, $status, $observacoes);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Agendamento criado: {$curso['nome']} - {$aluno['nome']} - $data às $hora</p>";
        $agendamentos_criados++;
    } else {
        echo "<p style='color: red;'>❌ Erro ao criar agendamento: {$curso['nome']}</p>";
    }
}

echo "<h2>🎉 Resultado</h2>";
echo "<p><strong>Agendamentos criados:</strong> $agendamentos_criados</p>";

if ($agendamentos_criados > 0) {
    echo "<p style='color: green;'>✅ Agora o professor pode ver seus cursos!</p>";
    echo "<p><strong>Próximos passos:</strong></p>";
    echo "<ul>";
    echo "<li><a href='cursos_professor.php' style='color: blue;'>📚 Ver Meus Cursos</a></li>";
    echo "<li><a href='aulas_professor.php' style='color: blue;'>📅 Ver Minhas Aulas</a></li>";
    echo "<li><a href='alunos_professor.php' style='color: blue;'>👥 Ver Meus Alunos</a></li>";
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ Nenhum agendamento foi criado</p>";
}
?>








