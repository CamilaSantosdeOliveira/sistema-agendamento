<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste JSON Puro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 600px;
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
        <h1>🧪 Teste JSON Puro</h1>
        
        <button onclick="testarDesativar()">⏸️ Testar API Original</button>
        
        <div id="resultado" class="result">Clique no botão para testar...</div>
    </div>

    <script>
        async function testarDesativar() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando API original...';
            resultado.className = 'result';
            
            try {
                console.log('Enviando requisição para API...');
                
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
                
                console.log('Resposta recebida. Status:', response.status);
                console.log('Content-Type:', response.headers.get('content-type'));
                
                const text = await response.text();
                console.log('Texto bruto:', text);
                
                resultado.textContent = `📋 Status: ${response.status}\n`;
                resultado.textContent += `📋 Content-Type: ${response.headers.get('content-type')}\n\n`;
                resultado.textContent += `📋 Resposta bruta:\n${text}\n\n`;
                
                // Tentar fazer parse do JSON
                try {
                    const data = JSON.parse(text);
                    resultado.textContent += `✅ JSON válido!\n`;
                    resultado.textContent += `📋 Dados:\n${JSON.stringify(data, null, 2)}`;
                    resultado.className = 'result success';
                } catch (e) {
                    resultado.textContent += `❌ JSON inválido!\n`;
                    resultado.textContent += `📋 Erro: ${e.message}\n`;
                    resultado.textContent += `📋 Primeiros 200 caracteres: ${text.substring(0, 200)}`;
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                console.error('Erro na requisição:', error);
                resultado.textContent = '❌ Erro de conexão: ' + error.message;
                resultado.className = 'result error';
            }
        }
    </script>
</body>
</html>


















