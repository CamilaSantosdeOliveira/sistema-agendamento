<?php
include 'db.php';

echo "🔍 VERIFICANDO PREÇOS DOS CURSOS\n\n";

// Verificar cursos com preço 0
$result = $conn->query("SELECT nome, preco FROM cursos WHERE preco = 0 OR preco IS NULL");
if ($result && $result->num_rows > 0) {
    echo "❌ CURSOS COM PREÇO R$ 0,00:\n";
    while ($curso = $result->fetch_assoc()) {
        echo "- " . $curso['nome'] . "\n";
    }
} else {
    echo "✅ Nenhum curso com preço R$ 0,00 encontrado!\n";
}

echo "\n📋 TODOS OS CURSOS:\n";
$result = $conn->query("SELECT nome, preco FROM cursos ORDER BY nome");
if ($result) {
    while ($curso = $result->fetch_assoc()) {
        $preco = $curso['preco'] > 0 ? "R$ " . number_format($curso['preco'], 2, ',', '.') : "R$ 0,00";
        echo "- " . $curso['nome'] . " → " . $preco . "\n";
    }
}

echo "\n🎯 PRÓXIMO PASSO: Execute corrigir_precos_cursos.php para corrigir os preços!";
?>















