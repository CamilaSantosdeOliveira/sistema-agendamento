<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Simples da API</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Teste Simples da API</h1>
        
        <button onclick="testarDesativar()">⏸️ Testar Desativar Usuário</button>
        <button onclick="testarAtivar()">▶️ Testar Ativar Usuário</button>
        
        <div id="resultado" class="result"></div>
    </div>

    <script>
        async function testarDesativar() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando...';
            
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
                
                const data = await response.json();
                resultado.textContent = JSON.stringify(data, null, 2);
                
            } catch (error) {
                resultado.textContent = '❌ Erro: ' + error.message;
            }
        }
        
        async function testarAtivar() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando...';
            
            try {
                const response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'ativar_usuario',
                        id: 1
                    })
                });
                
                const data = await response.json();
                resultado.textContent = JSON.stringify(data, null, 2);
                
            } catch (error) {
                resultado.textContent = '❌ Erro: ' + error.message;
            }
        }
    </script>
</body>
</html>
















