<?php
session_start();

// Se já está logado, redirecionar
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: dashboard_final.php');
    exit();
}

$error_message = '';

// Processar login
if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Verificar credenciais
    if ($username === 'admin' && $password === 'admin123') {
        // Login válido
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['user_data'] = [
            'name' => 'Administrador',
            'role' => 'admin',
            'permissions' => ['all']
        ];
        
        // Redirecionar para dashboard
        header('Location: dashboard_final.php');
        exit();
    } else {
        $error_message = 'Usuário ou senha incorretos!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .logo {
            margin-bottom: 30px;
        }
        
        .logo h1 {
            font-size: 32px;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        
        .logo p {
            color: #64748b;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }
        
        .demo-accounts {
            background: #f8fafc;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .demo-accounts h3 {
            color: #374151;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .demo-account {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .demo-account:hover {
            border-color: #3b82f6;
            background: #f0f9ff;
        }
        
        .demo-account .role {
            font-weight: 600;
            color: #3b82f6;
            font-size: 12px;
        }
        
        .demo-account .credentials {
            color: #6b7280;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .error-message {
            background: #fef2f2;
            color: #dc2626;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: block;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .logo h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🎓 EduConnect Tech</h1>
            <p>Sistema de Agendamento</p>
        </div>
        
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" required placeholder="Digite seu usuário" value="admin">
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required placeholder="Digite sua senha" value="admin123">
            </div>
            
            <button type="submit" class="login-btn">🚀 Entrar no Sistema</button>
        </form>
        
        <div class="demo-accounts">
            <h3>📋 Conta de Demonstração</h3>
            
            <div class="demo-account" onclick="document.getElementById('username').value='admin'; document.getElementById('password').value='admin123';">
                <div class="role">👨‍💼 Administrador</div>
                <div class="credentials">admin / admin123</div>
            </div>
        </div>
    </div>
</body>
</html>







