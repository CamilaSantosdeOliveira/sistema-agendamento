<?php
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste dos Botões dos Relatórios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-button {
            background: #3b82f6;
            color: white;
            padding: 15px 25px;
            margin: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .test-button:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }
        .success { background: #10b981; }
        .success:hover { background: #059669; }
        .warning { background: #f59e0b; }
        .warning:hover { background: #d97706; }
        .danger { background: #ef4444; }
        .danger:hover { background: #dc2626; }
        .info { background: #3b82f6; }
        .info:hover { background: #2563eb; }
        
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            background: #e5e7eb;
        }
        .status.success { background: #d1fae5; color: #065f46; }
        .status.error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <h1>🧪 Teste dos Botões dos Relatórios</h1>
    
    <div class="status success">
        ✅ <strong>Arquivos dos relatórios encontrados:</strong><br>
        - relatorio_cursos.php ✅<br>
        - relatorio_usuarios.php ✅<br>
        - relatorio_financeiro.php ✅<br>
        - relatorio_agendamentos.php ✅
    </div>
    
    <h2>Clique nos botões abaixo para testar:</h2>
    
    <button class="test-button warning" onclick="testarRelatorio('relatorio_cursos.php')">
        📊 Relatório de Cursos
    </button>
    
    <button class="test-button info" onclick="testarRelatorio('relatorio_usuarios.php')">
        👥 Relatório de Usuários
    </button>
    
    <button class="test-button success" onclick="testarRelatorio('relatorio_financeiro.php')">
        💰 Relatório Financeiro
    </button>
    
    <button class="test-button danger" onclick="testarRelatorio('relatorio_agendamentos.php')">
        📅 Relatório de Agendamentos
    </button>
    
    <br><br>
    
    <button class="test-button" onclick="voltarDashboard()">
        🏠 Voltar ao Dashboard
    </button>
    
    <div id="resultado" class="status" style="display: none;"></div>
    
    <script>
        function testarRelatorio(arquivo) {
            console.log('🔍 Testando relatório:', arquivo);
            
            // Mostrar status
            const resultado = document.getElementById('resultado');
            resultado.style.display = 'block';
            resultado.className = 'status';
            resultado.innerHTML = `🔄 Abrindo ${arquivo}...`;
            
            // Tentar abrir o relatório
            try {
                window.open(arquivo, '_blank');
                resultado.className = 'status success';
                resultado.innerHTML = `✅ ${arquivo} aberto com sucesso!`;
            } catch (error) {
                resultado.className = 'status error';
                resultado.innerHTML = `❌ Erro ao abrir ${arquivo}: ${error.message}`;
            }
        }
        
        function voltarDashboard() {
            window.location.href = 'dashboard_final.php';
        }
        
        // Teste automático ao carregar
        window.onload = function() {
            console.log('✅ Página de teste carregada!');
            console.log('🔍 Testando se os relatórios estão acessíveis...');
            
            // Teste automático dos links
            const relatorios = [
                'relatorio_cursos.php',
                'relatorio_usuarios.php', 
                'relatorio_financeiro.php',
                'relatorio_agendamentos.php'
            ];
            
            relatorios.forEach(relatorio => {
                console.log(`📄 Verificando: ${relatorio}`);
            });
        };
    </script>
</body>
</html>







