<?php
// Executar configuração de certificados
echo "<h1>🚨 EXECUTANDO CONFIGURAÇÃO DE CERTIFICADOS</h1>";
echo "<p>Executando script de criação das tabelas...</p>";

// Incluir e executar o script de criação
ob_start();
include 'criar_tabela_certificados.php';
$output = ob_get_clean();

echo $output;

echo "<h2>✅ CONFIGURAÇÃO CONCLUÍDA!</h2>";
echo "<p><a href='certificados.php' target='_blank'>🎓 Clique aqui para ver os certificados com dados reais</a></p>";
echo "<p><a href='SOLUCAO-DADOS-REAIS-CERTIFICADOS.html' target='_blank'>📋 Ver página de solução</a></p>";

// Verificar se as tabelas foram criadas
include 'db.php';

$tables = ['certificados', 'cursos'];
echo "<h3>🔍 VERIFICAÇÃO DAS TABELAS:</h3>";

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<p>✅ Tabela '$table' existe</p>";
        
        // Contar registros
        $count_result = $conn->query("SELECT COUNT(*) as total FROM $table");
        if ($count_result) {
            $count = $count_result->fetch_assoc()['total'];
            echo "<p>📊 Registros em '$table': $count</p>";
        }
    } else {
        echo "<p>❌ Tabela '$table' NÃO existe</p>";
    }
}

$conn->close();
?>
















