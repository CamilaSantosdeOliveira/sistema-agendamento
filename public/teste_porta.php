<?php
echo "<h2>🔍 Testando Portas do MySQL</h2>";

// Testar diferentes portas
$portas = [3306, 3307, 3308, 3309];

foreach ($portas as $porta) {
    echo "<h3>🔍 Testando porta $porta:</h3>";
    
    try {
        $conn = new mysqli('localhost', 'root', '', '', $porta);
        
        if ($conn->connect_error) {
            echo "❌ <strong>Falhou:</strong> " . $conn->connect_error . "<br>";
        } else {
            echo "✅ <strong>SUCESSO!</strong> MySQL está rodando na porta $porta!<br>";
            
            // Listar bancos
            $result = $conn->query("SHOW DATABASES");
            if ($result) {
                echo "<h4>📋 Bancos encontrados na porta $porta:</h4>";
                echo "<ul>";
                while ($row = $result->fetch_array()) {
                    $db_name = $row[0];
                    if (!in_array($db_name, ['information_schema', 'mysql', 'performance_schema', 'sys'])) {
                        echo "<li><strong>$db_name</strong></li>";
                    }
                }
                echo "</ul>";
            }
            
            $conn->close();
            break; // Parar no primeiro sucesso
        }
    } catch (Exception $e) {
        echo "❌ <strong>Erro:</strong> " . $e->getMessage() . "<br>";
    }
    
    echo "<br>";
}

echo "<h3>🎯 Próximo passo:</h3>";
echo "<p>Me informe qual porta funcionou para eu configurar o db.php corretamente!</p>";
?>


