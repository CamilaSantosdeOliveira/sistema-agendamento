<?php
session_start();
include 'db.php';

echo "<h1>🔐 Dados de Login Corretos - Sistema EduConnect</h1>";

echo "<div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 15px; margin: 20px 0;'>";
echo "<h2 style='text-align: center; margin-bottom: 30px;'>🎓 Contas para Teste</h2>";

echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;'>";

// Administrador
echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; backdrop-filter: blur(10px);'>";
echo "<h3 style='color: #ffd700; margin-bottom: 15px;'>👨‍💼 Administrador</h3>";
echo "<p><strong>Email:</strong> admin@educonnect.com</p>";
echo "<p><strong>Senha:</strong> 123456</p>";
echo "<p style='font-size: 0.9rem; opacity: 0.8;'>Acesso total ao sistema</p>";
echo "</div>";

// Professor
echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; backdrop-filter: blur(10px);'>";
echo "<h3 style='color: #ffd700; margin-bottom: 15px;'>👨‍🏫 Professor</h3>";
echo "<p><strong>Email:</strong> ricardo.silva@educonnect.com</p>";
echo "<p><strong>Senha:</strong> 123456</p>";
echo "<p style='font-size: 0.9rem; opacity: 0.8;'>Gerencia suas aulas e alunos</p>";
echo "</div>";

// Aluno
echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; backdrop-filter: blur(10px);'>";
echo "<h3 style='color: #ffd700; margin-bottom: 15px;'>👩‍🎓 Aluno</h3>";
echo "<p><strong>Email:</strong> camilacah7890@gmail.com</p>";
echo "<p><strong>Senha:</strong> 123456</p>";
echo "<p style='font-size: 0.9rem; opacity: 0.8;'>Acessa seus cursos e aulas</p>";
echo "</div>";

echo "</div>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>📋 Resumo dos Dados:</h3>";
echo "<ul style='list-style: none; padding: 0;'>";
echo "<li>✅ <strong>Admin:</strong> admin@educonnect.com / 123456</li>";
echo "<li>✅ <strong>Professor:</strong> ricardo.silva@educonnect.com / 123456</li>";
echo "<li>✅ <strong>Aluno:</strong> camilacah7890@gmail.com / 123456</li>";
echo "</ul>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='login.php' style='background: #3b82f6; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; margin: 10px;'>🚀 Fazer Login</a>";
echo "<a href='dashboard_final.php' style='background: #10b981; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; margin: 10px;'>👨‍💼 Dashboard Admin</a>";
echo "<a href='dashboard_professor.php' style='background: #f59e0b; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; margin: 10px;'>👨‍🏫 Dashboard Professor</a>";
echo "<a href='dashboard_aluno.php' style='background: #8b5cf6; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; margin: 10px;'>👩‍🎓 Dashboard Aluno</a>";
echo "</div>";

// Verificar se a Camila existe no banco
$query = "SELECT id, nome, email, tipo_usuario, ativo FROM usuarios WHERE email = 'camilacah7890@gmail.com'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $camila = $result->fetch_assoc();
    echo "<div style='background: #dcfce7; padding: 15px; border-radius: 8px; border: 1px solid #10b981; margin: 20px 0;'>";
    echo "<h4 style='color: #166534; margin: 0;'>✅ Camila Santos encontrada no sistema!</h4>";
    echo "<p style='margin: 5px 0;'><strong>Nome:</strong> " . $camila['nome'] . "</p>";
    echo "<p style='margin: 5px 0;'><strong>Email:</strong> " . $camila['email'] . "</p>";
    echo "<p style='margin: 5px 0;'><strong>Status:</strong> " . ($camila['ativo'] ? 'Ativo' : 'Inativo') . "</p>";
    echo "</div>";
} else {
    echo "<div style='background: #fef2f2; padding: 15px; border-radius: 8px; border: 1px solid #ef4444; margin: 20px 0;'>";
    echo "<h4 style='color: #dc2626; margin: 0;'>⚠️ Camila Santos não encontrada no banco de dados</h4>";
    echo "<p>Verifique se o email está correto ou se a conta foi criada.</p>";
    echo "</div>";
}
?>






