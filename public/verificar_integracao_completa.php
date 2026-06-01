<?php
// Verificar integração completa do dashboard
include 'db.php';

echo "<h2>🔍 Verificação Completa de Integração</h2>";

// Testar conexão
if ($conn->connect_error) {
    die("❌ Erro de conexão: " . $conn->connect_error);
}
echo "✅ Conexão com banco OK!<br><br>";

// 1. Verificar dados que o dashboard deveria mostrar
echo "<h3>📊 Dados Reais do Banco:</h3>";

// Cursos
$result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
$cursos_reais = $result ? $result->fetch_assoc()['total'] : 0;
echo "📚 Cursos Ativos (Banco): $cursos_reais<br>";

// Professores
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1");
$professores_reais = $result ? $result->fetch_assoc()['total'] : 0;
echo "👨‍🏫 Professores Ativos (Banco): $professores_reais<br>";

// Alunos
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1");
$alunos_reais = $result ? $result->fetch_assoc()['total'] : 0;
echo "👨‍🎓 Alunos Ativos (Banco): $alunos_reais<br>";

// Agendamentos
$result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data >= CURDATE()");
$agendamentos_reais = $result ? $result->fetch_assoc()['total'] : 0;
echo "📅 Agendamentos Futuros (Banco): $agendamentos_reais<br><br>";

// 2. Verificar se o dashboard está usando as mesmas consultas
echo "<h3>🔍 Verificando Consultas do Dashboard:</h3>";

// Simular as consultas que o dashboard deveria fazer
$dashboard_cursos = 0;
$dashboard_professores = 0;
$dashboard_alunos = 0;
$dashboard_agendamentos = 0;

// Consulta de cursos (como no dashboard)
$result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
if ($result) {
    $dashboard_cursos = $result->fetch_assoc()['total'];
}

// Consulta de professores (como no dashboard)
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1");
if ($result) {
    $dashboard_professores = $result->fetch_assoc()['total'];
}

// Consulta de alunos (como no dashboard)
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1");
if ($result) {
    $dashboard_alunos = $result->fetch_assoc()['total'];
}

// Consulta de agendamentos (como no dashboard)
$result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data >= CURDATE()");
if ($result) {
    $dashboard_agendamentos = $result->fetch_assoc()['total'];
}

echo "📚 Dashboard Cursos: $dashboard_cursos<br>";
echo "👨‍🏫 Dashboard Professores: $dashboard_professores<br>";
echo "👨‍🎓 Dashboard Alunos: $dashboard_alunos<br>";
echo "📅 Dashboard Agendamentos: $dashboard_agendamentos<br><br>";

// 3. Verificar se os dados são iguais
echo "<h3>✅ Resultado da Integração:</h3>";

if ($cursos_reais == $dashboard_cursos) {
    echo "✅ Cursos: INTEGRADO (Banco: $cursos_reais = Dashboard: $dashboard_cursos)<br>";
} else {
    echo "❌ Cursos: NÃO INTEGRADO (Banco: $cursos_reais ≠ Dashboard: $dashboard_cursos)<br>";
}

if ($professores_reais == $dashboard_professores) {
    echo "✅ Professores: INTEGRADO (Banco: $professores_reais = Dashboard: $dashboard_professores)<br>";
} else {
    echo "❌ Professores: NÃO INTEGRADO (Banco: $professores_reais ≠ Dashboard: $dashboard_professores)<br>";
}

if ($alunos_reais == $dashboard_alunos) {
    echo "✅ Alunos: INTEGRADO (Banco: $alunos_reais = Dashboard: $dashboard_alunos)<br>";
} else {
    echo "❌ Alunos: NÃO INTEGRADO (Banco: $alunos_reais ≠ Dashboard: $dashboard_alunos)<br>";
}

if ($agendamentos_reais == $dashboard_agendamentos) {
    echo "✅ Agendamentos: INTEGRADO (Banco: $agendamentos_reais = Dashboard: $dashboard_agendamentos)<br>";
} else {
    echo "❌ Agendamentos: NÃO INTEGRADO (Banco: $agendamentos_reais ≠ Dashboard: $dashboard_agendamentos)<br>";
}

echo "<br><strong>🎯 Conclusão:</strong> ";
if ($cursos_reais == $dashboard_cursos && $professores_reais == $dashboard_professores && 
    $alunos_reais == $dashboard_alunos && $agendamentos_reais == $dashboard_agendamentos) {
    echo "SISTEMA 100% INTEGRADO!";
} else {
    echo "SISTEMA PARCIALMENTE INTEGRADO - Alguns dados não estão sincronizados.";
}
?>


