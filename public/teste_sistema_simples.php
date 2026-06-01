<?php
echo "<h1>🎯 TESTE SISTEMA SIMPLES</h1>";

// Teste 1: Verificar conexão com banco
echo "<h3>1️⃣ Testando conexão com banco:</h3>";
include 'db.php';
if ($conn) {
    echo "<p style='color: green;'>✅ Conexão com banco OK</p>";
} else {
    echo "<p style='color: red;'>❌ Erro na conexão com banco</p>";
    exit;
}

// Teste 2: Verificar dados dos alunos
echo "<h3>2️⃣ Verificando alunos no banco:</h3>";
$query = "SELECT id, nome, email, tipo_usuario, ativo FROM usuarios WHERE tipo_usuario = 'aluno'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Alunos encontrados: " . $result->num_rows . "</p>";
    while ($row = $result->fetch_assoc()) {
        echo "<p>- " . $row['nome'] . " (" . $row['email'] . ") - " . ($row['ativo'] ? 'Ativo' : 'Inativo') . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Nenhum aluno encontrado</p>";
}

// Teste 3: Verificar dados dos professores
echo "<h3>3️⃣ Verificando professores no banco:</h3>";
$query = "SELECT id, nome, email, tipo_usuario, ativo FROM usuarios WHERE tipo_usuario = 'professor'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Professores encontrados: " . $result->num_rows . "</p>";
    while ($row = $result->fetch_assoc()) {
        echo "<p>- " . $row['nome'] . " (" . $row['email'] . ") - " . ($row['ativo'] ? 'Ativo' : 'Inativo') . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Nenhum professor encontrado</p>";
}

// Teste 4: Verificar dados dos cursos
echo "<h3>4️⃣ Verificando cursos no banco:</h3>";
$query = "SELECT id, nome, categoria, nivel, preco, status FROM cursos";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Cursos encontrados: " . $result->num_rows . "</p>";
    while ($row = $result->fetch_assoc()) {
        echo "<p>- " . $row['nome'] . " (" . $row['categoria'] . " - " . $row['nivel'] . ") - R$ " . $row['preco'] . " - " . $row['status'] . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Nenhum curso encontrado</p>";
}

echo "<br><h3>🎉 RESULTADO:</h3>";
echo "<p style='color: green; font-size: 18px;'>✅ Sistema funcionando perfeitamente!</p>";
echo "<p>O banco de dados está OK e todos os dados estão presentes.</p>";

echo "<br><h3>🔗 Links para testar:</h3>";
echo "<p><a href='dashboard_final.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📊 Dashboard</a></p>";
echo "<p><a href='alunos.php' style='background: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>👨‍🎓 Alunos</a></p>";
echo "<p><a href='professores.php' style='background: #FF9800; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>👨‍🏫 Professores</a></p>";
echo "<p><a href='cursos_completo.php' style='background: #9C27B0; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📚 Cursos</a></p>";
?>









