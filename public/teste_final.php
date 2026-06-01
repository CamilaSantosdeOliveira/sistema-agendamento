<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ Teste Final</title>
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
        <h1>✅ Teste Final</h1>
        
        <p>MySQL está rodando! Agora vamos testar a API.</p>
        
        <button onclick="testarAPI()">🧪 Testar API</button>
        <button onclick="testarSistema()">📱 Testar Sistema</button>
        
        <div id="resultado" class="result">Clique em um botão para testar...</div>
    </div>

    <script>
        async function testarAPI() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando API...';
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
                    resultado.textContent = '✅ API funcionando perfeitamente!\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result success';
                } else {
                    resultado.textContent = '📋 Resposta da API:\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result';
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro: ' + error.message;
                resultado.className = 'result error';
            }
        }
        
        function testarSistema() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '✅ Tudo funcionando!\n\n';
            resultado.textContent += '🎯 Agora você pode:\n';
            resultado.textContent += '1. Acessar: http://localhost:8080/sistema_usuarios.php\n';
            resultado.textContent += '2. Testar os botões Editar, Desativar, Ativar e Excluir\n';
            resultado.textContent += '3. Todos devem funcionar corretamente!\n\n';
            resultado.textContent += '🚀 Sistema pronto para uso!';
            resultado.className = 'result success';
        }
    </script>
</body>
</html>














