<?php
session_start();

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logout - EduConnect</title>
    <script>
        // Limpar dados do localStorage
        localStorage.removeItem('userData');
        localStorage.clear();
        
        // Redirecionar para login
        window.location.href = 'login.php';
    </script>
</head>
<body>
    <p>Fazendo logout...</p>
    <script>
        // Fallback caso o JavaScript não execute
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 100);
    </script>
</body>
</html>


