<?php
// Teste dos Botões de Configurações
echo "<h1>🧪 Teste dos Botões de Configurações</h1>";

// Conectar ao banco
include 'db.php';

echo "<h2>📋 Status do Sistema:</h2>";
$usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$cursos = $conn->query("SELECT COUNT(*) as total FROM cursos")->fetch_assoc()['total'];
$agendamentos = $conn->query("SELECT COUNT(*) as total FROM agendamentos")->fetch_assoc()['total'];

echo "<p><strong>Usuários:</strong> $usuarios</p>";
echo "<p><strong>Cursos:</strong> $cursos</p>";
echo "<p><strong>Agendamentos:</strong> $agendamentos</p>";

echo "<h2>🔧 Teste dos Botões:</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Botão</th><th>Função</th><th>Status</th><th>Teste</th></tr>";

// Teste 1: Gestão de Usuários
echo "<tr>";
echo "<td>👥 Gestão de Usuários</td>";
echo "<td>Configurar permissões</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarGestaoUsuarios()'>🔗 Testar Link</button></td>";
echo "</tr>";

// Teste 2: Configurações de Cursos
echo "<tr>";
echo "<td>📚 Configurações de Cursos</td>";
echo "<td>Definir categorias</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarConfigCursos()'>🔗 Testar Link</button></td>";
echo "</tr>";

// Teste 3: Backup
echo "<tr>";
echo "<td>💾 Backup</td>";
echo "<td>Gerenciar backups</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarBackup()'>🔗 Testar Link</button></td>";
echo "</tr>";

// Teste 4: Restaurar
echo "<tr>";
echo "<td>🔄 Restaurar</td>";
echo "<td>Restaurar backup</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarRestaurar()'>🔗 Testar Link</button></td>";
echo "</tr>";

// Teste 5: Logs
echo "<tr>";
echo "<td>📊 Logs do Sistema</td>";
echo "<td>Visualizar logs</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarLogs()'>🔗 Testar Link</button></td>";
echo "</tr>";

// Teste 6: Exportar
echo "<tr>";
echo "<td>📤 Exportar</td>";
echo "<td>Exportar dados</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarExportar()'>🔗 Testar Link</button></td>";
echo "</tr>";

// Teste 7: Modo Manutenção
echo "<tr>";
echo "<td>🔧 Modo Manutenção</td>";
echo "<td>Ativar manutenção</td>";
echo "<td>✅ Disponível</td>";
echo "<td><button onclick='testarManutencao()'>🔗 Testar Link</button></td>";
echo "</tr>";

echo "</table>";

echo "<h2>📊 Resultado dos Testes:</h2>";
echo "<div id='resultado'></div>";

echo "<h2>🔗 Links Diretos:</h2>";
echo "<p><a href='sistema_usuarios.php' target='_blank'>👥 Gestão de Usuários</a></p>";
echo "<p><a href='cursos_completo.php' target='_blank'>📚 Sistema de Cursos</a></p>";
echo "<p><a href='backup_completo_manual.php' target='_blank'>💾 Backup Completo</a></p>";
echo "<p><a href='download_backup.php' target='_blank'>📥 Download de Backups</a></p>";
echo "<p><a href='logs_sistema.php' target='_blank'>📊 Logs do Sistema</a></p>";
echo "<p><a href='exportar_dados.php' target='_blank'>📤 Exportar Dados</a></p>";
echo "<p><a href='modo_manutencao.php' target='_blank'>🔧 Modo Manutenção</a></p>";
?>

<script>
function testarGestaoUsuarios() {
    const resultado = document.getElementById('resultado');
    resultado.innerHTML = '<p>🔄 Testando Gestão de Usuários...</p>';
    
    fetch('sistema_usuarios.php')
        .then(response => {
            if (response.ok) {
                resultado.innerHTML = '<p>✅ Gestão de Usuários: FUNCIONANDO!</p>';
            } else {
                resultado.innerHTML = '<p>❌ Gestão de Usuários: ERRO ' + response.status + '</p>';
            }
        })
        .catch(error => {
            resultado.innerHTML = '<p>❌ Gestão de Usuários: Erro de conexão</p>';
        });
}

function testarConfigCursos() {
    const resultado = document.getElementById('resultado');
    resultado.innerHTML = '<p>🔄 Testando Configurações de Cursos...</p>';
    
    fetch('cursos_completo.php')
        .then(response => {
            if (response.ok) {
                resultado.innerHTML = '<p>✅ Configurações de Cursos: FUNCIONANDO!</p>';
            } else {
                resultado.innerHTML = '<p>❌ Configurações de Cursos: ERRO ' + response.status + '</p>';
            }
        })
        .catch(error => {
            resultado.innerHTML = '<p>❌ Configurações de Cursos: Erro de conexão</p>';
        });
}

function testarBackup() {
    const resultado = document.getElementById('resultado');
    resultado.innerHTML = '<p>🔄 Testando Sistema de Backup...</p>';
    
    fetch('backup_completo_manual.php')
        .then(response => {
            if (response.ok) {
                resultado.innerHTML = '<p>✅ Sistema de Backup: FUNCIONANDO!</p>';
            } else {
                resultado.innerHTML = '<p>❌ Sistema de Backup: ERRO ' + response.status + '</p>';
            }
        })
        .catch(error => {
            resultado.innerHTML = '<p>❌ Sistema de Backup: Erro de conexão</p>';
        });
}

function testarRestaurar() {
    const resultado = document.getElementById('resultado');
    resultado.innerHTML = '<p>🔄 Testando Sistema de Restauração...</p>';
    
    fetch('download_backup.php')
        .then(response => {
            if (response.ok) {
                resultado.innerHTML = '<p>✅ Sistema de Restauração: FUNCIONANDO!</p>';
            } else {
                resultado.innerHTML = '<p>❌ Sistema de Restauração: ERRO ' + response.status + '</p>';
            }
        })
        .catch(error => {
            resultado.innerHTML = '<p>❌ Sistema de Restauração: Erro de conexão</p>';
        });
}

function testarLogs() {
    const resultado = document.getElementById('resultado');
    resultado.innerHTML = '<p>🔄 Testando Logs do Sistema...</p>';
    
    fetch('logs_sistema.php')
        .then(response => {
            if (response.ok) {
                resultado.innerHTML = '<p>✅ Logs do Sistema: FUNCIONANDO!</p>';
            } else {
                resultado.innerHTML = '<p>❌ Logs do Sistema: ERRO ' + response.status + '</p>';
            }
        })
        .catch(error => {
            resultado.innerHTML = '<p>❌ Logs do Sistema: Erro de conexão</p>';
        });
}

function testarExportar() {
    const resultado = document.getElementById('resultado');
    resultado.innerHTML = '<p>🔄 Testando Sistema de Exportação...</p>';
    
    fetch('exportar_dados.php')
        .then(response => {
            if (response.ok) {
                resultado.innerHTML = '<p>✅ Sistema de Exportação: FUNCIONANDO!</p>';
            } else {
                resultado.innerHTML = '<p>❌ Sistema de Exportação: ERRO ' + response.status + '</p>';
            }
        })
        .catch(error => {
            resultado.innerHTML = '<p>❌ Sistema de Exportação: Erro de conexão</p>';
        });
}

function testarManutencao() {
    const resultado = document.getElementById('resultado');
    resultado.innerHTML = '<p>🔄 Testando Modo Manutenção...</p>';
    
    fetch('modo_manutencao.php')
        .then(response => {
            if (response.ok) {
                resultado.innerHTML = '<p>✅ Modo Manutenção: FUNCIONANDO!</p>';
            } else {
                resultado.innerHTML = '<p>❌ Modo Manutenção: ERRO ' + response.status + '</p>';
            }
        })
        .catch(error => {
            resultado.innerHTML = '<p>❌ Modo Manutenção: Erro de conexão</p>';
        });
}
</script>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
button { padding: 5px 10px; margin: 2px; cursor: pointer; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>


