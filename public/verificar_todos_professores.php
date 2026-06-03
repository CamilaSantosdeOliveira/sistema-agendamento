<?php
session_start();
include 'db.php';

echo "<h1>👨‍🏫 Verificar TODOS os Professores</h1>";

// Buscar TODOS os professores (ativos e inativos)
$query = "SELECT id, nome, email, ativo, tipo_usuario FROM usuarios WHERE tipo_usuario = 'professor' ORDER BY nome";
$result = $conn->query($query);

echo "<h2>📋 Lista Completa de Professores</h2>";

$total_professores = 0;
while ($professor = $result->fetch_assoc()) {
    $total_professores++;
    $status = $professor['ativo'] ? "✅ Ativo" : "❌ Inativo";
    
    echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 15px; border-radius: 8px;'>";
    echo "<h3>👨‍🏫 " . $professor['nome'] . " (ID: " . $professor['id'] . ")</h3>";
    echo "<p><strong>Email:</strong> " . $professor['email'] . "</p>";
    echo "<p><strong>Status:</strong> $status</p>";
    echo "<p><strong>Tipo:</strong> " . $professor['tipo_usuario'] . "</p>";
    
    // Verificar se tem aulas
    $query_aulas = "SELECT COUNT(*) as total FROM agendamentos WHERE professor_id = ?";
    $stmt = $conn->prepare($query_aulas);
    $stmt->bind_param("i", $professor['id']);
    $stmt->execute();
    $aulas = $stmt->get_result()->fetch_assoc()['total'];
    
    echo "<p><strong>📅 Aulas:</strong> $aulas</p>";
    echo "</div>";
}

echo "<h2>📊 Resumo</h2>";
echo "<p><strong>Total de Professores:</strong> $total_professores</p>";

// Verificar professores ativos
$query_ativos = "SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1";
$ativos = $conn->query($query_ativos)->fetch_assoc()['total'];

echo "<p><strong>Professores Ativos:</strong> $ativos</p>";

// Verificar professores inativos
$query_inativos = "SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 0";
$inativos = $conn->query($query_inativos)->fetch_assoc()['total'];

echo "<p><strong>Professores Inativos:</strong> $inativos</p>";

echo "<h3>🔗 Links:</h3>";
echo "<p><a href='painel_admin_professores.php' target='_blank'>Painel Administrativo</a></p>";
echo "<p><a href='dashboard_final.php' target='_blank'>Dashboard Admin</a></p>";
?>








