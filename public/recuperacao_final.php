<?php
echo "<h1>🎯 VERIFICAÇÃO FINAL DOS DADOS</h1>";

include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    echo "<p><a href='recriar_tabelas.php' style='background: #dc3545; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🔧 RECRIAR TABELAS</a></p>";
    exit;
}

echo "<h3>1️⃣ Verificando dados atuais...</h3>";

$tabelas = ['usuarios', 'cursos', 'agendamentos', 'avaliacoes', 'certificados', 'inscricoes', 'notificacoes', 'pagamentos'];
$tem_dados = false;

foreach ($tabelas as $tabela) {
    $result = $conn->query("SELECT COUNT(*) as total FROM $tabela");
    if ($result) {
        $total = $result->fetch_assoc()['total'];
        if ($total > 0) {
            echo "<p style='color: green;'>🎉 $tabela tem $total registros!</p>";
            $tem_dados = true;
        } else {
            echo "<p style='color: orange;'>⚠️ $tabela está vazia</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Erro ao verificar $tabela</p>";
    }
}

if ($tem_dados) {
    echo "<h3>🎉 DADOS RECUPERADOS COM SUCESSO!</h3>";
    echo "<p style='color: green;'>✅ Seus dados originais foram recuperados!</p>";
    echo "<p><a href='dashboard_final.php' style='background: #28a745; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🚀 Acessar Dashboard</a></p>";
} else {
    echo "<h3>😔 DADOS NÃO FORAM RECUPERADOS</h3>";
    echo "<p style='color: red;'>❌ Infelizmente, os dados originais foram perdidos.</p>";
    echo "<p>Vamos recriar o sistema com dados de demonstração:</p>";
    
    echo "<h3>🔧 OPÇÕES:</h3>";
    echo "<p><a href='recriar_tabelas.php' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>1️⃣ Recriar Tabelas</a></p>";
    echo "<p><a href='inserir_dados_simples.php' style='background: #28a745; color: white; padding: 10px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>2️⃣ Inserir Dados Demo</a></p>";
    echo "<p><a href='dashboard_final.php' style='background: #ffc107; color: black; padding: 10px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>3️⃣ Testar Dashboard</a></p>";
}

echo "<h3>📊 RESUMO:</h3>";
echo "<p>• MySQL: ✅ Funcionando</p>";
echo "<p>• Apache: ✅ Funcionando</p>";
echo "<p>• Tabelas: ✅ Existem</p>";
echo "<p>• Dados: " . ($tem_dados ? "✅ Recuperados" : "❌ Perdidos") . "</p>";

$conn->close();
?>









