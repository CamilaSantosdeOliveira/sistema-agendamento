<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Detalhado API Certificados</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        .step { margin: 15px 0; padding: 15px; border-left: 4px solid #ccc; background: #f9f9f9; }
        .btn { padding: 10px 20px; margin: 5px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block; }
        .btn:hover { background: #0056b3; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🧪 Teste Detalhado API Certificados</h1>
    <p>Testando cada aspecto da API de certificados</p>

    <div class='step'>
        <h3>📋 Teste 1: Verificar Ações Disponíveis</h3>
        <button onclick="testarAcoes()" class="btn">🔍 Testar Ações</button>
        <div id="resultado-acoes"></div>
    </div>

    <div class='step'>
        <h3>📋 Teste 2: Testar Emissão Individual</h3>
        <button onclick="testarEmissaoIndividual()" class="btn">📜 Testar Emissão</button>
        <div id="resultado-emissao"></div>
    </div>

    <div class='step'>
        <h3>📋 Teste 3: Verificar Dados Enviados</h3>
        <button onclick="verificarDados()" class="btn">📊 Verificar Dados</button>
        <div id="resultado-dados"></div>
    </div>

    <div class='step'>
        <h3>🎯 Resultados</h3>
        <div id="resultado"></div>
    </div>

    <div class='step'>
        <h3>🔧 Navegação</h3>
        <a href="certificados.php" class="btn">📜 Voltar para Certificados</a>
        <a href="dashboard_final.php" class="btn">🏠 Dashboard</a>
    </div>

    <script>
        function mostrarResultado(elemento, mensagem, tipo = 'info') {
            const div = document.getElementById(elemento);
            div.innerHTML = `<p class="${tipo}">${mensagem}</p>`;
        }

        async function testarAcoes() {
            try {
                console.log('🧪 Testando ações disponíveis...');
                mostrarResultado('resultado-acoes', '🔄 Testando...', 'info');
                
                // Testar ação vazia
                const response1 = await fetch('api/certificados.php');
                const data1 = await response1.json();
                console.log('Ação vazia:', data1);
                
                // Testar ação inexistente
                const response2 = await fetch('api/certificados.php?action=acao_inexistente');
                const data2 = await response2.json();
                console.log('Ação inexistente:', data2);
                
                // Testar ação válida
                const response3 = await fetch('api/certificados.php?action=test_connection');
                const data3 = await response3.json();
                console.log('Ação válida:', data3);
                
                mostrarResultado('resultado-acoes', '✅ Teste de ações concluído. Verifique o console.', 'success');
            } catch (error) {
                console.error('❌ Erro no teste de ações:', error);
                mostrarResultado('resultado-acoes', `❌ Erro: ${error.message}`, 'error');
            }
        }

        async function testarEmissaoIndividual() {
            try {
                console.log('🧪 Testando emissão individual...');
                mostrarResultado('resultado-emissao', '🔄 Testando emissão...', 'info');
                
                const dados = {
                    action: 'emitir_certificado_individual',
                    aluno_id: 10,
                    curso_id: 1,
                    data_conclusao: new Date().toISOString().split('T')[0]
                };
                
                console.log('📤 Dados enviados:', dados);
                
                const response = await fetch('api/certificados.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(dados)
                });
                
                console.log('📡 Resposta HTTP:', response.status, response.statusText);
                
                const result = await response.json();
                console.log('📊 Resposta JSON:', result);
                
                if (result.success) {
                    mostrarResultado('resultado-emissao', `✅ Sucesso: ${result.message}`, 'success');
                } else {
                    mostrarResultado('resultado-emissao', `❌ Erro: ${result.message}`, 'error');
                }
            } catch (error) {
                console.error('❌ Erro no teste de emissão:', error);
                mostrarResultado('resultado-emissao', `❌ Erro: ${error.message}`, 'error');
            }
        }

        async function verificarDados() {
            try {
                console.log('🧪 Verificando dados...');
                mostrarResultado('resultado-dados', '🔄 Verificando...', 'info');
                
                // Verificar se o aluno existe
                const alunoResponse = await fetch('api/usuarios.php?action=buscar&id=10');
                const alunoData = await alunoResponse.json();
                console.log('👤 Dados do aluno:', alunoData);
                
                // Verificar se o curso existe
                const cursoResponse = await fetch('api/cursos.php?action=listar');
                const cursoData = await cursoResponse.json();
                console.log('📚 Dados dos cursos:', cursoData);
                
                if (alunoData.success && cursoData.success) {
                    mostrarResultado('resultado-dados', '✅ Dados verificados com sucesso', 'success');
                } else {
                    mostrarResultado('resultado-dados', '❌ Erro ao verificar dados', 'error');
                }
            } catch (error) {
                console.error('❌ Erro na verificação:', error);
                mostrarResultado('resultado-dados', `❌ Erro: ${error.message}`, 'error');
            }
        }

        // Teste automático ao carregar
        window.onload = function() {
            console.log('🧪 Página de teste carregada');
            mostrarResultado('resultado', '✅ Página carregada. Clique nos botões para testar.', 'success');
        };
    </script>
</body>
</html>









