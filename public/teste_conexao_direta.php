<?php
echo "<h1>🔍 TESTE DE CONEXÃO DIRETA</h1>";

// Teste 1: Verificar se o MySQL está rodando
echo "<h3>1️⃣ Verificando se o MySQL está rodando:</h3>";
$connection = @mysqli_connect('localhost', 'root', '');
if ($connection) {
    echo "<p style='color: green;'>✅ MySQL está rodando</p>";
    mysqli_close($connection);
} else {
    echo "<p style='color: red;'>❌ MySQL não está rodando</p>";
    echo "<p>Erro: " . mysqli_connect_error() . "</p>";
}

// Teste 2: Verificar se o banco existe
echo "<h3>2️⃣ Verificando se o banco existe:</h3>";
$connection = @mysqli_connect('localhost', 'root', '');
if ($connection) {
    $result = mysqli_query($connection, "SHOW DATABASES LIKE 'sistema_agendamento'");
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color: green;'>✅ Banco 'sistema_agendamento' existe</p>";
    } else {
        echo "<p style='color: red;'>❌ Banco 'sistema_agendamento' não existe</p>";
    }
    mysqli_close($connection);
}

// Teste 3: Conectar diretamente ao banco
echo "<h3>3️⃣ Conectando ao banco:</h3>";
$connection = @mysqli_connect('localhost', 'root', '', 'sistema_agendamento');
if ($connection) {
    echo "<p style='color: green;'>✅ Conexão com banco estabelecida</p>";
    
    // Teste 4: Verificar tabelas
    echo "<h3>4️⃣ Verificando tabelas:</h3>";
    $result = mysqli_query($connection, "SHOW TABLES");
    if ($result) {
        echo "<p style='color: green;'>✅ Tabelas encontradas:</p>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<p>- " . $row[0] . "</p>";
        }
    }
    
    mysqli_close($connection);
} else {
    echo "<p style='color: red;'>❌ Erro ao conectar ao banco</p>";
    echo "<p>Erro: " . mysqli_connect_error() . "</p>";
}

echo "<br><h3>🔗 Links para testar:</h3>";
echo "<p><a href='alunos.php'>👨‍🎓 Ver Página de Alunos</a></p>";
echo "<p><a href='dashboard_final.php'>📊 Dashboard Principal</a></p>";
?>











