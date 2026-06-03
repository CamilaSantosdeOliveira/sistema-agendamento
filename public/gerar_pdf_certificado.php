<?php
// Gerar PDF do certificado
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
    
    // Configurar cabeçalhos para HTML (que será convertido para PDF pelo navegador)
    header('Content-Type: text/html; charset=UTF-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Criar HTML do certificado
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Certificado - ' . $certificado['aluno_nome'] . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 40px;
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                min-height: 100vh;
            }
            .certificado {
                background: white;
                border: 3px solid #3b82f6;
                border-radius: 15px;
                padding: 60px;
                text-align: center;
                max-width: 800px;
                margin: 0 auto;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            }
            .header {
                margin-bottom: 40px;
            }
            .header h1 {
                color: #3b82f6;
                font-size: 2.5em;
                margin: 0;
                font-weight: bold;
            }
            .header p {
                color: #64748b;
                font-size: 1.2em;
                margin: 10px 0 0 0;
            }
            .content {
                margin: 40px 0;
            }
            .content p {
                font-size: 1.1em;
                color: #334155;
                margin: 15px 0;
            }
            .aluno-nome {
                font-size: 2em;
                font-weight: bold;
                color: #1e293b;
                margin: 20px 0;
            }
            .curso-nome {
                font-size: 1.5em;
                font-weight: bold;
                color: #3b82f6;
                margin: 20px 0;
            }
            .footer {
                margin-top: 40px;
                padding-top: 30px;
                border-top: 2px solid #e2e8f0;
            }
            .codigo {
                font-family: monospace;
                font-size: 1.1em;
                color: #64748b;
                background: #f1f5f9;
                padding: 10px;
                border-radius: 5px;
                display: inline-block;
            }
            .data {
                color: #64748b;
                font-size: 1em;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="certificado">
            <div class="header">
                <h1>🎓 EduConnect</h1>
                <p>Sistema Educacional Profissional</p>
            </div>
            
            <div class="content">
                <p>Certificamos que</p>
                <div class="aluno-nome">' . htmlspecialchars($certificado['aluno_nome']) . '</div>
                <p>concluiu com êxito o curso</p>
                <div class="curso-nome">' . htmlspecialchars($certificado['curso_nome']) . '</div>
                <p>com carga horária de ' . $certificado['carga_horaria'] . ' horas</p>
                <p>em ' . date('d/m/Y', strtotime($certificado['data_conclusao'])) . '</p>
            </div>
            
            <div class="footer">
                <p><strong>Código de Validação:</strong></p>
                <div class="codigo">' . $certificado['codigo_verificacao'] . '</div>
                <div class="data">
                    Emitido em: ' . date('d/m/Y', strtotime($certificado['data_emissao'])) . '
                </div>
            </div>
        </div>
        
        <script>
            // Forçar download automático
            window.onload = function() {
                // Aguardar um pouco para o conteúdo carregar
                setTimeout(function() {
                    // Tentar imprimir como PDF
                    window.print();
                    
                    // Fechar a aba após um tempo
                    setTimeout(function() {
                        window.close();
                    }, 2000);
                }, 500);
            };
        </script>
    </body>
    </html>
    ';
    
    // Para simplicidade, vamos retornar o HTML como PDF
    // Em produção, você usaria uma biblioteca como TCPDF, FPDF ou DOMPDF
    echo $html;
    
} catch (Exception $e) {
    die('Erro ao gerar certificado: ' . $e->getMessage());
}
?>


