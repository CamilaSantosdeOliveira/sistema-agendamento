<?php
// Redirecionamento automático para index.html
if (file_exists('index.html')) {
    header('Location: index.html');
    exit;
} else {
    echo '<h1>EduConnect - Sistema de Agendamento</h1>';
    echo '<p>Arquivo index.html não encontrado.</p>';
    echo '<p>Verifique se o arquivo existe na pasta.</p>';
}
?>

