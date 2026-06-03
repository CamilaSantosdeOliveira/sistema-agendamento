<?php
session_start();
include 'db.php';

echo "<h1>🔐 Verificar e Corrigir Dados de Login</h1>";

// Buscar todos os usuários
$query = "SELECT id, nome, email, tipo_usuario, ativo FROM usuarios ORDER BY tipo_usuario, nome";
$result = $conn->query($query);

echo "<h2>📋 Usuários Atuais no Sistema</h2>";

$admin = null;
$professor = null;
$alunos = [];

while ($usuario = $result->fetch_assoc()) {
    $status = $usuario['ativo'] ? "✅ Ativo" : "❌ Inativo";
    
    echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 15px; border-radius: 8px;'>";
    echo "<h3>👤 " . $usuario['nome'] . " (ID: " . $usuario['id'] . ")</h3>";
    echo "<p><strong>Email:</strong> " . $usuario['email'] . "</p>";
    echo "<p><strong>Tipo:</strong> " . $usuario['tipo_usuario'] . "</p>";
    echo "<p><strong>Status:</strong> $status</p>";
    echo "</div>";
    
    // Separar por tipo
    if ($usuario['tipo_usuario'] === 'admin') {
        $admin = $usuario;
    } elseif ($usuario['tipo_usuario'] === 'professor') {
        $professor = $usuario;
    } elseif ($usuario['tipo_usuario'] === 'aluno') {
        $alunos[] = $usuario;
    }
}

echo "<h2>🎯 Dados de Login Corretos</h2>";

echo "<div style='background: #f0f9ff; padding: 20px; border-radius: 8px; border: 2px solid #3b82f6;'>";
echo "<h3>👨‍💼 Administrador:</h3>";
if ($admin) {
    echo "<p><strong>Email:</strong> " . $admin['email'] . "</p>";
    echo "<p><strong>Senha:</strong> 123456</p>";
} else {
    echo "<p>❌ Nenhum administrador encontrado</p>";
}

echo "<h3>👨‍🏫 Professor:</h3>";
if ($professor) {
    echo "<p><strong>Email:</strong> " . $professor['email'] . "</p>";
    echo "<p><strong>Senha:</strong> 123456</p>";
} else {
    echo "<p>❌ Nenhum professor encontrado</p>";
}

echo "<h3>👩‍🎓 Aluno (Camila Santos):</h3>";
$camila = null;
foreach ($alunos as $aluno) {
    if (strpos(strtolower($aluno['nome']), 'camila') !== false) {
        $camila = $aluno;
        break;
    }
}

if ($camila) {
    echo "<p><strong>Email:</strong> " . $camila['email'] . "</p>";
    echo "<p><strong>Senha:</strong> 123456</p>";
} else {
    echo "<p>❌ Camila Santos não encontrada</p>";
    if (!empty($alunos)) {
        echo "<p>Alunos disponíveis:</p>";
        foreach ($alunos as $aluno) {
            echo "<p>- " . $aluno['nome'] . " (" . $aluno['email'] . ")</p>";
        }
    }
}
echo "</div>";

echo "<h3>🔗 Links:</h3>";
echo "<p><a href='login.php' target='_blank'>Tela de Login</a></p>";
echo "<p><a href='dashboard_final.php' target='_blank'>Dashboard Admin</a></p>";
echo "<p><a href='dashboard_professor.php' target='_blank'>Dashboard Professor</a></p>";
echo "<p><a href='dashboard_aluno.php' target='_blank'>Dashboard Aluno</a></p>";
?>








