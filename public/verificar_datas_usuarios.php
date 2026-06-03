<?php
echo "<h1>🔍 VERIFICANDO DATAS DOS USUÁRIOS NO BANCO</h1>";

include 'db.php';

// Verificar estrutura da tabela usuarios
echo "<h3>1️⃣ Estrutura da tabela usuarios:</h3>";
$result = $conn->query("DESCRIBE usuarios");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Verificar dados dos usuários com datas
echo "<h3>2️⃣ Dados dos usuários com datas:</h3>";
$query = "SELECT id, nome, email, tipo_usuario, ativo, data_cadastro FROM usuarios ORDER BY nome";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th><th>Ativo</th><th>Data Cadastro (Raw)</th><th>Data Formatada</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['nome'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['tipo_usuario'] . "</td>";
        echo "<td>" . ($row['ativo'] ? 'Sim' : 'Não') . "</td>";
        echo "<td>" . $row['data_cadastro'] . "</td>";
        
        // Tentar formatar a data
        if ($row['data_cadastro']) {
            $data = new DateTime($row['data_cadastro']);
            echo "<td>" . $data->format('d/m/Y H:i:s') . "</td>";
        } else {
            echo "<td style='color: red;'>NULL</td>";
        }
        
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Nenhum usuário encontrado</p>";
}

// Verificar se há problemas com timestamps
echo "<h3>3️⃣ Verificando timestamps:</h3>";
$query = "SELECT id, nome, data_cadastro, UNIX_TIMESTAMP(data_cadastro) as timestamp_unix FROM usuarios LIMIT 5";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Data Cadastro</th><th>Timestamp Unix</th><th>Data Convertida</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['nome'] . "</td>";
        echo "<td>" . $row['data_cadastro'] . "</td>";
        echo "<td>" . $row['timestamp_unix'] . "</td>";
        
        if ($row['timestamp_unix'] > 0) {
            $data = date('d/m/Y H:i:s', $row['timestamp_unix']);
            echo "<td>" . $data . "</td>";
        } else {
            echo "<td style='color: red;'>Timestamp inválido</td>";
        }
        
        echo "</tr>";
    }
    echo "</table>";
}

echo "<br><h3>🔗 Links para testar:</h3>";
echo "<p><a href='dashboard_final.php'>📊 Dashboard</a></p>";
echo "<p><a href='alunos.php'>👨‍🎓 Alunos</a></p>";
?>











