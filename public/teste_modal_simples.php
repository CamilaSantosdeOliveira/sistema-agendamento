<!DOCTYPE html>
<html>
<head>
    <title>Teste Modal Simples</title>
</head>
<body>
    <h1>🔍 Teste Modal Simples</h1>
    
    <button onclick="abrirModal()" style="padding: 15px 30px; background: #10b981; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; margin: 20px 0;">
        📅 Abrir Modal
    </button>
    
    <script>
        function abrirModal() {
            const modal = document.createElement('div');
            modal.id = 'modalTeste';
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
                        <h2 style="margin: 0;">📅 Teste Modal</h2>
                        <button onclick="document.getElementById('modalTeste').remove()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                    </div>
                    
                    <form>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Nome do Aluno:</label>
                            <input type="text" name="nome" placeholder="Nome completo" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email:</label>
                            <input type="email" name="email" placeholder="email@exemplo.com" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Professor:</label>
                            <select name="professor" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                <option value="">Selecione...</option>
                                <option value="Prof. Maria">Prof. Maria</option>
                                <option value="Prof. João">Prof. João</option>
                            </select>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Curso:</label>
                            <select name="curso" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                <option value="">Selecione...</option>
                                <option value="PHP">PHP</option>
                                <option value="JavaScript">JavaScript</option>
                            </select>
                        </div>
                        
                        <div style="text-align: center;">
                            <button type="button" onclick="document.getElementById('modalTeste').remove()" style="padding: 10px 20px; background: #64748b; color: white; border: none; border-radius: 4px; cursor: pointer;">Fechar</button>
                        </div>
                    </form>
                </div>
            `;
            
            document.body.appendChild(modal);
        }
    </script>
</body>
</html>


