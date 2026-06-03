<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 Iniciar MySQL - XAMPP</title>
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
        .alert {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .steps {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .step {
            margin: 15px 0;
            padding: 10px;
            background: white;
            border-left: 4px solid #007cba;
            border-radius: 3px;
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
        .btn-test {
            background: #007cba;
        }
        .result {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 MySQL não está rodando!</h1>
        
        <div class="alert alert-warning">
            <strong>⚠️ Problema:</strong> O MySQL precisa estar rodando para que a API funcione corretamente.
        </div>
        
        <div class="alert alert-info">
            <strong>💡 Solução:</strong> Siga os passos abaixo para iniciar o MySQL no XAMPP.
        </div>
        
        <div class="steps">
            <h3>📋 Passos para iniciar o MySQL:</h3>
            
            <div class="step">
                <strong>1️⃣ Abrir XAMPP Control Panel</strong><br>
                • Procure por "XAMPP Control Panel" no menu iniciar<br>
                • Ou clique no ícone do XAMPP na barra de tarefas
            </div>
            
            <div class="step">
                <strong>2️⃣ Iniciar MySQL</strong><br>
                • Localize a linha "MySQL" no painel<br>
                • Clique no botão "Start" ao lado de MySQL<br>
                • Aguarde até o status ficar verde
            </div>
            
            <div class="step">
                <strong>3️⃣ Verificar se está funcionando</strong><br>
                • O status do MySQL deve mostrar "Running"<br>
                • Clique no botão abaixo para testar
            </div>
        </div>
        
        <button class="btn-test" onclick="testarMySQL()">🔍 Testar MySQL</button>
        <button onclick="testarAPI()">🧪 Testar API</button>
        
        <div id="resultado" class="result" style="display: none;"></div>
    </div>

    <script>
        async function testarMySQL() {
            const resultado = document.getElementById('resultado');
            resultado.style.display = 'block';
            resultado.textContent = '🔄 Testando conexão com MySQL...';
            resultado.className = 'result';
            
            try {
                const response = await fetch('teste_banco.php');
                const text = await response.text();
                
                if (text.includes('✅ Conexão estabelecida')) {
                    resultado.textContent = '✅ MySQL está funcionando!\n\nAgora você pode usar o sistema normalmente.';
                    resultado.className = 'result success';
                } else {
                    resultado.textContent = '❌ MySQL ainda não está funcionando.\n\nResposta:\n' + text;
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro ao testar: ' + error.message;
                resultado.className = 'result error';
            }
        }
        
        async function testarAPI() {
            const resultado = document.getElementById('resultado');
            resultado.style.display = 'block';
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
                    resultado.textContent = '✅ API funcionando!\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result success';
                } else {
                    resultado.textContent = '📋 Resposta da API:\n\n' + JSON.stringify(data, null, 2);
                    
                    if (data.message && data.message.includes('MySQL')) {
                        resultado.textContent += '\n\n⚠️ MySQL ainda não está rodando. Siga os passos acima.';
                        resultado.className = 'result error';
                    } else {
                        resultado.className = 'result';
                    }
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro ao testar API: ' + error.message;
                resultado.className = 'result error';
            }
        }
    </script>
</body>
</html>


















