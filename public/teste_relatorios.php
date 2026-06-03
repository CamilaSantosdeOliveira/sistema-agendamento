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
    <title>Teste dos Relatórios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-card {
            background: white;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .test-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .success { border-left: 4px solid #10b981; }
        .info { border-left: 4px solid #3b82f6; }
        .warning { border-left: 4px solid #f59e0b; }
        .danger { border-left: 4px solid #ef4444; }
    </style>
</head>
<body>
    <h1>🧪 Teste dos Relatórios</h1>
    <p>Clique nos cards abaixo para testar se os relatórios estão funcionando:</p>
    
    <div class="test-card success" onclick="testarRelatorio('relatorio_cursos.php')">
        <h3>📊 Relatório de Cursos</h3>
        <p>Testar se o relatório de cursos está funcionando</p>
    </div>
    
    <div class="test-card info" onclick="testarRelatorio('relatorio_usuarios.php')">
        <h3>👥 Relatório de Usuários</h3>
        <p>Testar se o relatório de usuários está funcionando</p>
    </div>
    
    <div class="test-card warning" onclick="testarRelatorio('relatorio_financeiro.php')">
        <h3>💰 Relatório Financeiro</h3>
        <p>Testar se o relatório financeiro está funcionando</p>
    </div>
    
    <div class="test-card danger" onclick="testarRelatorio('relatorio_agendamentos.php')">
        <h3>📅 Relatório de Agendamentos</h3>
        <p>Testar se o relatório de agendamentos está funcionando</p>
    </div>
    
    <div class="test-card info" onclick="voltarDashboard()">
        <h3>🏠 Voltar ao Dashboard</h3>
        <p>Retornar ao dashboard principal</p>
    </div>
    
    <script>
        function testarRelatorio(arquivo) {
            console.log('Testando relatório:', arquivo);
            window.open(arquivo, '_blank');
        }
        
        function voltarDashboard() {
            window.location.href = 'dashboard_final.php';
        }
        
        // Teste automático ao carregar a página
        window.onload = function() {
            console.log('✅ Página de teste carregada com sucesso!');
            console.log('📊 Relatórios disponíveis para teste:');
            console.log('- relatorio_cursos.php');
            console.log('- relatorio_usuarios.php');
            console.log('- relatorio_financeiro.php');
            console.log('- relatorio_agendamentos.php');
        };
    </script>
</body>
</html>









