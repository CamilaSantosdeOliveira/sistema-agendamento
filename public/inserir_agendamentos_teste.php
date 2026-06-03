<?php
echo "<h1>📅 INSERINDO AGENDAMENTOS DE TESTE</h1>";
include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>1️⃣ Limpando agendamentos antigos...</h3>";
$conn->query("DELETE FROM agendamentos");
echo "<p style='color: green;'>✅ Agendamentos antigos removidos</p>";

echo "<h3>2️⃣ Inserindo agendamentos de teste...</h3>";

// Buscar IDs de usuários e cursos existentes
$usuarios_result = $conn->query("SELECT id, nome, tipo_usuario FROM usuarios WHERE tipo_usuario = 'aluno' LIMIT 2");
$cursos_result = $conn->query("SELECT id, nome FROM cursos LIMIT 2");

$usuarios = [];
$cursos = [];

if ($usuarios_result && $usuarios_result->num_rows > 0) {
    while ($row = $usuarios_result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

if ($cursos_result && $cursos_result->num_rows > 0) {
    while ($row = $cursos_result->fetch_assoc()) {
        $cursos[] = $row;
    }
}

if (empty($usuarios)) {
    echo "<p style='color: red;'>❌ Nenhum aluno encontrado! Insira alunos primeiro.</p>";
    exit;
}

if (empty($cursos)) {
    echo "<p style='color: red;'>❌ Nenhum curso encontrado! Insira cursos primeiro.</p>";
    exit;
}

// Inserir agendamentos de teste
$agendamentos = [
    [
        'usuario_id' => $usuarios[0]['id'],
        'curso_id' => $cursos[0]['id'],
        'data' => date('Y-m-d', strtotime('+2 days')),
        'hora' => '14:00:00',
        'status' => 'agendado',
        'observacoes' => 'Primeira aula de teste'
    ],
    [
        'usuario_id' => $usuarios[0]['id'],
        'curso_id' => $cursos[0]['id'],
        'data' => date('Y-m-d', strtotime('+5 days')),
        'hora' => '16:00:00',
        'status' => 'agendado',
        'observacoes' => 'Segunda aula de teste'
    ]
];

if (count($usuarios) > 1 && count($cursos) > 1) {
    $agendamentos[] = [
        'usuario_id' => $usuarios[1]['id'],
        'curso_id' => $cursos[1]['id'],
        'data' => date('Y-m-d', strtotime('+3 days')),
        'hora' => '10:00:00',
        'status' => 'agendado',
        'observacoes' => 'Aula de outro aluno'
    ];
}

$sucessos = 0;
foreach ($agendamentos as $agendamento) {
    $sql = "INSERT INTO agendamentos (usuario_id, curso_id, data, hora, status, observacoes) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iissss', 
        $agendamento['usuario_id'],
        $agendamento['curso_id'],
        $agendamento['data'],
        $agendamento['hora'],
        $agendamento['status'],
        $agendamento['observacoes']
    );
    
    if ($stmt->execute()) {
        $sucessos++;
        echo "<p style='color: green;'>✅ Agendamento inserido: {$agendamento['data']} {$agendamento['hora']}</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao inserir agendamento: " . $stmt->error . "</p>";
    }
}

echo "<h3>🎉 RESULTADO:</h3>";
echo "<p style='color: green;'>✅ {$sucessos} agendamentos inseridos com sucesso!</p>";

echo "<br><h3>🔗 PRÓXIMOS PASSOS:</h3>";
echo "<p><a href='dashboard_final.php' style='color: blue;'>📊 Ver Dashboard</a></p>";
echo "<p><a href='verificar_dados_completos.php' style='color: blue;'>🔍 Verificar Dados</a></p>";
?>











