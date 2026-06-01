<?php
echo "<h2>🔍 Verificando Dados nos Bancos</h2>";

$conn = new mysqli('localhost', 'root', '', 3306);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$bancos = ['educonnect', 'educonnectdb', 'sistema_agendamento'];

foreach ($bancos as $banco) {
    echo "<h3>📊 Banco: <strong>$banco</strong></h3>";
    
    $conn->select_db($banco);
    $tabelas = $conn->query("SHOW TABLES");
    
    if ($tabelas) {
        echo "<ul>";
        while ($tabela = $tabelas->fetch_array()) {
            $nome_tabela = $tabela[0];
            echo "<li><strong>$nome_tabela</strong>";
            
            // Contar registros
            $count = $conn->query("SELECT COUNT(*) as total FROM $nome_tabela");
            if ($count) {
                $total = $count->fetch_assoc()['total'];
                echo " - <span style='color: blue;'>$total registros</span>";
            }
            
            // Mostrar estrutura
            $estrutura = $conn->query("DESCRIBE $nome_tabela");
            if ($estrutura) {
                echo "<br>&nbsp;&nbsp;📋 Campos: ";
                $campos = [];
                while ($campo = $estrutura->fetch_assoc()) {
                    $campos[] = $campo['Field'] . " (" . $campo['Type'] . ")";
                }
                echo implode(', ', $campos);
            }
            
            echo "</li>";
        }
        echo "</ul>";
    }
    
    echo "<hr>";
}

echo "<h3>🎯 Recomendação:</h3>";
echo "<p>Baseado nos dados encontrados, recomendo usar o banco que tem mais tabelas e dados.</p>";
echo "<p>Me diga qual banco você quer usar e vou configurar o sistema!</p>";

$conn->close();
?>

