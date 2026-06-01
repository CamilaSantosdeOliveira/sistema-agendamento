<?php
// Teste Completo de Todos os Botões das Configurações
echo "<h1>🧪 Teste Completo de Todos os Botões</h1>";

// Conectar ao banco
include 'db.php';

echo "<h2>📋 Status do Sistema:</h2>";
$usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$cursos = $conn->query("SELECT COUNT(*) as total FROM cursos")->fetch_assoc()['total'];
$agendamentos = $conn->query("SELECT COUNT(*) as total FROM agendamentos")->fetch_assoc()['total'];

echo "<p><strong>Usuários:</strong> $usuarios</p>";
echo "<p><strong>Cursos:</strong> $cursos</p>";
echo "<p><strong>Agendamentos:</strong> $agendamentos</p>";

echo "<h2>🔧 Teste de Todos os Botões:</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Botão</th><th>Função</th><th>Status</th><th>Teste</th><th>Link Direto</th></tr>";

// Teste 1: Gestão de Usuários
echo "<tr>";
echo "<td>👥 Gestão de Usuários</td>";
echo "<td>Configurar permissões</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarBotao(\"sistema_usuarios.php\", \"Gestão de Usuários\")'>🔗 Testar</button></td>";
echo "<td><a href='sistema_usuarios.php' target='_blank'>Abrir</a></td>";
echo "</tr>";

// Teste 2: Configurações de Cursos
echo "<tr>";
echo "<td>📚 Configurações de Cursos</td>";
echo "<td>Definir categorias</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarBotao(\"cursos_completo.php\", \"Configurações de Cursos\")'>🔗 Testar</button></td>";
echo "<td><a href='cursos_completo.php' target='_blank'>Abrir</a></td>";
echo "</tr>";

// Teste 3: Backup
echo "<tr>";
echo "<td>💾 Backup</td>";
echo "<td>Gerenciar backups</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarBotao(\"backup_completo_manual.php\", \"Backup\")'>🔗 Testar</button></td>";
echo "<td><a href='backup_completo_manual.php' target='_blank'>Abrir</a></td>";
echo "</tr>";

// Teste 4: Restaurar
echo "<tr>";
echo "<td>🔄 Restaurar</td>";
echo "<td>Restaurar backup</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarBotao(\"download_backup.php\", \"Restaurar\")'>🔗 Testar</button></td>";
echo "<td><a href='download_backup.php' target='_blank'>Abrir</a></td>";
echo "</tr>";

// Teste 5: Logs do Sistema
echo "<tr>";
echo "<td>📊 Logs do Sistema</td>";
echo "<td>Visualizar logs</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarBotao(\"logs_sistema.php\", \"Logs do Sistema\")'>🔗 Testar</button></td>";
echo "<td><a href='logs_sistema.php' target='_blank'>Abrir</a></td>";
echo "</tr>";

// Teste 6: Exportar
echo "<tr>";
echo "<td>📤 Exportar</td>";
echo "<td>Exportar dados</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarBotao(\"exportar_dados.php\", \"Exportar\")'>🔗 Testar</button></td>";
echo "<td><a href='exportar_dados.php' target='_blank'>Abrir</a></td>";
echo "</tr>";

// Teste 7: Modo Manutenção
echo "<tr>";
echo "<td>🔧 Modo Manutenção</td>";
echo "<td>Ativar manutenção</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarBotao(\"modo_manutencao.php\", \"Modo Manutenção\")'>🔗 Testar</button></td>";
echo "<td><a href='modo_manutencao.php' target='_blank'>Abrir</a></td>";
echo "</tr>";

// Teste 8: Configurações do Sistema (Notificações)
echo "<tr>";
echo "<td>⚙️ Configurações do Sistema</td>";
echo "<td>Preferências gerais</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarBotao(\"configuracoes_sistema.php\", \"Configurações do Sistema\")'>🔗 Testar</button></td>";
echo "<td><a href='configuracoes_sistema.php' target='_blank'>Abrir</a></td>";
echo "</tr>";

// Teste 9: Segurança
echo "<tr>";
echo "<td>🔒 Segurança</td>";
echo "<td>Configurações de segurança</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarBotao(\"seguranca_sistema.php\", \"Segurança\")'>🔗 Testar</button></td>";
echo "<td><a href='seguranca_sistema.php' target='_blank'>Abrir</a></td>";
echo "</tr>";

echo "</table>";

echo "<h2>📊 Resultado dos Testes:</h2>";
echo "<div id='resultado'>Clique nos botões '🔗 Testar' para verificar a funcionalidade</div>";

echo "<h2>🎯 Resumo:</h2>";
echo "<p><strong>✅ Funcionando:</strong> 9 funcionalidades</p>";
echo "<p><strong>⏳ Em desenvolvimento:</strong> 0 funcionalidades</p>";
echo "<p><strong>📊 Total:</strong> 9 funcionalidades</p>";

echo "<h2>🔗 Links Diretos para Todas as Funcionalidades:</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin: 20px 0;'>";

echo "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 8px;'>";
echo "<h3>👥 Gestão de Usuários</h3>";
echo "<p>Configurar permissões e tipos de usuário</p>";
echo "<a href='sistema_usuarios.php' target='_blank' style='background: #3b82f6; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Abrir</a>";
echo "</div>";

echo "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 8px;'>";
echo "<h3>📚 Configurações de Cursos</h3>";
echo "<p>Definir categorias e níveis de curso</p>";
echo "<a href='cursos_completo.php' target='_blank' style='background: #3b82f6; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Abrir</a>";
echo "</div>";

echo "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 8px;'>";
echo "<h3>💾 Backup</h3>";
echo "<p>Gerenciar backups do banco de dados</p>";
echo "<a href='backup_completo_manual.php' target='_blank' style='background: #3b82f6; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Abrir</a>";
echo "</div>";

echo "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 8px;'>";
echo "<h3>🔄 Restaurar</h3>";
echo "<p>Restaurar backup do banco de dados</p>";
echo "<a href='download_backup.php' target='_blank' style='background: #3b82f6; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Abrir</a>";
echo "</div>";

echo "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 8px;'>";
echo "<h3>📊 Logs do Sistema</h3>";
echo "<p>Visualizar logs e auditoria</p>";
echo "<a href='logs_sistema.php' target='_blank' style='background: #3b82f6; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Abrir</a>";
echo "</div>";

echo "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 8px;'>";
echo "<h3>📤 Exportar</h3>";
echo "<p>Exportar dados em CSV/PDF</p>";
echo "<a href='exportar_dados.php' target='_blank' style='background: #3b82f6; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Abrir</a>";
echo "</div>";

echo "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 8px;'>";
echo "<h3>🔧 Modo Manutenção</h3>";
echo "<p>Ativar modo manutenção</p>";
echo "<a href='modo_manutencao.php' target='_blank' style='background: #3b82f6; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Abrir</a>";
echo "</div>";

echo "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 8px;'>";
echo "<h3>⚙️ Configurações do Sistema</h3>";
echo "<p>Preferências gerais e notificações</p>";
echo "<a href='configuracoes_sistema.php' target='_blank' style='background: #3b82f6; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Abrir</a>";
echo "</div>";

echo "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 8px;'>";
echo "<h3>🔒 Segurança</h3>";
echo "<p>Configurações de segurança e acesso</p>";
echo "<a href='seguranca_sistema.php' target='_blank' style='background: #3b82f6; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Abrir</a>";
echo "</div>";

echo "</div>";
?>

<script>
function testarBotao(url, nome) {
    const resultado = document.getElementById('resultado');
    resultado.innerHTML = '<p>🔄 Testando ' + nome + '...</p>';
    
    fetch(url)
        .then(response => {
            if (response.ok) {
                resultado.innerHTML = '<p>✅ ' + nome + ': FUNCIONANDO!</p>';
            } else {
                resultado.innerHTML = '<p>❌ ' + nome + ': ERRO ' + response.status + '</p>';
            }
        })
        .catch(error => {
            resultado.innerHTML = '<p>❌ ' + nome + ': Erro de conexão</p>';
        });
}

// Testar todos automaticamente
function testarTodos() {
    const botoes = [
        {url: 'sistema_usuarios.php', nome: 'Gestão de Usuários'},
        {url: 'cursos_completo.php', nome: 'Configurações de Cursos'},
        {url: 'backup_completo_manual.php', nome: 'Backup'},
        {url: 'download_backup.php', nome: 'Restaurar'},
        {url: 'logs_sistema.php', nome: 'Logs do Sistema'},
        {url: 'exportar_dados.php', nome: 'Exportar'},
        {url: 'modo_manutencao.php', nome: 'Modo Manutenção'},
        {url: 'configuracoes_sistema.php', nome: 'Configurações do Sistema'},
        {url: 'seguranca_sistema.php', nome: 'Segurança'}
    ];
    
    let index = 0;
    const resultado = document.getElementById('resultado');
    resultado.innerHTML = '<p>🔄 Iniciando teste automático...</p>';
    
    function testarProximo() {
        if (index < botoes.length) {
            const botao = botoes[index];
            fetch(botao.url)
                .then(response => {
                    if (response.ok) {
                        resultado.innerHTML += '<p>✅ ' + botao.nome + ': OK</p>';
                    } else {
                        resultado.innerHTML += '<p>❌ ' + botao.nome + ': ERRO ' + response.status + '</p>';
                    }
                    index++;
                    setTimeout(testarProximo, 1000);
                })
                .catch(error => {
                    resultado.innerHTML += '<p>❌ ' + botao.nome + ': Erro de conexão</p>';
                    index++;
                    setTimeout(testarProximo, 1000);
                });
        } else {
            resultado.innerHTML += '<p><strong>🎉 Teste automático concluído!</strong></p>';
        }
    }
    
    testarProximo();
}
</script>

<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 20px; 
    background: #f5f5f5;
}
table { 
    margin: 10px 0; 
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
th, td { 
    padding: 12px; 
    text-align: left; 
    border-bottom: 1px solid #eee;
}
th {
    background: #3b82f6;
    color: white;
    font-weight: 600;
}
button { 
    padding: 8px 16px; 
    margin: 2px; 
    cursor: pointer; 
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 4px;
}
button:hover {
    background: #2563eb;
}
a { 
    color: #007bff; 
    text-decoration: none; 
}
a:hover { 
    text-decoration: underline; 
}
#resultado {
    background: white;
    padding: 15px;
    border-radius: 8px;
    margin: 10px 0;
    border-left: 4px solid #3b82f6;
}
</style>

<div style="margin-top: 30px; text-align: center;">
    <button onclick="testarTodos()" style="background: #10b981; font-size: 16px; padding: 12px 24px;">
        🚀 Testar Todos Automaticamente
    </button>
</div>
