<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste JavaScript Certificados</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        .step { margin: 15px 0; padding: 15px; border-left: 4px solid #ccc; background: #f9f9f9; }
        .btn { padding: 10px 20px; margin: 5px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block; }
        .btn:hover { background: #0056b3; }
        #resultado { margin: 20px 0; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🧪 Teste JavaScript Certificados</h1>
    <p>Testando a função de emissão de certificados via JavaScript</p>

    <div class='step'>
        <h3>📋 Teste 1: Buscar Cursos via JavaScript</h3>
        <button onclick="testarBuscaCursos()" class="btn">🔍 Testar Busca de Cursos</button>
        <div id="resultado-cursos"></div>
    </div>

    <div class='step'>
        <h3>📋 Teste 2: Emitir Certificado via JavaScript</h3>
        <button onclick="testarEmissaoCertificado()" class="btn">📜 Testar Emissão de Certificado</button>
        <div id="resultado-emissao"></div>
    </div>

    <div class='step'>
        <h3>📋 Teste 3: Verificar Console</h3>
        <p>Abra o console do navegador (F12) para ver mensagens de erro detalhadas</p>
        <button onclick="testarConsole()" class="btn">🔍 Testar Console</button>
    </div>

    <div class='step'>
        <h3>🎯 Resultados</h3>
        <div id="resultado"></div>
    </div>

    <div class='step'>
        <h3>🔧 Navegação</h3>
        <a href="certificados.php" class="btn">📜 Voltar para Certificados</a>
        <a href="dashboard_final.php" class="btn">🏠 Dashboard</a>
        <a href="debug_conexao_certificados.php" class="btn">🔍 Debug Completo</a>
    </div>

    <script>
        // Função para mostrar resultados
        function mostrarResultado(elemento, mensagem, tipo = 'info') {
            const div = document.getElementById(elemento);
            div.innerHTML = `<p class="${tipo}">${mensagem}</p>`;
        }

        // Função para testar busca de cursos
        async function testarBuscaCursos() {
            try {
                console.log('🧪 Iniciando teste de busca de cursos...');
                mostrarResultado('resultado-cursos', '🔄 Buscando cursos...', 'info');
                
                const response = await fetch('api/cursos.php?action=listar');
                console.log('📡 Resposta da API:', response);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('📊 Dados recebidos:', data);
                
                if (data.success) {
                    mostrarResultado('resultado-cursos', `✅ Cursos encontrados: ${data.data.length}`, 'success');
                    console.log('✅ Busca de cursos bem-sucedida');
                } else {
                    mostrarResultado('resultado-cursos', `❌ Erro: ${data.message}`, 'error');
                    console.error('❌ Erro na busca de cursos:', data.message);
                }
            } catch (error) {
                console.error('❌ Erro no teste de busca de cursos:', error);
                mostrarResultado('resultado-cursos', `❌ Erro de conexão: ${error.message}`, 'error');
            }
        }

        // Função para testar emissão de certificado
        async function testarEmissaoCertificado() {
            try {
                console.log('🧪 Iniciando teste de emissão de certificado...');
                mostrarResultado('resultado-emissao', '🔄 Emitindo certificado...', 'info');
                
                // Primeiro buscar cursos
                const cursosResponse = await fetch('api/cursos.php?action=listar');
                const cursosData = await cursosResponse.json();
                
                if (!cursosData.success || cursosData.data.length === 0) {
                    throw new Error('Nenhum curso disponível');
                }
                
                const curso = cursosData.data[0];
                console.log('📚 Curso selecionado:', curso);
                
                // Emitir certificado
                const response = await fetch('api/certificados.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'emitir_certificado_individual',
                        aluno_id: 10, // João Silva
                        curso_id: curso.id,
                        data_conclusao: new Date().toISOString().split('T')[0]
                    })
                });
                
                console.log('📡 Resposta da API de certificados:', response);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('📊 Dados recebidos:', data);
                
                if (data.success) {
                    mostrarResultado('resultado-emissao', `✅ Certificado emitido: ${data.message}`, 'success');
                    console.log('✅ Emissão de certificado bem-sucedida');
                } else {
                    mostrarResultado('resultado-emissao', `❌ Erro: ${data.message}`, 'error');
                    console.error('❌ Erro na emissão:', data.message);
                }
            } catch (error) {
                console.error('❌ Erro no teste de emissão:', error);
                mostrarResultado('resultado-emissao', `❌ Erro de conexão: ${error.message}`, 'error');
            }
        }

        // Função para testar console
        function testarConsole() {
            console.log('🧪 Teste do console iniciado');
            console.info('ℹ️ Informação de teste');
            console.warn('⚠️ Aviso de teste');
            console.error('❌ Erro de teste');
            
            mostrarResultado('resultado', '✅ Teste do console executado. Verifique o console do navegador (F12)', 'success');
        }

        // Teste automático ao carregar a página
        window.onload = function() {
            console.log('🧪 Página de teste carregada');
            mostrarResultado('resultado', '✅ Página carregada. Clique nos botões para testar.', 'success');
        };
    </script>
</body>
</html>







