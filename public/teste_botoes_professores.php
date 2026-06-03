<?php
echo "<h2>🧪 Teste dos Botões - Professores</h2>";

echo "<h3>1. Verificando se a página carrega:</h3>";
echo "<p><a href='professores.php' target='_blank'>Abrir página de professores</a></p>";

echo "<h3>2. Testando API individual:</h3>";
echo "<p><a href='api/professores.php' target='_blank'>Testar API diretamente</a></p>";

echo "<h3>3. Verificando JavaScript:</h3>";
?>
<script>
console.log('🧪 Teste JavaScript iniciado');

// Testar se as funções existem
if (typeof adicionarProfessor === 'function') {
    console.log('✅ Função adicionarProfessor existe');
} else {
    console.log('❌ Função adicionarProfessor NÃO existe');
}

if (typeof editarProfessor === 'function') {
    console.log('✅ Função editarProfessor existe');
} else {
    console.log('❌ Função editarProfessor NÃO existe');
}

if (typeof verDetalhes === 'function') {
    console.log('✅ Função verDetalhes existe');
} else {
    console.log('❌ Função verDetalhes NÃO existe');
}

if (typeof toggleStatus === 'function') {
    console.log('✅ Função toggleStatus existe');
} else {
    console.log('❌ Função toggleStatus NÃO existe');
}

// Testar fetch
console.log('🧪 Testando fetch...');
fetch('api/professores.php')
    .then(response => response.json())
    .then(data => {
        console.log('✅ API funcionando:', data);
    })
    .catch(error => {
        console.log('❌ Erro na API:', error);
    });
</script>

<?php
echo "<h3>4. Instruções para testar:</h3>";
echo "<ol>";
echo "<li>Abra a página de professores</li>";
echo "<li>Abra o Console do navegador (F12)</li>";
echo "<li>Clique nos botões 'Editar', 'Ver' e 'Pausar'</li>";
echo "<li>Verifique se aparecem mensagens no console</li>";
echo "<li>Verifique se aparecem prompts/alertas</li>";
echo "</ol>";

echo "<h3>5. Links úteis:</h3>";
echo "<p><a href='professores.php'>Página de Professores</a></p>";
echo "<p><a href='dashboard_corrigido.php'>Dashboard Principal</a></p>";
echo "<p><a href='teste_professores_final.php'>Teste da API</a></p>";
?>


