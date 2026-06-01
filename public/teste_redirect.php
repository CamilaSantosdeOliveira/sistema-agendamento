<?php
session_start();

echo "Testando redirecionamento...<br>";
echo "Sessão user_logged_in: " . (isset($_SESSION['user_logged_in']) ? $_SESSION['user_logged_in'] : 'NÃO DEFINIDA') . "<br>";

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    echo "Redirecionando para login...<br>";
    header('Location: login.html');
    exit();
} else {
    echo "Usuário está logado!<br>";
}
?>













