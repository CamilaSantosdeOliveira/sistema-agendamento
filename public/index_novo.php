<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Redirecionar para login se não estiver logado
    header('Location: login.html');
    exit();
}

include 'db.php';

// Contar cursos ativos
$cursos_query = "SELECT COUNT(*) as count FROM cursos WHERE status = 'ativo'";
$cursos_result = $conn->query($cursos_query);
$cursos_count = $cursos_result ? $cursos_result->fetch_assoc()['count'] : 0;

// Contar professores ativos
$professores_query = "SELECT COUNT(*) as count FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1";
$professores_result = $conn->query($professores_query);
$professores_count = $professores_result ? $professores_result->fetch_assoc()['count'] : 0;

// Contar alunos cadastrados
$alunos_query = "SELECT COUNT(*) as count FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1";
$alunos_result = $conn->query($alunos_query);
$alunos_count = $alunos_result ? $alunos_result->fetch_assoc()['count'] : 0;

// Contar agendamentos futuros
$agendamentos_query = "SELECT COUNT(*) as count FROM agendamentos WHERE data >= CURDATE()";
$agendamentos_result = $conn->query($agendamentos_query);
$agendamentos_count = $agendamentos_result ? $agendamentos_result->fetch_assoc()['count'] : 0;

// Buscar cursos para exibição
$cursos_query = "SELECT * FROM cursos WHERE status = 'ativo' ORDER BY nome LIMIT 6";
$cursos_result = $conn->query($cursos_query);

// Buscar professores para exibição
$professores_query = "SELECT * FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1 ORDER BY nome LIMIT 10";
$professores_result = $conn->query($professores_query);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>EduConnect - Dashboard de Cursos de Tecnologia</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .welcome-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        
        .welcome-container h1 {
            color: #3b82f6;
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .welcome-container p {
            color: #64748b;
            margin-bottom: 30px;
        }
        
        .btn {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>🎓 EduConnect</h1>
        <p>Bem-vindo ao Sistema de Agendamento!</p>
        <p>Você está logado como: <strong><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Usuário'; ?></strong></p>
        
        <a href="dashboard_final.php" class="btn">🚀 Acessar Dashboard</a>
        <a href="logout.php" class="btn" style="background: linear-gradient(135deg, #ef4444, #dc2626);">🚪 Sair</a>
    </div>
</body>
</html>















