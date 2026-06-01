<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✏️ Teste de Edição</title>
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
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Teste de Edição de Usuário</h1>
        
        <div class="warning">
            <strong>⚠️ IMPORTANTE:</strong> Atualmente estamos usando a API de simulação (fallback).<br>
            As alterações NÃO são salvas no banco de dados real, apenas simuladas.
        </div>
        
        <h3>🧪 Testes Disponíveis:</h3>
        
        <button onclick="testarBuscarUsuario()">🔍 1. Buscar Dados do Usuário</button>
        <button onclick="testarSalvarEdicao()">💾 2. Salvar Edição</button>
        <button onclick="testarFluxoCompleto()">🔄 3. Fluxo Completo</button>
        
        <div id="resultado" class="result">Clique em um botão para testar...</div>
    </div>

    <script>
        async function testarBuscarUsuario() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔍 Testando buscar dados do usuário...';
            resultado.className = 'result';
            
            try {
                const response = await fetch('api/usuarios_fallback.php?action=buscar_usuario&id=1');
                const data = await response.json();
                
                if (data.success) {
                    resultado.textContent = '✅ Buscar usuário funcionando!\n\n';
                    resultado.textContent += 'Dados retornados:\n';
                    resultado.textContent += JSON.stringify(data.data, null, 2);
                    resultado.className = 'result success';
                } else {
                    resultado.textContent = '❌ Erro ao buscar usuário:\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro: ' + error.message;
                resultado.className = 'result error';
            }
        }
        
        async function testarSalvarEdicao() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '💾 Testando salvar edição...';
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
                        nome: 'Nome Editado',
                        email: 'novo@email.com',
                        telefone: '(11) 88888-8888',
                        formacao: 'Nova Formação',
                        valor_hora: 150.00
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultado.textContent = '✅ Salvar edição funcionando!\n\n';
                    resultado.textContent += 'Resposta da API:\n';
                    resultado.textContent += JSON.stringify(data, null, 2);
                    resultado.className = 'result success';
                } else {
                    resultado.textContent = '❌ Erro ao salvar:\n\n' + JSON.stringify(data, null, 2);
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                resultado.textContent = '❌ Erro: ' + error.message;
                resultado.className = 'result error';
            }
        }
        
        async function testarFluxoCompleto() {
            const resultado = document.getElementById('resultado');
            resultado.textContent = '🔄 Testando fluxo completo de edição...\n';
            resultado.className = 'result';
            
            try {
                // Passo 1: Buscar dados
                resultado.textContent += '\n1️⃣ Buscando dados do usuário...\n';
                const buscarResponse = await fetch('api/usuarios_fallback.php?action=buscar_usuario&id=1');
                const userData = await buscarResponse.json();
                
                if (userData.success) {
                    resultado.textContent += '✅ Dados encontrados!\n';
                    resultado.textContent += 'Nome atual: ' + userData.data.nome + '\n';
                    resultado.textContent += 'Email atual: ' + userData.data.email + '\n\n';
                    
                    // Passo 2: Simular edição
                    resultado.textContent += '2️⃣ Simulando edição de dados...\n';
                    
                    // Passo 3: Salvar alterações
                    resultado.textContent += '3️⃣ Salvando alterações...\n';
                    const editarResponse = await fetch('api/usuarios_fallback.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'editar_usuario',
                            id: 1,
                            nome: 'Nome Alterado pelo Teste',
                            email: 'teste_edicao@email.com',
                            telefone: userData.data.telefone,
                            formacao: userData.data.formacao || '',
                            valor_hora: userData.data.valor_hora || 0
                        })
                    });
                    
                    const editData = await editarResponse.json();
                    
                    if (editData.success) {
                        resultado.textContent += '✅ Alterações salvas com sucesso!\n\n';
                        resultado.textContent += '🎉 FLUXO COMPLETO FUNCIONANDO!\n\n';
                        resultado.textContent += 'Resposta final:\n';
                        resultado.textContent += JSON.stringify(editData, null, 2);
                        resultado.className = 'result success';
                    } else {
                        resultado.textContent += '❌ Erro ao salvar alterações\n';
                        resultado.textContent += JSON.stringify(editData, null, 2);
                        resultado.className = 'result error';
                    }
                    
                } else {
                    resultado.textContent += '❌ Erro ao buscar dados\n';
                    resultado.textContent += JSON.stringify(userData, null, 2);
                    resultado.className = 'result error';
                }
                
            } catch (error) {
                resultado.textContent += '❌ Erro no fluxo: ' + error.message;
                resultado.className = 'result error';
            }
        }
    </script>
</body>
</html>
















