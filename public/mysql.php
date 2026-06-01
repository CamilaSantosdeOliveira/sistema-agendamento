<?php
echo "<h1>Teste MySQL</h1>";

try {
    $conn = new mysqli('localhost', 'root', '', 'sistema_agendamento', 3306);
    
    if ($conn->connect_error) {
        echo "Erro: " . $conn->connect_error;
    } else {
        echo "Conectado!";
        
        $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<br>Professores: " . $row['total'];
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
















