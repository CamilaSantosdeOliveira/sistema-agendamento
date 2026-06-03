<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Redirecionar para login se não estiver logado
    header('Location: login.html');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>EduConnect - Logado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .btn {
            background: #3b82f6;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            font-weight: bold;
        }
        .btn:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 EduConnect</h1>
        <p>Você está logado como: <strong><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Usuário'; ?></strong></p>
        <a href="dashboard_final.php" class="btn">🚀 Acessar Dashboard</a>
        <a href="logout.php" class="btn" style="background: #ef4444;">🚪 Sair</a>
    </div>
</body>
</html>















