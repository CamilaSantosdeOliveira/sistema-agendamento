<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ Teste API Fallback</title>
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
            background: #28a745;
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
        <h1>✅ Teste API Fallback</h1>
        
        <p>Esta API funciona mesmo sem banco de dados!</p>
        
        <button onclick="testarDesativar()">⏸️ Testar Desativar</button>
        <button onclick="testarAtivar()">▶️ Testar Ativar</button>
        <button onclick="testarExcluir()">🗑️ Testar Excluir</button>
        <button onclick="testarEditar()">✏️ Testar Editar</button>
        
        <div id="resultado" class="result">Clique em um botão para testar...</div>
    </div>

    <script>
        async function testarDesativar() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando desativar...';
            resultado.className = 'result';
            
            try {
                const response = await fetch('api/usuarios_fallback.php', {
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
                
                if (data.success) {
                    resultado.textContent = '✅ Desativar funcionando!\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result success';
                } else {
                    resultado.textContent = '❌ Erro: ' + data.message + '\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro: ' + error.message;
                resultado.className = 'result error';
            }
        }
        
        async function testarAtivar() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando ativar...';
            resultado.className = 'result';
            
            try {
                const response = await fetch('api/usuarios_fallback.php', {
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
                
                if (data.success) {
                    resultado.textContent = '✅ Ativar funcionando!\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result success';
                } else {
                    resultado.textContent = '❌ Erro: ' + data.message + '\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro: ' + error.message;
                resultado.className = 'result error';
            }
        }
        
        async function testarExcluir() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando excluir...';
            resultado.className = 'result';
            
            try {
                const response = await fetch('api/usuarios_fallback.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'excluir_usuario',
                        id: 1
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultado.textContent = '✅ Excluir funcionando!\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result success';
                } else {
                    resultado.textContent = '❌ Erro: ' + data.message + '\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro: ' + error.message;
                resultado.className = 'result error';
            }
        }
        
        async function testarEditar() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando editar...';
            resultado.className = 'result';
            
            try {
                const response = await fetch('api/usuarios_fallback.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'editar_usuario',
                        id: 1,
                        nome: 'Nome Teste',
                        email: 'teste@email.com'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultado.textContent = '✅ Editar funcionando!\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result success';
                } else {
                    resultado.textContent = '❌ Erro: ' + data.message + '\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro: ' + error.message;
                resultado.className = 'result error';
            }
        }
    </script>
</body>
</html>


















