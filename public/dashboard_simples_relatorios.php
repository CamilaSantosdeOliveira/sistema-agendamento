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
    <title>Dashboard Simples - Relatórios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8fafc;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header h1 {
            color: #1e293b;
            margin-bottom: 10px;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .quick-action-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 4px solid #3b82f6;
        }
        
        .quick-action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .quick-action-card.warning {
            border-left-color: #f59e0b;
        }
        
        .quick-action-card.info {
            border-left-color: #3b82f6;
        }
        
        .quick-action-card.success {
            border-left-color: #10b981;
        }
        
        .quick-action-card.danger {
            border-left-color: #ef4444;
        }
        
        .quick-action-icon {
            font-size: 2rem;
            margin-bottom: 16px;
            color: #3b82f6;
        }
        
        .quick-action-card.warning .quick-action-icon {
            color: #f59e0b;
        }
        
        .quick-action-card.info .quick-action-icon {
            color: #3b82f6;
        }
        
        .quick-action-card.success .quick-action-icon {
            color: #10b981;
        }
        
        .quick-action-card.danger .quick-action-icon {
            color: #ef4444;
        }
        
        .quick-action-content h3 {
            margin: 0 0 8px 0;
            color: #1e293b;
            font-size: 1.2rem;
        }
        
        .quick-action-content p {
            margin: 0 0 16px 0;
            color: #64748b;
        }
        
        .quick-action-stats {
            display: flex;
            gap: 12px;
        }
        
        .stat-item {
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.85rem;
            color: #475569;
        }
        
        .status {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            background: #d1fae5;
            color: #065f46;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Dashboard Simples - Relatórios</h1>
            <p>Teste dos botões dos relatórios</p>
        </div>
        
        <div class="status">
            ✅ <strong>Arquivos dos relatórios confirmados:</strong> relatorio_cursos.php, relatorio_usuarios.php, relatorio_financeiro.php, relatorio_agendamentos.php
        </div>
        
        <div class="quick-actions">
            <div class="quick-action-card warning" onclick="showRelatorioCursos()">
                <div class="quick-action-icon">
                    📊
                </div>
                <div class="quick-action-content">
                    <h3>📊 Relatório de Cursos</h3>
                    <p>Análise completa dos cursos</p>
                    <div class="quick-action-stats">
                        <span class="stat-item">8 cursos ativos</span>
                    </div>
                </div>
            </div>
            
            <div class="quick-action-card info" onclick="showRelatorioUsuarios()">
                <div class="quick-action-icon">
                    👥
                </div>
                <div class="quick-action-content">
                    <h3>👥 Relatório de Usuários</h3>
                    <p>Estatísticas dos usuários</p>
                    <div class="quick-action-stats">
                        <span class="stat-item">19 usuários</span>
                    </div>
                </div>
            </div>
            
            <div class="quick-action-card success" onclick="showRelatorioFinanceiro()">
                <div class="quick-action-icon">
                    💰
                </div>
                <div class="quick-action-content">
                    <h3>💰 Relatório Financeiro</h3>
                    <p>Análise financeira</p>
                    <div class="quick-action-stats">
                        <span class="stat-item">R$ 1.199,60</span>
                    </div>
                </div>
            </div>
            
            <div class="quick-action-card danger" onclick="showRelatorioAgendamentos()">
                <div class="quick-action-icon">
                    📅
                </div>
                <div class="quick-action-content">
                    <h3>📅 Relatório de Agendamentos</h3>
                    <p>Análise de aulas</p>
                    <div class="quick-action-stats">
                        <span class="stat-item">1 agendamento</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <button onclick="voltarDashboard()" style="background: #3b82f6; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px;">
                🏠 Voltar ao Dashboard Completo
            </button>
        </div>
    </div>
    
    <script>
        function showRelatorioCursos() {
            console.log('🔍 Abrindo Relatório de Cursos...');
            window.location.href = 'relatorio_cursos.php';
        }
        
        function showRelatorioUsuarios() {
            console.log('🔍 Abrindo Relatório de Usuários...');
            window.location.href = 'relatorio_usuarios.php';
        }
        
        function showRelatorioFinanceiro() {
            console.log('🔍 Abrindo Relatório Financeiro...');
            window.location.href = 'relatorio_financeiro.php';
        }
        
        function showRelatorioAgendamentos() {
            console.log('🔍 Abrindo Relatório de Agendamentos...');
            window.location.href = 'relatorio_agendamentos.php';
        }
        
        function voltarDashboard() {
            window.location.href = 'dashboard_final.php';
        }
        
        // Teste automático
        window.onload = function() {
            console.log('✅ Dashboard simples carregado!');
            console.log('🔍 Funções dos relatórios disponíveis:');
            console.log('- showRelatorioCursos()');
            console.log('- showRelatorioUsuarios()');
            console.log('- showRelatorioFinanceiro()');
            console.log('- showRelatorioAgendamentos()');
        };
    </script>
</body>
</html>









