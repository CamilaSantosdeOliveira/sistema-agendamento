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

// Função para gerar código de validação único
function gerarCodigoValidacao() {
    return 'CERT-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

// Função para criar PDF do certificado
function criarPDFCertificado($dados) {
    // Simulação de criação de PDF
    // Em produção, você usaria uma biblioteca como TCPDF ou FPDF
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
            Código de Validação: {$dados['codigo_validacao']}
        </div>
    </body>
    </html>";
    
    return $html;
}

// Processar requisições
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Debug: Log dos dados recebidos
error_log("API Certificados - Method: $method, Action: $action");
if ($method === 'POST') {
    $input = file_get_contents('php://input');
    error_log("API Certificados - Raw input: $input");
    $post_data = json_decode($input, true);
    error_log("API Certificados - Decoded data: " . print_r($post_data, true));
    if ($post_data && isset($post_data['action'])) {
        $action = $post_data['action'];
        error_log("API Certificados - Action from POST data: $action");
    }
}

try {
    switch ($action) {
        case 'emitir_certificado':
            if ($method === 'POST') {
                // Buscar alunos que podem receber certificados
                $sql = "SELECT u.id, u.nome, u.email, 
                               COUNT(a.id) as aulas_concluidas,
                               c.nome as curso_nome,
                               c.duracao_horas
                        FROM usuarios u
                        LEFT JOIN agendamentos a ON u.id = a.aluno_id AND a.status = 'concluido'
                        LEFT JOIN cursos c ON a.curso_id = c.id
                        WHERE u.tipo_usuario = 'aluno' 
                        AND u.ativo = 1
                        GROUP BY u.id
                        HAVING aulas_concluidas >= 5";
                
                $result = $conn->query($sql);
                $certificados_emitidos = 0;
                
                while ($aluno = $result->fetch_assoc()) {
                    $codigo = gerarCodigoValidacao();
                    
                    // Inserir certificado
                    $stmt = $conn->prepare("INSERT INTO certificados (aluno_id, curso_id, codigo_validacao, data_emissao, status) VALUES (?, ?, ?, NOW(), 'emitido')");
                    $stmt->bind_param('iis', $aluno['id'], $aluno['curso_id'], $codigo);
                    
                    if ($stmt->execute()) {
                        $certificados_emitidos++;
                    }
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => "{$certificados_emitidos} certificados emitidos com sucesso!",
                    'count' => $certificados_emitidos
                ]);
            }
            break;
            
        case 'emitir_certificado_individual':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $aluno_id = $data['aluno_id'] ?? 0;
                $curso_id = $data['curso_id'] ?? 0;
                $data_conclusao = $data['data_conclusao'] ?? date('Y-m-d');
                
                // Verificar se o aluno existe e é do tipo 'aluno'
                $stmt = $conn->prepare("SELECT id, nome, email FROM usuarios WHERE id = ? AND tipo_usuario = 'aluno'");
                $stmt->bind_param('i', $aluno_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Aluno não encontrado'
                    ]);
                    break;
                }
                
                $aluno = $result->fetch_assoc();
                
                // Verificar se o curso existe
                $stmt = $conn->prepare("SELECT id, nome FROM cursos WHERE id = ? AND status = 'ativo'");
                $stmt->bind_param('i', $curso_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Curso não encontrado'
                    ]);
                    break;
                }
                
                $curso = $result->fetch_assoc();
                
                // Gerar código de verificação único
                $codigo = gerarCodigoValidacao();
                
                // Inserir certificado
                $stmt = $conn->prepare("INSERT INTO certificados (aluno_id, curso_id, codigo_verificacao, data_emissao, data_conclusao, status, carga_horaria, observacoes) VALUES (?, ?, ?, NOW(), ?, 'emitido', 40, 'Certificado emitido automaticamente')");
                $stmt->bind_param('iiss', $aluno_id, $curso_id, $codigo, $data_conclusao);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => "Certificado emitido com sucesso para {$aluno['nome']} no curso {$curso['nome']}",
                        'certificado_id' => $conn->insert_id,
                        'codigo' => $codigo
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erro ao inserir certificado: ' . $stmt->error
                    ]);
                }
            }
            break;
            
        case 'ver_certificado':
            if ($method === 'GET') {
                $id = $_GET['id'] ?? 0;
                
                $sql = "SELECT c.id, c.codigo_verificacao, c.data_emissao, c.status,
                               u.nome as aluno_nome, u.email as aluno_email,
                               cur.nome as curso_nome, cur.duracao_horas as carga_horaria
                        FROM certificados c
                        JOIN usuarios u ON c.aluno_id = u.id
                        JOIN cursos cur ON c.curso_id = cur.id
                        WHERE c.id = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($certificado = $result->fetch_assoc()) {
                    $certificado['data_conclusao'] = date('d/m/Y', strtotime($certificado['data_emissao']));
                    
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
                
                $sql = "SELECT c.id, c.codigo_verificacao, c.data_emissao,
                               u.nome as aluno_nome,
                               cur.nome as curso_nome, cur.duracao_horas as carga_horaria
                        FROM certificados c
                        JOIN usuarios u ON c.aluno_id = u.id
                        JOIN cursos cur ON c.curso_id = cur.id
                        WHERE c.id = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($certificado = $result->fetch_assoc()) {
                    $certificado['data_conclusao'] = date('d/m/Y', strtotime($certificado['data_emissao']));
                    
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
                $id = $data['certificado_id'] ?? $data['id'] ?? 0;
                
                error_log("API - Validando certificado ID: $id");
                
                $sql = "UPDATE certificados SET status = 'validado' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);
                
                if ($stmt->execute()) {
                    error_log("API - Certificado $id validado com sucesso");
                    echo json_encode([
                        'success' => true,
                        'message' => 'Certificado validado com sucesso!'
                    ]);
                } else {
                    error_log("API - Erro ao validar certificado $id: " . $stmt->error);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erro ao validar certificado: ' . $stmt->error
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Método não permitido'
                ]);
            }
            break;
            
        case 'revogar_certificado':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['certificado_id'] ?? $data['id'] ?? 0;
                
                error_log("API - Revogando certificado ID: $id");
                
                $sql = "UPDATE certificados SET status = 'revogado' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);
                
                if ($stmt->execute()) {
                    error_log("API - Certificado $id revogado com sucesso");
                    echo json_encode([
                        'success' => true,
                        'message' => 'Certificado revogado com sucesso!'
                    ]);
                } else {
                    error_log("API - Erro ao revogar certificado $id: " . $stmt->error);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erro ao revogar certificado: ' . $stmt->error
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Método não permitido'
                ]);
            }
            break;
            
        case 'desrevogar_certificado':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['certificado_id'] ?? $data['id'] ?? 0;
                
                error_log("API - Reativando certificado ID: $id");
                
                $sql = "UPDATE certificados SET status = 'emitido' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);
                
                if ($stmt->execute()) {
                    error_log("API - Certificado $id reativado com sucesso");
                    echo json_encode([
                        'success' => true,
                        'message' => 'Certificado reativado com sucesso!'
                    ]);
                } else {
                    error_log("API - Erro ao reativar certificado $id: " . $stmt->error);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erro ao reativar certificado: ' . $stmt->error
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Método não permitido'
                ]);
            }
            break;
            
        case 'listar_certificados':
            $sql = "SELECT c.id, c.codigo_verificacao, c.data_emissao, c.status,
                           u.nome as aluno_nome, u.email as aluno_email,
                           cur.nome as curso_nome, cur.duracao_horas as carga_horaria
                    FROM certificados c
                    JOIN usuarios u ON c.aluno_id = u.id
                    JOIN cursos cur ON c.curso_id = cur.id
                    ORDER BY c.data_emissao DESC";
            
            $result = $conn->query($sql);
            $certificados = [];
            
            while ($row = $result->fetch_assoc()) {
                $certificados[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $certificados
            ]);
            break;
            
        case 'test_connection':
            echo json_encode([
                'success' => true,
                'message' => 'API de certificados funcionando corretamente',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;
            
        default:
            // Se não há ação especificada, listar certificados por padrão
            $sql = "SELECT c.id, c.codigo_verificacao, c.data_emissao, c.status,
                           u.nome as aluno_nome, u.email as aluno_email,
                           cur.nome as curso_nome, cur.duracao_horas as carga_horaria
                    FROM certificados c
                    JOIN usuarios u ON c.aluno_id = u.id
                    JOIN cursos cur ON c.curso_id = cur.id
                    ORDER BY c.data_emissao DESC";
            
            $result = $conn->query($sql);
            $certificados = [];
            
            while ($row = $result->fetch_assoc()) {
                $certificados[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $certificados
            ]);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno: ' . $e->getMessage()
    ]);
}

$conn->close();
?>

