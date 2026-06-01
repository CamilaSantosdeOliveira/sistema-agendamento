<?php
// Sistema de Exportação de Dados
session_start();
include 'db.php';

$formato = $_GET['formato'] ?? 'csv';
$tabela = $_GET['tabela'] ?? 'usuarios';

// Função para exportar CSV
function exportarCSV($dados, $nome_arquivo) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nome_arquivo . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    if (!empty($dados)) {
        // Cabeçalhos
        fputcsv($output, array_keys($dados[0]));
        
        // Dados
        foreach ($dados as $linha) {
            fputcsv($output, $linha);
        }
    }
    
    fclose($output);
}

// Função para exportar PDF
function exportarPDF($dados, $nome_arquivo, $titulo) {
    require_once('tcpdf/tcpdf.php');
    
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
    $pdf->SetCreator('EduConnect');
    $pdf->SetAuthor('Sistema de Agendamento');
    $pdf->SetTitle($titulo);
    
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    
    // Título
    $pdf->Cell(0, 10, $titulo, 0, 1, 'C');
    $pdf->Ln(10);
    
    if (!empty($dados)) {
        // Cabeçalhos
        $headers = array_keys($dados[0]);
        foreach ($headers as $header) {
            $pdf->Cell(40, 7, $header, 1);
        }
        $pdf->Ln();
        
        // Dados
        foreach ($dados as $linha) {
            foreach ($linha as $valor) {
                $pdf->Cell(40, 6, $valor, 1);
            }
            $pdf->Ln();
        }
    }
    
    $pdf->Output($nome_arquivo . '.pdf', 'D');
}

// Buscar dados baseado na tabela
$dados = [];
$titulo = '';

switch ($tabela) {
    case 'usuarios':
        $sql = "SELECT id, nome, email, tipo_usuario, ativo, criado_em FROM usuarios ORDER BY nome";
        $titulo = 'Relatório de Usuários - EduConnect';
        break;
        
    case 'cursos':
        $sql = "SELECT id, nome, categoria, nivel, duracao_horas, preco, status FROM cursos ORDER BY nome";
        $titulo = 'Relatório de Cursos - EduConnect';
        break;
        
    case 'agendamentos':
        $sql = "SELECT a.id, u.nome as aluno, p.nome as professor, c.nome as curso, 
                       a.data_agendamento, a.hora_inicio, a.hora_fim, a.status
                FROM agendamentos a
                LEFT JOIN usuarios u ON a.aluno_id = u.id
                LEFT JOIN usuarios p ON a.professor_id = p.id
                LEFT JOIN cursos c ON a.curso_id = c.id
                ORDER BY a.data_agendamento DESC";
        $titulo = 'Relatório de Agendamentos - EduConnect';
        break;
        
    case 'certificados':
        $sql = "SELECT c.id, u.nome as aluno, cur.nome as curso, c.codigo_verificacao, 
                       c.data_emissao, c.status
                FROM certificados c
                LEFT JOIN usuarios u ON c.aluno_id = u.id
                LEFT JOIN cursos cur ON c.curso_id = cur.id
                ORDER BY c.data_emissao DESC";
        $titulo = 'Relatório de Certificados - EduConnect';
        break;
        
    default:
        $sql = "SELECT id, nome, email, tipo_usuario FROM usuarios ORDER BY nome";
        $titulo = 'Relatório Geral - EduConnect';
}

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dados[] = $row;
    }
}

// Exportar baseado no formato
if ($formato === 'csv') {
    exportarCSV($dados, 'relatorio_' . $tabela . '_' . date('Y-m-d_H-i-s'));
} elseif ($formato === 'pdf') {
    exportarPDF($dados, 'relatorio_' . $tabela . '_' . date('Y-m-d_H-i-s'), $titulo);
} else {
    // Página de seleção
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Exportar Dados - EduConnect</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                min-height: 100vh;
                padding: 20px;
            }

            .container {
                max-width: 800px;
                margin: 0 auto;
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                overflow: hidden;
            }

            .header {
                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                color: white;
                padding: 30px;
                text-align: center;
            }

            .header h1 {
                font-size: 2em;
                margin-bottom: 10px;
            }

            .content {
                padding: 30px;
            }

            .export-section {
                margin-bottom: 30px;
            }

            .export-section h3 {
                color: #374151;
                margin-bottom: 15px;
                font-size: 1.2em;
            }

            .export-options {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
                margin-bottom: 20px;
            }

            .export-card {
                background: #f8fafc;
                border: 2px solid #e5e7eb;
                border-radius: 12px;
                padding: 20px;
                text-align: center;
                transition: all 0.3s;
                cursor: pointer;
            }

            .export-card:hover {
                border-color: #3b82f6;
                transform: translateY(-2px);
            }

            .export-card.selected {
                border-color: #3b82f6;
                background: #eff6ff;
            }

            .export-icon {
                font-size: 2em;
                margin-bottom: 10px;
            }

            .export-title {
                font-weight: 600;
                color: #374151;
                margin-bottom: 5px;
            }

            .export-desc {
                font-size: 0.9em;
                color: #6b7280;
            }

            .data-section {
                margin-bottom: 30px;
            }

            .data-options {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 10px;
            }

            .data-option {
                background: #f8fafc;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 15px;
                text-align: center;
                cursor: pointer;
                transition: all 0.2s;
            }

            .data-option:hover {
                background: #eff6ff;
                border-color: #3b82f6;
            }

            .data-option.selected {
                background: #eff6ff;
                border-color: #3b82f6;
                color: #1d4ed8;
            }

            .btn {
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 500;
                font-size: 1em;
                transition: all 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-primary {
                background: #3b82f6;
                color: white;
            }

            .btn-primary:hover {
                background: #2563eb;
            }

            .btn-secondary {
                background: #6b7280;
                color: white;
            }

            .btn-secondary:hover {
                background: #4b5563;
            }

            .actions {
                text-align: center;
                margin-top: 30px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>📤 Exportar Dados</h1>
                <p>Exporte dados do sistema em diferentes formatos</p>
            </div>

            <div class="content">
                <div class="export-section">
                    <h3>📊 Formato de Exportação</h3>
                    <div class="export-options">
                        <div class="export-card" onclick="selecionarFormato('csv')">
                            <div class="export-icon">📊</div>
                            <div class="export-title">CSV</div>
                            <div class="export-desc">Planilha Excel</div>
                        </div>
                        <div class="export-card" onclick="selecionarFormato('pdf')">
                            <div class="export-icon">📄</div>
                            <div class="export-title">PDF</div>
                            <div class="export-desc">Documento PDF</div>
                        </div>
                    </div>
                </div>

                <div class="data-section">
                    <h3>📋 Dados para Exportar</h3>
                    <div class="data-options">
                        <div class="data-option" onclick="selecionarDados('usuarios')">
                            👥 Usuários
                        </div>
                        <div class="data-option" onclick="selecionarDados('cursos')">
                            📚 Cursos
                        </div>
                        <div class="data-option" onclick="selecionarDados('agendamentos')">
                            📅 Agendamentos
                        </div>
                        <div class="data-option" onclick="selecionarDados('certificados')">
                            🏆 Certificados
                        </div>
                    </div>
                </div>

                <div class="actions">
                    <button class="btn btn-primary" onclick="exportarDados()">
                        📤 Exportar Dados
                    </button>
                    <button class="btn btn-secondary" onclick="window.history.back()">
                        ← Voltar
                    </button>
                </div>
            </div>
        </div>

        <script>
            let formatoSelecionado = '';
            let dadosSelecionados = '';

            function selecionarFormato(formato) {
                formatoSelecionado = formato;
                
                // Remover seleção anterior
                document.querySelectorAll('.export-card').forEach(card => {
                    card.classList.remove('selected');
                });
                
                // Selecionar novo
                event.target.closest('.export-card').classList.add('selected');
            }

            function selecionarDados(dados) {
                dadosSelecionados = dados;
                
                // Remover seleção anterior
                document.querySelectorAll('.data-option').forEach(option => {
                    option.classList.remove('selected');
                });
                
                // Selecionar novo
                event.target.classList.add('selected');
            }

            function exportarDados() {
                if (!formatoSelecionado) {
                    alert('Selecione um formato de exportação!');
                    return;
                }
                
                if (!dadosSelecionados) {
                    alert('Selecione os dados para exportar!');
                    return;
                }
                
                const url = `exportar_dados.php?formato=${formatoSelecionado}&tabela=${dadosSelecionados}`;
                window.location.href = url;
            }
        </script>
    </body>
    </html>
    <?php
}
?>







