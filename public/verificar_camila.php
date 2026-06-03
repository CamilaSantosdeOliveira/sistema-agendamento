<?php
session_start();
include 'db.php';

echo "<h1>👩‍🎓 Verificar Dados da Camila Santos</h1>";

// Buscar dados da Camila Santos
$query = "SELECT id, nome, email, tipo_usuario, ativo FROM usuarios WHERE nome LIKE '%Camila%' OR email LIKE '%camila%'";
$result = $conn->query($query);

echo "<h2>📋 Dados da Camila Santos</h2>";

if ($result->num_rows > 0) {
    while ($usuario = $result->fetch_assoc()) {
        $status = $usuario['ativo'] ? "✅ Ativo" : "❌ Inativo";
        
        echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 15px; border-radius: 8px;'>";
        echo "<h3>👩‍🎓 " . $usuario['nome'] . " (ID: " . $usuario['id'] . ")</h3>";
        echo "<p><strong>Email:</strong> " . $usuario['email'] . "</p>";
        echo "<p><strong>Tipo:</strong> " . $usuario['tipo_usuario'] . "</p>";
        echo "<p><strong>Status:</strong> $status</p>";
        
        // Verificar se tem aulas
        $query_aulas = "SELECT COUNT(*) as total FROM agendamentos WHERE aluno_id = ?";
        $stmt = $conn->prepare($query_aulas);
        $stmt->bind_param("i", $usuario['id']);
        $stmt->execute();
        $aulas = $stmt->get_result()->fetch_assoc()['total'];
        
        echo "<p><strong>📅 Aulas:</strong> $aulas</p>";
        echo "</div>";
    }
} else {
    echo "<p>Nenhum usuário encontrado com 'Camila' no nome.</p>";
}

// Listar todos os alunos para comparação
echo "<h2>📋 Todos os Alunos</h2>";
$query_alunos = "SELECT id, nome, email, ativo FROM usuarios WHERE tipo_usuario = 'aluno' ORDER BY nome";
$result_alunos = $conn->query($query_alunos);

while ($aluno = $result_alunos->fetch_assoc()) {
    $status = $aluno['ativo'] ? "✅ Ativo" : "❌ Inativo";
    
    echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 15px; border-radius: 8px;'>";
    echo "<h3>👩‍🎓 " . $aluno['nome'] . " (ID: " . $aluno['id'] . ")</h3>";
    echo "<p><strong>Email:</strong> " . $aluno['email'] . "</p>";
    echo "<p><strong>Status:</strong> $status</p>";
    echo "</div>";
}

echo "<h3>🔗 Links:</h3>";
echo "<p><a href='login.php' target='_blank'>Tela de Login</a></p>";
echo "<p><a href='dashboard_aluno.php' target='_blank'>Dashboard Aluno</a></p>";
?>








