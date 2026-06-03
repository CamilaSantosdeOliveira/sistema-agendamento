<?php
// Teste da API de usuários
echo "<h2>🧪 Teste da API de Usuários</h2>";

// Teste 1: Verificar se o arquivo existe
echo "<h3>1. Verificando arquivo da API:</h3>";
if (file_exists('api/usuarios.php')) {
    echo "✅ Arquivo api/usuarios.php existe<br>";
} else {
    echo "❌ Arquivo api/usuarios.php não existe<br>";
}

// Teste 2: Verificar conteúdo do arquivo
echo "<h3>2. Verificando conteúdo da API:</h3>";
$content = file_get_contents('api/usuarios.php');
if (strpos($content, 'excluir_usuario') !== false) {
    echo "✅ Ação 'excluir_usuario' encontrada na API<br>";
} else {
    echo "❌ Ação 'excluir_usuario' NÃO encontrada na API<br>";
}

if (strpos($content, 'desativar_usuario') !== false) {
    echo "✅ Ação 'desativar_usuario' encontrada na API<br>";
} else {
    echo "❌ Ação 'desativar_usuario' NÃO encontrada na API<br>";
}

if (strpos($content, 'editar_usuario') !== false) {
    echo "✅ Ação 'editar_usuario' encontrada na API<br>";
} else {
    echo "❌ Ação 'editar_usuario' NÃO encontrada na API<br>";
}

// Teste 3: Testar chamada direta da API
echo "<h3>3. Testando chamada da API:</h3>";
echo "<button onclick='testarAPI()'>🧪 Testar API</button>";
echo "<div id='resultado'></div>";

?>

<script>
async function testarAPI() {
    const resultado = document.getElementById('resultado');
    resultado.innerHTML = '🔄 Testando...';
    
    try {
        // Teste de busca de usuário
        const response = await fetch('api/usuarios.php?action=buscar_usuario&id=1');
        const data = await response.json();
        
        resultado.innerHTML = `
            <h4>📡 Resposta da API:</h4>
            <pre>${JSON.stringify(data, null, 2)}</pre>
        `;
        
    } catch (error) {
        resultado.innerHTML = `
            <h4>❌ Erro na API:</h4>
            <pre>${error.message}</pre>
        `;
    }
}
</script>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
}
h2, h3 {
    color: #333;
}
button {
    background: #007cba;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin: 10px 0;
}
button:hover {
    background: #005a87;
}
pre {
    background: #f8f8f8;
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 5px;
    overflow-x: auto;
}
</style>


















