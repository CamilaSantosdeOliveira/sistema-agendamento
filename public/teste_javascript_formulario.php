<!DOCTYPE html>
<html>
<head>
    <title>Teste JavaScript Formulário</title>
</head>
<body>
    <h2>🔍 Teste JavaScript - Carregamento de Dados</h2>
    
    <button onclick="testarCarregamento()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer;">
        🚀 Testar Carregamento de Dados
    </button>
    
    <div id="resultado" style="margin-top: 20px;"></div>
    
    <script>
        async function testarCarregamento() {
            const resultado = document.getElementById('resultado');
            resultado.innerHTML = '<p>🔄 Carregando dados...</p>';
            
            try {
                console.log('Iniciando requisição para get_dados_agendamento.php');
                
                const response = await fetch('get_dados_agendamento.php');
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Dados recebidos:', data);
                
                if (data.success) {
                    const professores = data.data.professores;
                    const cursos = data.data.cursos;
                    
                    let html = '<h3>✅ Dados carregados com sucesso!</h3>';
                    
                    html += '<h4>👨‍🏫 Professores (' + professores.length + '):</h4>';
                    html += '<ul>';
                    professores.forEach(prof => {
                        html += '<li>' + prof.nome + '</li>';
                    });
                    html += '</ul>';
                    
                    html += '<h4>📚 Cursos (' + cursos.length + '):</h4>';
                    html += '<ul>';
                    cursos.forEach(curso => {
                        html += '<li>' + curso.nome + '</li>';
                    });
                    html += '</ul>';
                    
                    html += '<h4>🎯 Teste do Modal:</h4>';
                    html += '<button onclick="testarModal()" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 5px; cursor: pointer;">📅 Abrir Modal de Agendamento</button>';
                    
                    resultado.innerHTML = html;
                } else {
                    resultado.innerHTML = '<p>❌ Erro: ' + (data.message || 'Erro desconhecido') + '</p>';
                }
                
            } catch (error) {
                console.error('Erro:', error);
                resultado.innerHTML = '<p>❌ Erro ao carregar dados: ' + error.message + '</p>';
            }
        }
        
        function testarModal() {
            // Simular a função do dashboard
            fetch('get_dados_agendamento.php')
                .then(response => response.json())
                .then(response => {
                    const data = response.data;
                    
                    // Criar opções dos professores
                    let professoresOptions = '<option value="">Selecione um professor...</option>';
                    if (data.professores && data.professores.length > 0) {
                        data.professores.forEach(prof => {
                            professoresOptions += `<option value="${prof.nome}">${prof.nome}</option>`;
                        });
                    }
                    
                    // Criar opções dos cursos
                    let cursosOptions = '<option value="">Selecione um curso...</option>';
                    if (data.cursos && data.cursos.length > 0) {
                        data.cursos.forEach(curso => {
                            cursosOptions += `<option value="${curso.nome}">${curso.nome}</option>`;
                        });
                    }
                    
                    const modal = document.createElement('div');
                    modal.id = 'testeModal';
                    modal.style.cssText = `
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0,0,0,0.5);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        z-index: 10000;
                    `;
                    
                    modal.innerHTML = `
                        <div style="background: white; padding: 30px; border-radius: 12px; width: 600px; max-width: 90%;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <h2 style="margin: 0;">📅 Teste do Modal</h2>
                                <button onclick="document.getElementById('testeModal').remove()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Professor:</label>
                                <select style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                                    ${professoresOptions}
                                </select>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Curso:</label>
                                <select style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                                    ${cursosOptions}
                                </select>
                            </div>
                            
                            <div style="text-align: center;">
                                <button onclick="document.getElementById('testeModal').remove()" style="padding: 10px 20px; background: #64748b; color: white; border: none; border-radius: 6px; cursor: pointer;">Fechar</button>
                            </div>
                        </div>
                    `;
                    
                    document.body.appendChild(modal);
                })
                .catch(error => {
                    console.error('Erro ao carregar modal:', error);
                    alert('Erro ao carregar modal: ' + error.message);
                });
        }
    </script>
</body>
</html>




