<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste API - Desativar Usuário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .test-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #007cba;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background: #005a87;
        }
        .result {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            white-space: pre-wrap;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Teste da API - Desativar Usuário</h1>
        
        <div class="test-section">
            <h3>1. Teste de Busca de Usuário</h3>
            <button onclick="testarBuscaUsuario()">🔍 Buscar Usuário ID 1</button>
            <div id="resultadoBusca" class="result"></div>
        </div>
        
        <div class="test-section">
            <h3>2. Teste de Desativar Usuário</h3>
            <button onclick="testarDesativarUsuario()">⏸️ Desativar Usuário ID 1</button>
            <div id="resultadoDesativar" class="result"></div>
        </div>
        
        <div class="test-section">
            <h3>3. Teste de Ativar Usuário</h3>
            <button onclick="testarAtivarUsuario()">▶️ Ativar Usuário ID 1</button>
            <div id="resultadoAtivar" class="result"></div>
        </div>
        
        <div class="test-section">
            <h3>4. Teste de Excluir Usuário</h3>
            <button onclick="testarExcluirUsuario()">🗑️ Excluir Usuário ID 1</button>
            <div id="resultadoExcluir" class="result"></div>
        </div>
        
        <div class="test-section">
            <h3>5. Logs do Console</h3>
            <button onclick="limparLogs()">🧹 Limpar Logs</button>
            <div id="logs" class="result info"></div>
        </div>
    </div>

    <script>
        // Função para mostrar logs
        function log(message) {
            const logsDiv = document.getElementById('logs');
            const timestamp = new Date().toLocaleTimeString();
            logsDiv.textContent += `[${timestamp}] ${message}\n`;
            console.log(message);
        }
        
        // Função para mostrar resultado
        function mostrarResultado(elementId, data, isSuccess = true) {
            const element = document.getElementById(elementId);
            element.textContent = JSON.stringify(data, null, 2);
            element.className = `result ${isSuccess ? 'success' : 'error'}`;
        }
        
        // Função para limpar logs
        function limparLogs() {
            document.getElementById('logs').textContent = '';
        }
        
        // Teste de busca de usuário
        async function testarBuscaUsuario() {
            log('🔄 Testando busca de usuário...');
            try {
                const response = await fetch('api/usuarios.php?action=buscar_usuario&id=1');
                const data = await response.json();
                
                log(`📥 Resposta da busca: ${JSON.stringify(data)}`);
                mostrarResultado('resultadoBusca', data, data.success);
                
            } catch (error) {
                log(`❌ Erro na busca: ${error.message}`);
                mostrarResultado('resultadoBusca', {error: error.message}, false);
            }
        }
        
        // Teste de desativar usuário
        async function testarDesativarUsuario() {
            log('🔄 Testando desativar usuário...');
            try {
                const requestData = {
                    action: 'desativar_usuario',
                    id: 1
                };
                
                log(`📤 Enviando dados: ${JSON.stringify(requestData)}`);
                
                const response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestData)
                });
                
                const data = await response.json();
                
                log(`📥 Resposta da desativação: ${JSON.stringify(data)}`);
                mostrarResultado('resultadoDesativar', data, data.success);
                
            } catch (error) {
                log(`❌ Erro na desativação: ${error.message}`);
                mostrarResultado('resultadoDesativar', {error: error.message}, false);
            }
        }
        
        // Teste de ativar usuário
        async function testarAtivarUsuario() {
            log('🔄 Testando ativar usuário...');
            try {
                const requestData = {
                    action: 'ativar_usuario',
                    id: 1
                };
                
                log(`📤 Enviando dados: ${JSON.stringify(requestData)}`);
                
                const response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestData)
                });
                
                const data = await response.json();
                
                log(`📥 Resposta da ativação: ${JSON.stringify(data)}`);
                mostrarResultado('resultadoAtivar', data, data.success);
                
            } catch (error) {
                log(`❌ Erro na ativação: ${error.message}`);
                mostrarResultado('resultadoAtivar', {error: error.message}, false);
            }
        }
        
        // Teste de excluir usuário
        async function testarExcluirUsuario() {
            log('🔄 Testando excluir usuário...');
            try {
                const requestData = {
                    action: 'excluir_usuario',
                    id: 1
                };
                
                log(`📤 Enviando dados: ${JSON.stringify(requestData)}`);
                
                const response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestData)
                });
                
                const data = await response.json();
                
                log(`📥 Resposta da exclusão: ${JSON.stringify(data)}`);
                mostrarResultado('resultadoExcluir', data, data.success);
                
            } catch (error) {
                log(`❌ Erro na exclusão: ${error.message}`);
                mostrarResultado('resultadoExcluir', {error: error.message}, false);
            }
        }
        
        // Inicializar logs
        log('🚀 Página de teste carregada');
        log('📋 Clique nos botões para testar as funcionalidades');
    </script>
</body>
</html>
















