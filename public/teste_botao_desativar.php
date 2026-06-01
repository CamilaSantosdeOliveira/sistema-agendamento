<?php
// Teste do Botão Desativar
echo "<h1>🧪 Teste do Botão Desativar</h1>";

// Conectar ao banco
include 'db.php';

// Verificar usuários ativos
$result = $conn->query("SELECT id, nome, email, ativo FROM usuarios WHERE ativo = 1 LIMIT 3");
echo "<h2>👥 Usuários Ativos (para teste):</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Status</th><th>Teste</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['nome'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . ($row['ativo'] ? 'Ativo' : 'Inativo') . "</td>";
    echo "<td><button onclick='testarDesativar(" . $row['id'] . ")'>⏸️ Testar Desativar</button></td>";
    echo "</tr>";
}
echo "</table>";

// Verificar usuários inativos
$result = $conn->query("SELECT id, nome, email, ativo FROM usuarios WHERE ativo = 0 LIMIT 3");
echo "<h2>⏸️ Usuários Inativos:</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Status</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['nome'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . ($row['ativo'] ? 'Ativo' : 'Inativo') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>📊 Estatísticas:</h2>";
$total = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$ativos = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE ativo = 1")->fetch_assoc()['total'];
$inativos = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE ativo = 0")->fetch_assoc()['total'];

echo "<p><strong>Total:</strong> $total usuários</p>";
echo "<p><strong>Ativos:</strong> $ativos usuários</p>";
echo "<p><strong>Inativos:</strong> $inativos usuários</p>";

echo "<h2>🔧 Teste Manual da API:</h2>";
echo "<button onclick='testarAPI()'>🧪 Testar API de Desativar</button>";
echo "<div id='resultado'></div>";
?>

<script>
async function testarDesativar(id) {
    if (confirm('Deseja testar desativar o usuário ID ' + id + '?')) {
        try {
            const response = await fetch('api/usuarios.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'desativar_usuario',
                    id: id
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('✅ Sucesso: ' + result.message);
                location.reload();
            } else {
                alert('❌ Erro: ' + result.message);
            }
        } catch (error) {
            alert('❌ Erro de conexão: ' + error.message);
        }
    }
}

async function testarAPI() {
    const resultado = document.getElementById('resultado');
    resultado.innerHTML = '<p>🔄 Testando API...</p>';
    
    try {
        const response = await fetch('api/usuarios.php?action=buscar_usuario&id=2');
        const result = await response.json();
        
        if (result.success) {
            resultado.innerHTML = '<p>✅ API funcionando! Usuário encontrado: ' + result.data.nome + '</p>';
        } else {
            resultado.innerHTML = '<p>❌ Erro na API: ' + result.message + '</p>';
        }
    } catch (error) {
        resultado.innerHTML = '<p>❌ Erro de conexão: ' + error.message + '</p>';
    }
}
</script>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
button { padding: 5px 10px; margin: 2px; cursor: pointer; }
</style>







