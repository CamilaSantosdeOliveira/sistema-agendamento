<!DOCTYPE html>
<html>
<head>
    <title>Teste Console - Dados</title>
</head>
<body>
    <h1>🔍 Teste Console - Verificar Dados</h1>
    
    <button onclick="testarDados()" style="padding: 15px 30px; background: #10b981; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; margin: 20px 0;">
        🔍 Testar Carregamento de Dados
    </button>
    
    <div id="resultado" style="margin-top: 20px; padding: 15px; background: #f3f4f6; border-radius: 8px;"></div>
    
    <script>
        async function testarDados() {
            const resultado = document.getElementById('resultado');
            resultado.innerHTML = '<p>🔄 Carregando dados...</p>';
            
            try {
                console.log('=== INICIANDO TESTE ===');
                
                const response = await fetch('get_dados_agendamento.php');
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('=== DADOS RECEBIDOS ===');
                console.log('Data completa:', data);
                
                if (data.success) {
                    const professores = data.data.professores;
                    const cursos = data.data.cursos;
                    
                    console.log('=== PROFESSORES ===');
                    console.log('Quantidade:', professores.length);
                    console.log('Dados:', professores);
                    
                    console.log('=== CURSOS ===');
                    console.log('Quantidade:', cursos.length);
                    console.log('Dados:', cursos);
                    
                    // Criar opções dos professores
                    let professoresOptions = '<option value="">Selecione um professor...</option>';
                    if (professores && professores.length > 0) {
                        professores.forEach(prof => {
                            professoresOptions += `<option value="${prof.nome}">${prof.nome}</option>`;
                        });
                    }
                    console.log('=== OPÇÕES PROFESSORES ===');
                    console.log('HTML gerado:', professoresOptions);
                    
                    // Criar opções dos cursos
                    let cursosOptions = '<option value="">Selecione um curso...</option>';
                    if (cursos && cursos.length > 0) {
                        cursos.forEach(curso => {
                            cursosOptions += `<option value="${curso.nome}">${curso.nome}</option>`;
                        });
                    }
                    console.log('=== OPÇÕES CURSOS ===');
                    console.log('HTML gerado:', cursosOptions);
                    
                    let html = '<h3>✅ Dados carregados com sucesso!</h3>';
                    html += '<p><strong>Professores:</strong> ' + professores.length + '</p>';
                    html += '<p><strong>Cursos:</strong> ' + cursos.length + '</p>';
                    
                    html += '<h4>👨‍🏫 Professores:</h4>';
                    html += '<ul>';
                    professores.forEach(prof => {
                        html += '<li>' + prof.nome + '</li>';
                    });
                    html += '</ul>';
                    
                    html += '<h4>📚 Cursos:</h4>';
                    html += '<ul>';
                    cursos.forEach(curso => {
                        html += '<li>' + curso.nome + '</li>';
                    });
                    html += '</ul>';
                    
                    html += '<h4>🎯 Teste do Select:</h4>';
                    html += '<select style="width: 200px; padding: 10px; margin: 10px 0;">';
                    html += professoresOptions;
                    html += '</select>';
                    
                    html += '<br><select style="width: 200px; padding: 10px; margin: 10px 0;">';
                    html += cursosOptions;
                    html += '</select>';
                    
                    resultado.innerHTML = html;
                    
                } else {
                    console.log('=== ERRO NA API ===');
                    console.log('Erro:', data.message);
                    resultado.innerHTML = '<p>❌ Erro: ' + (data.message || 'Erro desconhecido') + '</p>';
                }
                
            } catch (error) {
                console.error('=== ERRO GERAL ===');
                console.error('Erro:', error);
                resultado.innerHTML = '<p>❌ Erro ao carregar dados: ' + error.message + '</p>';
            }
        }
    </script>
</body>
</html>


