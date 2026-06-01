<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir arquivo de conexão
include '../db.php';

// Verificar se a conexão foi estabelecida
if (!$conn) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro de conexão com banco de dados'
    ]);
    exit;
}

// Função para gerar código de verificação único
function gerarCodigoVerificacao() {
    return 'CERT-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . date('Y');
}

// Função para criar PDF do certificado
function criarPDFCertificado($dados) {
    // Simulação de criação de PDF
    $html = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 40px; }
            .header { color: #3b82f6; font-size: 24px; margin-bottom: 20px; }
            .title { font-size: 18px; margin: 30px 0; }
            .name { font-size: 22px; font-weight: bold; color: #1e293b; margin: 20px 0; }
            .course { color: #3b82f6; font-size: 20px; margin: 15px 0; }
            .footer { margin-top: 40px; font-size: 12px; color: #64748b; }
        </style>
    </head>
    <body>
        <div class='header'>🎓 EduConnect</div>
        <div class='title'>Certificado de Conclusão</div>
        <p>Certificamos que</p>
        <div class='name'>{$dados['aluno_nome']}</div>
        <p>concluiu com êxito o curso</p>
        <div class='course'>{$dados['curso_nome']}</div>
        <p>com carga horária de {$dados['carga_horaria']} horas</p>
        <p>em {$dados['data_conclusao']}</p>
        <div class='footer'>
            Código de Validação: {$dados['codigo_verificacao']}
        </div>
    </body>
    </html>";
    
    return $html;
}

// Processar requisições
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Se for POST, tentar pegar o action do body
if ($method === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $action = $action ?: ($data['action'] ?? '');
}

try {
    switch ($action) {
        case 'listar_certificados':
            $sql = "SELECT c.id, c.codigo_verificacao, c.data_emissao, c.data_conclusao, c.status, c.carga_horaria,
                           u.nome as aluno_nome,
                           cur.nome as curso_nome
                    FROM certificados c
                    JOIN usuarios u ON c.aluno_id = u.id
                    JOIN cursos cur ON c.curso_id = cur.id
                    ORDER BY c.data_emissao DESC";
            
            $result = $conn->query($sql);
            $certificados = [];
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $certificados[] = $row;
                }
            }
            
            echo json_encode([
                'success' => true,
                'data' => $certificados,
                'count' => count($certificados)
            ]);
            break;
            
        case 'emitir_certificado':
            if ($method === 'POST') {
                // Buscar alunos e cursos disponíveis
                $alunos = $conn->query("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' LIMIT 3");
                $cursos = $conn->query("SELECT id, nome, carga_horaria FROM cursos WHERE status = 'ativo' LIMIT 3");
                
                $certificados_criados = 0;
                
                if ($alunos && $cursos && $alunos->num_rows > 0 && $cursos->num_rows > 0) {
                    $alunos_array = [];
                    $cursos_array = [];
                    
                    while ($aluno = $alunos->fetch_assoc()) {
                        $alunos_array[] = $aluno;
                    }
                    
                    while ($curso = $cursos->fetch_assoc()) {
                        $cursos_array[] = $curso;
                    }
                    
                    // Criar certificados
                    for ($i = 0; $i < min(3, count($alunos_array), count($cursos_array)); $i++) {
                        $aluno = $alunos_array[$i];
                        $curso = $cursos_array[$i];
                        
                        $codigo = gerarCodigoVerificacao();
                        $data_emissao = date('Y-m-d');
                        $data_conclusao = date('Y-m-d', strtotime('-1 day'));
                        
                        $sql = "INSERT INTO certificados (aluno_id, curso_id, codigo_verificacao, data_emissao, data_conclusao, status, carga_horaria, observacoes) 
                                VALUES (?, ?, ?, ?, ?, 'emitido', ?, ?)";
                        
                        $stmt = $conn->prepare($sql);
                        $observacoes = "Certificado emitido automaticamente.";
                        $stmt->bind_param("iisssss", 
                            $aluno['id'], 
                            $curso['id'], 
                            $codigo, 
                            $data_emissao, 
                            $data_conclusao, 
                            $curso['carga_horaria'],
                            $observacoes
                        );
                        
                        if ($stmt->execute()) {
                            $certificados_criados++;
                        }
                    }
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => "{$certificados_criados} certificados emitidos com sucesso!",
                    'count' => $certificados_criados
                ]);
            }
            break;
            
        case 'ver_certificado':
            if ($method === 'GET') {
                $id = $_GET['id'] ?? 0;
                
                $sql = "SELECT c.id, c.codigo_verificacao, c.data_emissao, c.data_conclusao, c.status, c.carga_horaria,
                               u.nome as aluno_nome, u.email as aluno_email,
                               cur.nome as curso_nome
                        FROM certificados c
                        JOIN usuarios u ON c.aluno_id = u.id
                        JOIN cursos cur ON c.curso_id = cur.id
                        WHERE c.id = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($certificado = $result->fetch_assoc()) {
                    echo json_encode([
                        'success' => true,
                        'data' => $certificado
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Certificado não encontrado'
                    ]);
                }
            }
            break;
            
        case 'baixar_certificado':
            if ($method === 'GET') {
                $id = $_GET['id'] ?? 0;
                
                $sql = "SELECT c.id, c.codigo_verificacao, c.data_emissao, c.data_conclusao, c.carga_horaria,
                               u.nome as aluno_nome,
                               cur.nome as curso_nome
                        FROM certificados c
                        JOIN usuarios u ON c.aluno_id = u.id
                        JOIN cursos cur ON c.curso_id = cur.id
                        WHERE c.id = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($certificado = $result->fetch_assoc()) {
                    // Gerar PDF
                    $pdf_content = criarPDFCertificado($certificado);
                    
                    // Definir headers para download
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: attachment; filename="certificado_' . $id . '.pdf"');
                    header('Content-Length: ' . strlen($pdf_content));
                    
                    echo $pdf_content;
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Certificado não encontrado'
                    ]);
                }
            }
            break;
            
        case 'validar_certificado':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id'] ?? 0;
                
                $sql = "UPDATE certificados SET status = 'validado' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Certificado validado com sucesso!'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erro ao validar certificado'
                    ]);
                }
            }
            break;
            
        case 'revogar_certificado':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id'] ?? 0;
                
                $sql = "UPDATE certificados SET status = 'revogado' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Certificado revogado com sucesso!'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erro ao revogar certificado'
                    ]);
                }
            }
            break;
            
        case 'test_connection':
            echo json_encode([
                'success' => true,
                'message' => 'API de certificados funcionando corretamente',
                'timestamp' => date('Y-m-d H:i:s'),
                'database' => 'Conectado'
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Ação não reconhecida: ' . $action,
                'available_actions' => ['listar_certificados', 'emitir_certificado', 'ver_certificado', 'baixar_certificado', 'validar_certificado', 'revogar_certificado', 'test_connection']
            ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno: ' . $e->getMessage(),
        'error_details' => $e->getTraceAsString()
    ]);
}

$conn->close();
?>







