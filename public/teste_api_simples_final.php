<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste API Simples</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Teste API Simples</h1>
        
        <button onclick="testarAPI()">🔧 Testar API Simples</button>
        <button onclick="testarDesativar()">⏸️ Testar Desativar</button>
        <button onclick="testarBanco()">🗄️ Testar Banco</button>
        
        <div id="resultado" class="result"></div>
    </div>

    <script>
        async function testarAPI() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando API simples...';
            resultado.className = 'result';
            
            try {
                const response = await fetch('api/teste_simples.php?action=teste');
                const text = await response.text();
                resultado.textContent = '📋 Resposta bruta:\n' + text;
                
                try {
                    const data = JSON.parse(text);
                    resultado.textContent += '\n\n📋 JSON parseado:\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result success';
                } catch (e) {
                    resultado.textContent += '\n\n❌ Erro ao fazer parse do JSON: ' + e.message;
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro de conexão: ' + error.message;
                resultado.className = 'result error';
            }
        }
        
        async function testarDesativar() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando desativar...';
            resultado.className = 'result';
            
            try {
                const response = await fetch('api/teste_simples.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'desativar_usuario',
                        id: 1
                    })
                });
                
                const text = await response.text();
                resultado.textContent = '📋 Resposta bruta:\n' + text;
                
                try {
                    const data = JSON.parse(text);
                    resultado.textContent += '\n\n📋 JSON parseado:\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result success';
                } catch (e) {
                    resultado.textContent += '\n\n❌ Erro ao fazer parse do JSON: ' + e.message;
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro de conexão: ' + error.message;
                resultado.className = 'result error';
            }
        }
        
        async function testarBanco() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando banco...';
            resultado.className = 'result';
            
            try {
                const response = await fetch('teste_banco.php');
                const text = await response.text();
                resultado.textContent = '📋 Resposta do teste de banco:\n' + text;
                resultado.className = 'result';
                
            } catch (error) {
                resultado.textContent = '❌ Erro de conexão: ' + error.message;
                resultado.className = 'result error';
            }
        }
    </script>
</body>
</html>
















