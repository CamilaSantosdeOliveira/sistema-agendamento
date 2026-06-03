<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Final da API</title>
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
        <h1>🧪 Teste Final da API</h1>
        
        <button onclick="testarAPI()">🔧 Testar API</button>
        <button onclick="testarDesativar()">⏸️ Testar Desativar</button>
        <button onclick="testarAtivar()">▶️ Testar Ativar</button>
        <button onclick="testarExcluir()">🗑️ Testar Excluir</button>
        
        <div id="resultado" class="result"></div>
    </div>

    <script>
        async function testarAPI() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando conexão com API...';
            resultado.className = 'result';
            
            try {
                const response = await fetch('api/usuarios.php?action=listar_alunos');
                const data = await response.json();
                
                resultado.textContent = '✅ API funcionando!\n' + JSON.stringify(data, null, 2);
                resultado.className = 'result success';
                
            } catch (error) {
                resultado.textContent = '❌ Erro na API: ' + error.message;
                resultado.className = 'result error';
            }
        }
        
        async function testarDesativar() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando desativar usuário...';
            resultado.className = 'result';
            
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
                
                if (data.success) {
                    resultado.textContent = '✅ Usuário desativado com sucesso!\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result success';
                } else {
                    resultado.textContent = '❌ Erro: ' + data.message + '\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro de conexão: ' + error.message;
                resultado.className = 'result error';
            }
        }
        
        async function testarAtivar() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando ativar usuário...';
            resultado.className = 'result';
            
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
                
                if (data.success) {
                    resultado.textContent = '✅ Usuário ativado com sucesso!\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result success';
                } else {
                    resultado.textContent = '❌ Erro: ' + data.message + '\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro de conexão: ' + error.message;
                resultado.className = 'result error';
            }
        }
        
        async function testarExcluir() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando excluir usuário...';
            resultado.className = 'result';
            
            try {
                const response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'excluir_usuario',
                        id: 999 // ID que não existe para teste
                    })
                });
                
                const data = await response.json();
                
                resultado.textContent = '📋 Resposta da API:\n' + JSON.stringify(data, null, 2);
                resultado.className = 'result';
                
            } catch (error) {
                resultado.textContent = '❌ Erro de conexão: ' + error.message;
                resultado.className = 'result error';
            }
        }
    </script>
</body>
</html>


















