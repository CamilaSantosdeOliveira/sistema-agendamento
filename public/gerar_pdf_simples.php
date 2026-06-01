<?php
// Gerar PDF real do certificado
require_once 'db.php';

// Verificar se o ID do certificado foi fornecido
$certificado_id = intval($_GET['id'] ?? 0);

if (!$certificado_id) {
    die('ID do certificado não fornecido');
}

try {
    // Buscar dados do certificado
    $sql = "
        SELECT 
            c.id,
            c.codigo_verificacao,
            c.data_emissao,
            c.status,
            c.data_conclusao,
            c.carga_horaria,
            u.nome as aluno_nome,
            u.email as aluno_email,
            cur.nome as curso_nome,
            cur.descricao as curso_descricao
        FROM certificados c
        INNER JOIN usuarios u ON c.aluno_id = u.id
        INNER JOIN cursos cur ON c.curso_id = cur.id
        WHERE c.id = ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $certificado_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die('Certificado não encontrado');
    }
    
    $certificado = $result->fetch_assoc();
    
    // Configurar cabeçalhos para HTML (não PDF)
    header('Content-Type: text/html; charset=UTF-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Criar HTML que será convertido para PDF
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Certificado - ' . htmlspecialchars($certificado['aluno_nome']) . '</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap");

            @page {
                margin: 0;
                size: A4 landscape;
            }

            * {
                box-sizing: border-box;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                margin: 0;
                padding: 0;
                background: #e2e8f0;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                font-family: "Montserrat", sans-serif;
            }

            .instrucoes {
                position: fixed;
                top: 20px;
                right: 20px;
                background: white;
                padding: 24px;
                border-radius: 16px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                z-index: 1000;
                width: 320px;
                border: 1px solid #e2e8f0;
            }

            .instrucoes h3 {
                margin-top: 0;
                color: #0f172a;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .instrucoes h3 i { color: #3b82f6; }

            .instrucoes p {
                color: #475569;
                font-size: 0.9em;
                margin-bottom: 8px;
            }

            .instrucoes .btn {
                background: linear-gradient(135deg, #2563eb, #3b82f6);
                color: white;
                border: none;
                padding: 12px 20px;
                border-radius: 8px;
                cursor: pointer;
                margin-top: 15px;
                font-size: 15px;
                font-weight: 600;
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                box-shadow: 0 4px 12px rgba(37,99,235,0.2);
            }

            .instrucoes .btn:hover { background: linear-gradient(135deg, #1d4ed8, #2563eb); }

            /* Certificado A4 Landscape */
            .certificado-container {
                width: 297mm;
                height: 210mm;
                background: white;
                position: relative;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                overflow: hidden;
            }

            /* Fundo Elegante */
            .certificado-bg {
                position: absolute;
                inset: 0;
                background: 
                    radial-gradient(circle at 0% 0%, rgba(212, 175, 55, 0.05) 0%, transparent 50%),
                    radial-gradient(circle at 100% 100%, rgba(37, 99, 235, 0.05) 0%, transparent 50%);
                z-index: 0;
            }

            /* Bordas e Cantos */
            .border-outer {
                position: absolute;
                inset: 10mm;
                border: 1px solid #d4af37;
                z-index: 1;
            }

            .border-inner {
                position: absolute;
                inset: 12mm;
                border: 3px double #d4af37;
                padding: 10mm;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                z-index: 2;
                background: rgba(255,255,255,0.95);
            }

            /* Corner Ornaments */
            .corner {
                position: absolute;
                width: 40px;
                height: 40px;
                border: 4px solid #d4af37;
            }
            .corner-tl { top: -2px; left: -2px; border-right: none; border-bottom: none; }
            .corner-tr { top: -2px; right: -2px; border-left: none; border-bottom: none; }
            .corner-bl { bottom: -2px; left: -2px; border-right: none; border-top: none; }
            .corner-br { bottom: -2px; right: -2px; border-left: none; border-top: none; }

            /* Marca d\'água */
            .watermark {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 300px;
                color: rgba(212, 175, 55, 0.03);
                z-index: 0;
            }

            /* Conteúdo */
            .content-wrapper {
                z-index: 3;
                position: relative;
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .header-logo {
                font-family: "Playfair Display", serif;
                font-size: 2em;
                color: #1e293b;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .header-logo i { color: #d4af37; font-size: 1.2em; }

            .title {
                font-family: "Playfair Display", serif;
                font-size: 3.8em;
                color: #d4af37;
                letter-spacing: 5px;
                margin: 0 0 10px 0;
                text-transform: uppercase;
                font-weight: 700;
            }

            .subtitle {
                font-size: 1.1em;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 3px;
                margin-bottom: 35px;
            }

            .certify-text {
                font-size: 1.2em;
                color: #475569;
                margin-bottom: 15px;
                font-style: italic;
            }

            .student-name {
                font-family: "Playfair Display", serif;
                font-size: 3.5em;
                color: #0f172a;
                font-weight: 700;
                margin-bottom: 15px;
                border-bottom: 2px solid #e2e8f0;
                padding-bottom: 5px;
                display: inline-block;
                min-width: 60%;
                text-align: center;
            }

            .course-text {
                font-size: 1.2em;
                color: #475569;
                margin-bottom: 15px;
            }

            .course-name {
                font-size: 2.2em;
                font-weight: 700;
                color: #1e293b;
                margin-bottom: 15px;
            }

            .details {
                font-size: 1.1em;
                color: #64748b;
                margin-bottom: 40px;
                display: flex;
                gap: 20px;
                justify-content: center;
            }

            /* Selo e Assinaturas */
            .footer-row {
                display: flex;
                justify-content: space-between;
                align-items: flex-end;
                width: 90%;
                margin-top: 20px;
                position: relative;
            }

            .signature-block {
                text-align: center;
                width: 220px;
            }
            .signature-line {
                border-bottom: 1px solid #334155;
                margin-bottom: 8px;
                height: 40px;
                background-image: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 200 40\'%3E%3Cpath d=\'M 10 30 Q 30 10 50 30 T 90 30 T 130 30 T 170 30\' fill=\'none\' stroke=\'rgba(15,23,42,0.5)\' stroke-width=\'2\'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: center bottom 2px;
                background-size: 80%;
                opacity: 0.6;
            }
            .signature-name {
                font-weight: 700;
                color: #1e293b;
                font-size: 1.1em;
            }
            .signature-title {
                color: #64748b;
                font-size: 0.85em;
            }

            .seal-container {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                bottom: 0;
            }
            .seal {
                width: 110px;
                height: 110px;
                background: linear-gradient(135deg, #d4af37, #fde047);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 45px;
                box-shadow: 0 10px 25px rgba(212, 175, 55, 0.4);
                border: 4px dashed white;
                outline: 2px solid #d4af37;
                outline-offset: -8px;
            }

            .validation-info {
                position: absolute;
                bottom: -8mm;
                right: 0;
                text-align: right;
                font-size: 0.7em;
                color: #94a3b8;
            }
            .validation-code {
                font-family: monospace;
                font-weight: bold;
                color: #475569;
            }

            @media print {
                @page {
                    size: A4 landscape;
                    margin: 0;
                }
                html, body {
                    width: 297mm !important;
                    height: 210mm !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    display: block !important;
                    background: white !important;
                    overflow: hidden !important;
                }
                .instrucoes { display: none !important; }
                .certificado-container { 
                    box-shadow: none !important; 
                    border: none !important; 
                    width: 100% !important;
                    height: 100% !important;
                    position: relative !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    page-break-inside: avoid !important;
                    transform: none !important;
                }
            }
        </style>
    </head>
    <body>
        <div class="instrucoes">
            <h3><i class="fas fa-print"></i> Impressão de Certificado</h3>
            <p>1. Clique no botão "Imprimir/Salvar PDF" abaixo.</p>
            <p>2. Na tela de impressão do navegador, mude o destino para <strong>"Salvar como PDF"</strong>.</p>
            <p>3. Certifique-se de que a opção <strong>"Gráficos de plano de fundo"</strong> (Background graphics) esteja marcada nas configurações.</p>
            <p style="color: #ea580c; font-weight: bold; background: #fff7ed; padding: 6px; border-radius: 4px; border: 1px solid #fdba74;">⚠️ ATENÇÃO: No menu de impressão, você DEVE mudar o Layout para "Paisagem" (Landscape)!</p>
            <button class="btn" onclick="imprimirCertificado()"><i class="fas fa-file-pdf"></i> Imprimir/Salvar PDF</button>
        </div>
        
        <div class="certificado-container">
            <div class="certificado-bg"></div>
            <div class="border-outer"></div>
            <div class="border-inner">
                <div class="corner corner-tl"></div>
                <div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div>
                <div class="corner corner-br"></div>
                
                <i class="fas fa-award watermark"></i>
                
                <div class="content-wrapper">
                    <div class="header-logo">
                        <i class="fas fa-graduation-cap"></i> EduConnect
                    </div>
                    
                    <h1 class="title">Certificado</h1>
                    <div class="subtitle">de Conclusão</div>
                    
                    <div class="certify-text">O Sistema Educacional Profissional certifica que</div>
                    
                    <div class="student-name">' . htmlspecialchars($certificado['aluno_nome']) . '</div>
                    
                    <div class="course-text">concluiu com êxito o curso de</div>
                    
                    <div class="course-name">' . htmlspecialchars($certificado['curso_nome']) . '</div>
                    
                    <div class="details">
                        <span><strong>Carga Horária:</strong> ' . $certificado['carga_horaria'] . ' horas</span>
                        <span>&bull;</span>
                        <span><strong>Concluído em:</strong> ' . date('d/m/Y', strtotime($certificado['data_conclusao'])) . '</span>
                    </div>
                    
                    <div class="footer-row">
                        <div class="signature-block">
                            <div class="signature-line"></div>
                            <div class="signature-name">Dr. Carlos Eduardo</div>
                            <div class="signature-title">Diretor Acadêmico</div>
                        </div>
                        
                        <div class="seal-container">
                            <div class="seal"><i class="fas fa-star"></i></div>
                        </div>
                        
                        <div class="signature-block">
                            <div class="signature-line"></div>
                            <div class="signature-name">Marina Silva</div>
                            <div class="signature-title">Coordenadora Pedagógica</div>
                        </div>
                    </div>
                </div>
                
                <div class="validation-info">
                    Código de Validação: <span class="validation-code">' . htmlspecialchars($certificado['codigo_verificacao']) . '</span><br>
                    Data de Emissão: ' . date('d/m/Y', strtotime($certificado['data_emissao'])) . '
                </div>
            </div>
        </div>
        
        <script>
            function imprimirCertificado() {
                window.print();
            }
        </script>
    </body>
    </html>
    ';
    
    // Para simplicidade, vamos usar uma abordagem diferente
    // Vamos criar um arquivo HTML temporário e usar wkhtmltopdf se disponível
    // Ou usar uma biblioteca online
    
    // Por enquanto, vamos retornar o HTML com instruções para imprimir
    echo $html;
    
} catch (Exception $e) {
    die('Erro ao gerar certificado: ' . $e->getMessage());
}
?>
