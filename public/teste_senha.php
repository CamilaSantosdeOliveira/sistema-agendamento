<?php
echo "<h2>🔐 Testando Senhas do MySQL</h2>";

// Testar diferentes senhas
$senhas = [
    '',           // Sem senha
    'Cami7890#',  // Senha atual
    'root',       // Senha comum
    'admin',      // Outra senha comum
    'password',   // Senha padrão
    '123456'      // Senha simples
];

foreach ($senhas as $senha) {
    $senha_display = $senha ? $senha : '(sem senha)';
    echo "<h3>Testando senha: $senha_display</h3>";
    
    try {
        $conn = new mysqli('localhost', 'root', $senha, 'sistema_agendamento', 3306);
        
        if ($conn->connect_error) {
            echo "❌ <strong>Falhou:</strong> " . $conn->connect_error . "<br>";
        } else {
            echo "✅ <strong>SUCESSO!</strong> Senha correta encontrada!<br>";
            
            // Testar se o banco existe
            $result = $conn->query("SHOW DATABASES LIKE 'sistema_agendamento'");
            if ($result && $result->num_rows > 0) {
                echo "📋 Banco 'sistema_agendamento' encontrado!<br>";
                
                // Mostrar tabelas
                $tabelas = $conn->query("SHOW TABLES");
                if ($tabelas) {
                    echo "📊 Tabelas no banco:<br>";
                    while ($row = $tabelas->fetch_array()) {
                        echo "&nbsp;&nbsp;• " . $row[0] . "<br>";
                    }
                }
            } else {
                echo "⚠️ Banco 'sistema_agendamento' não encontrado<br>";
                echo "📋 Bancos disponíveis:<br>";
                $bancos = $conn->query("SHOW DATABASES");
                while ($row = $bancos->fetch_array()) {
                    echo "&nbsp;&nbsp;• " . $row[0] . "<br>";
                }
            }
            
            $conn->close();
            break; // Parar no primeiro sucesso
        }
    } catch (Exception $e) {
        echo "❌ <strong>Erro:</strong> " . $e->getMessage() . "<br>";
    }
    
    echo "<br>";
}

echo "<h3>🔧 Como resolver:</h3>";
echo "<ol>";
echo "<li><strong>Se encontrou a senha:</strong> Atualize o arquivo db.php</li>";
echo "<li><strong>Se não encontrou:</strong> Redefina a senha do MySQL</li>";
echo "<li><strong>Para redefinir senha:</strong> Use o XAMPP Control Panel</li>";
echo "</ol>";

echo "<h3>📞 Ajuda:</h3>";
echo "<p>Se nenhuma senha funcionar, você pode:</p>";
echo "<ul>";
echo "<li>Redefinir a senha do MySQL no XAMPP</li>";
echo "<li>Usar o phpMyAdmin para redefinir</li>";
echo "<li>Reinstalar o XAMPP se necessário</li>";
echo "</ul>";
?>



