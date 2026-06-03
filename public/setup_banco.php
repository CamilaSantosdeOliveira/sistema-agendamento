<?php
echo "<h2>🔧 Setup do Banco de Dados</h2>";

// Conectar ao banco
include 'db.php';
if (!$conn) {
    echo "❌ Erro na conexão com banco<br>";
    exit;
}

echo "✅ Conectado ao banco de dados<br>";

// Ler o arquivo SQL
$sql_file = 'criar_tabelas.sql';
if (!file_exists($sql_file)) {
    echo "❌ Arquivo $sql_file não encontrado<br>";
    exit;
}

$sql_content = file_get_contents($sql_file);
$queries = explode(';', $sql_content);

echo "<h3>Executando queries SQL:</h3>";

$success_count = 0;
$error_count = 0;

foreach ($queries as $query) {
    $query = trim($query);
    if (empty($query)) continue;
    
    try {
        if ($conn->query($query)) {
            echo "✅ Query executada com sucesso<br>";
            $success_count++;
        } else {
            echo "❌ Erro na query: " . $conn->error . "<br>";
            $error_count++;
        }
    } catch (Exception $e) {
        echo "❌ Exceção: " . $e->getMessage() . "<br>";
        $error_count++;
    }
}

echo "<h3>Resultado:</h3>";
echo "✅ Queries executadas com sucesso: $success_count<br>";
echo "❌ Erros: $error_count<br>";

// Verificar se as tabelas foram criadas
echo "<h3>Verificando tabelas criadas:</h3>";
$tables = ['usuarios', 'cursos', 'inscricoes', 'agendamentos'];

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Tabela '$table' existe<br>";
        
        // Contar registros
        $count_result = $conn->query("SELECT COUNT(*) as total FROM $table");
        $count = $count_result->fetch_assoc()['total'];
        echo "   📊 Registros: $count<br>";
    } else {
        echo "❌ Tabela '$table' NÃO existe<br>";
    }
}

echo "<br><h3>🎯 Próximos passos:</h3>";
echo "<p>1. <a href='verificar_banco.php'>Verificar estrutura do banco</a></p>";
echo "<p>2. <a href='cursos_completo.php'>Testar página de cursos</a></p>";
echo "<p>3. <a href='dashboard_corrigido.php'>Testar dashboard</a></p>";
?>


