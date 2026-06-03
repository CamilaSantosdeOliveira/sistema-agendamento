<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 Debug Console</title>
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
        button {
            background: #007cba;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        .result {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            white-space: pre-wrap;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        .info { background: #d1ecf1; border-color: #bee5eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Console</h1>
        
        <button onclick="testarCompleto()">🔍 Teste Completo</button>
        <button onclick="testarMySQL()">🗄️ Testar MySQL</button>
        <button onclick="testarAPI()">🧪 Testar API</button>
        <button onclick="limparConsole()">🧹 Limpar Console</button>
        
        <div id="resultado" class="result">Clique em um botão para começar...</div>
    </div>

    <script>
        function log(message, type = 'info') {
            const resultado = document.getElementById('resultado');
            const timestamp = new Date().toLocaleTimeString();
            resultado.textContent += `[${timestamp}] ${message}\n`;
            
            if (type === 'error') {
                resultado.className = 'result error';
            } else if (type === 'success') {
                resultado.className = 'result success';
            } else {
                resultado.className = 'result info';
            }
            
            // Scroll para o final
            resultado.scrollTop = resultado.scrollHeight;
        }
        
        async function testarCompleto() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔍 Iniciando teste completo...\n';
            
            try {
                log('1️⃣ Testando conexão com MySQL...');
                const mysqlResponse = await fetch('teste_banco.php');
                const mysqlText = await mysqlResponse.text();
                
                if (mysqlText.includes('✅ Conexão estabelecida')) {
                    log('✅ MySQL está funcionando!', 'success');
                } else {
                    log('❌ MySQL não está funcionando', 'error');
                    log('📋 Resposta MySQL: ' + mysqlText.substring(0, 200));
                }
                
                log('2️⃣ Testando API...');
                const apiResponse = await fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'desativar_usuario',
                        id: 1
                    })
                });
                
                log(`📊 Status da API: ${apiResponse.status}`);
                log(`📋 Content-Type: ${apiResponse.headers.get('content-type')}`);
                
                const apiText = await apiResponse.text();
                log(`📝 Tamanho da resposta: ${apiText.length} caracteres`);
                
                if (apiText.length === 0) {
                    log('❌ API retornou resposta vazia!', 'error');
                } else {
                    log('📋 Primeiros 200 caracteres: ' + apiText.substring(0, 200));
                    
                    try {
                        const apiData = JSON.parse(apiText);
                        log('✅ JSON válido!', 'success');
                        log('📋 Dados: ' + JSON.stringify(apiData, null, 2));
                    } catch (e) {
                        log('❌ JSON inválido: ' + e.message, 'error');
                    }
                }
                
            } catch (error) {
                log('❌ Erro geral: ' + error.message, 'error');
                log('📋 Stack: ' + error.stack, 'error');
            }
        }
        
        async function testarMySQL() {
            log('🗄️ Testando MySQL...');
            
            try {
                const response = await fetch('teste_banco.php');
                const text = await response.text();
                
                if (text.includes('✅ Conexão estabelecida')) {
                    log('✅ MySQL funcionando!', 'success');
                } else {
                    log('❌ MySQL não funcionando', 'error');
                    log('📋 Resposta: ' + text.substring(0, 200));
                }
                
            } catch (error) {
                log('❌ Erro MySQL: ' + error.message, 'error');
            }
        }
        
        async function testarAPI() {
            log('🧪 Testando API...');
            
            try {
                const response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'desativar_usuario',
                        id: 1
                    })
                });
                
                log(`📊 Status: ${response.status}`);
                
                const text = await response.text();
                log(`📝 Resposta (${text.length} chars): ${text.substring(0, 200)}`);
                
                if (text.length > 0) {
                    try {
                        const data = JSON.parse(text);
                        log('✅ JSON válido!', 'success');
                    } catch (e) {
                        log('❌ JSON inválido: ' + e.message, 'error');
                    }
                } else {
                    log('❌ Resposta vazia!', 'error');
                }
                
            } catch (error) {
                log('❌ Erro API: ' + error.message, 'error');
            }
        }
        
        function limparConsole() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = 'Console limpo...\n';
            resultado.className = 'result';
        }
        
        // Log inicial
        log('🚀 Debug Console iniciado');
        log('📱 Abra o Console do navegador (F12) para ver logs detalhados');
    </script>
</body>
</html>


















