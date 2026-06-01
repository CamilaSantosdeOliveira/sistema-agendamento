<?php
// Configurar tratamento de erros - suprimir warnings
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Capturar erros e exceções
function handleError($errno, $errstr, $errfile, $errline) {
    errorResponse("Erro PHP: $errstr em $errfile:$errline", 500);
}

function handleException($exception) {
    errorResponse("Exceção: " . $exception->getMessage(), 500);
}

set_error_handler('handleError');
set_exception_handler('handleException');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Tratar requisições OPTIONS (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir conexão com banco
require_once __DIR__ . '/../db.php';

// Verificar se a conexão foi estabelecida
if (!isset($conn) || $conn->connect_error) {
    errorResponse('Erro de conexão com o banco de dados', 500);
}

// Função para retornar resposta de erro
function errorResponse($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit();
}

// Função para retornar resposta de sucesso
function successResponse($data, $message = 'Operação realizada com sucesso') {
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Função para validar dados do agendamento
function validateAgendamento($data) {
    $errors = [];
    
    if (empty($data['data'])) {
        $errors[] = 'Data da aula é obrigatória';
    }
    
    if (empty($data['hora_inicio'])) {
        $errors[] = 'Hora de início é obrigatória';
    }
    
    if (empty($data['professor_id'])) {
        $errors[] = 'Professor é obrigatório';
    }
    
    if (empty($data['curso_id'])) {
        $errors[] = 'Curso é obrigatório';
    }
    
    if (empty($data['aluno_id'])) {
        $errors[] = 'Aluno é obrigatório';
    }
    
    if (!empty($errors)) {
        errorResponse(implode(', ', $errors));
    }
}

// Função para verificar disponibilidade do professor
function checkProfessorAvailability($conn, $professor_id, $data, $hora_inicio, $agendamento_id = null) {
    $sql = "SELECT id FROM agendamentos 
            WHERE professor_id = ? 
            AND data_agendamento = ? 
            AND status != 'cancelado'
            AND id != ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isi', $professor_id, $data, $agendamento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        errorResponse('Professor não está disponível neste horário');
    }
}

// Função para verificar disponibilidade do aluno
function checkAlunoAvailability($conn, $aluno_id, $data, $hora_inicio, $agendamento_id = null) {
    $sql = "SELECT id FROM agendamentos 
            WHERE aluno_id = ? 
            AND data_agendamento = ? 
            AND status != 'cancelado'
            AND id != ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isi', $aluno_id, $data, $agendamento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        errorResponse('Aluno não está disponível neste horário');
    }
}

// GET - Listar agendamentos (para dashboard)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Para dashboard: mostrar apenas próximas aulas (futuras e não canceladas)
        $sql = "SELECT 
                    a.id,
                    a.data_agendamento as data,
                    a.hora_inicio,
                    a.hora_fim,
                    a.status,
                    a.observacoes,
                    a.criado_em,
                    p.nome as professor_nome,
                    al.nome as aluno_nome,
                    c.nome as curso_nome
                FROM agendamentos a
                LEFT JOIN usuarios p ON a.professor_id = p.id
                LEFT JOIN usuarios al ON a.aluno_id = al.id
                LEFT JOIN cursos c ON a.curso_id = c.id
                WHERE a.status != 'cancelado'
                ORDER BY a.data_agendamento ASC, a.hora_inicio ASC";
        
        $result = $conn->query($sql);
        
        if (!$result) {
            errorResponse('Erro na consulta SQL: ' . $conn->error, 500);
        }
        
        $agendamentos = [];
        
        while ($row = $result->fetch_assoc()) {
            $agendamentos[] = $row;
        }
        
        successResponse($agendamentos, 'Agendamentos listados com sucesso');
        
    } catch (Exception $e) {
        errorResponse('Erro ao listar agendamentos: ' . $e->getMessage(), 500);
    }
}

// POST com ação 'listar' - Listar todas as aulas (para gerenciamento)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset(json_decode(file_get_contents('php://input'), true)['acao']) && json_decode(file_get_contents('php://input'), true)['acao'] === 'listar') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Para gerenciamento: mostrar todas as aulas (incluindo canceladas e passadas)
        $sql = "SELECT 
                    a.id,
                    a.data,
                    a.hora,
                    a.status,
                    a.observacoes,
                    a.data_criacao,
                    u.nome as aluno_nome,
                    c.nome as curso_nome
                FROM agendamentos a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                LEFT JOIN cursos c ON a.curso_id = c.id
                ORDER BY a.data DESC, a.hora DESC";
        
        $result = $conn->query($sql);
        
        if (!$result) {
            errorResponse('Erro na consulta SQL: ' . $conn->error, 500);
        }
        
        $agendamentos = [];
        
        while ($row = $result->fetch_assoc()) {
            $agendamentos[] = $row;
        }
        
        successResponse($agendamentos, 'Agendamentos listados com sucesso');
        
    } catch (Exception $e) {
        errorResponse('Erro ao listar agendamentos: ' . $e->getMessage(), 500);
    }
}

// POST - Criar novo agendamento ou ações específicas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Pegar dados do POST
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            errorResponse('Dados inválidos');
        }
        
        // Verificar se é uma ação específica (cancelar/reativar)
        if (isset($input['acao'])) {
            if ($input['acao'] === 'cancelar' && isset($input['agendamento_id'])) {
                // Cancelar agendamento
                $agendamento_id = $input['agendamento_id'];
                
                if (!is_numeric($agendamento_id)) {
                    errorResponse('ID do agendamento inválido');
                }
                
                // Verificar se agendamento existe
                $sql = "SELECT id FROM agendamentos WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $agendamento_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    errorResponse('Agendamento não encontrado', 404);
                }
                
                // Cancelar agendamento
                $sql = "UPDATE agendamentos SET status = 'cancelado' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $agendamento_id);
                
                if ($stmt->execute()) {
                    successResponse(['id' => $agendamento_id], 'Agendamento cancelado com sucesso');
                } else {
                    errorResponse('Erro ao cancelar agendamento');
                }
                
            } elseif ($input['acao'] === 'reativar' && isset($input['agendamento_id'])) {
                // Reativar agendamento
                $agendamento_id = $input['agendamento_id'];
                
                if (!is_numeric($agendamento_id)) {
                    errorResponse('ID do agendamento inválido');
                }
                
                // Verificar se agendamento existe
                $sql = "SELECT id FROM agendamentos WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $agendamento_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    errorResponse('Agendamento não encontrado', 404);
                }
                
                // Reativar agendamento
                $sql = "UPDATE agendamentos SET status = 'pendente' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $agendamento_id);
                
                if ($stmt->execute()) {
                    successResponse(['id' => $agendamento_id], 'Agendamento reativado com sucesso');
                } else {
                    errorResponse('Erro ao reativar agendamento');
                }
                
            } else {
                errorResponse('Ação inválida ou dados incompletos');
            }
        }
        
        // Se não é uma ação específica, criar novo agendamento
        // Debug: mostrar dados recebidos
        error_log("Dados recebidos: " . json_encode($input));
        
        // Validar dados
        validateAgendamento($input);
        
        // Verificar disponibilidade
        checkProfessorAvailability($conn, $input['professor_id'], $input['data'], $input['hora_inicio']);
        checkAlunoAvailability($conn, $input['aluno_id'], $input['data'], $input['hora_inicio']);
        
        // Inserir agendamento
        $sql = "INSERT INTO agendamentos (
                    data_agendamento, 
                    hora_inicio, 
                    hora_fim,
                    professor_id, 
                    curso_id,
                    aluno_id,
                    status, 
                    observacoes, 
                    criado_em
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        
        // Preparar observações e respeitar status enviado pelo formulário
        $observacoes = $input['observacoes'] ?? '';
        $statusPermitido = ['pendente', 'confirmado', 'agendado'];
        $status = in_array(($input['status'] ?? ''), $statusPermitido, true)
            ? $input['status']
            : 'confirmado';
        
        $stmt->bind_param(
            'sssiiiss',
            $input['data'],
            $input['hora_inicio'],
            $input['hora_fim'],
            $input['professor_id'],
            $input['curso_id'],
            $input['aluno_id'],
            $status,
            $observacoes
        );
        
        if ($stmt->execute()) {
            $agendamento_id = $conn->insert_id;
            
            // Buscar dados do agendamento criado
            $sql = "SELECT 
                        a.id,
                        a.data_agendamento as data,
                        a.hora_inicio,
                        a.hora_fim,
                        a.status,
                        a.observacoes,
                        a.criado_em,
                        p.nome as professor_nome,
                        al.nome as aluno_nome,
                        c.nome as curso_nome
                    FROM agendamentos a
                    LEFT JOIN usuarios p ON a.professor_id = p.id
                    LEFT JOIN usuarios al ON a.aluno_id = al.id
                    LEFT JOIN cursos c ON a.curso_id = c.id
                    WHERE a.id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $agendamento_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $agendamento = $result->fetch_assoc();
            
            successResponse($agendamento, 'Agendamento criado com sucesso');
        } else {
            errorResponse('Erro ao criar agendamento: ' . $stmt->error);
        }
        
    } catch (Exception $e) {
        errorResponse('Erro ao criar agendamento: ' . $e->getMessage(), 500);
    }
}

// PUT - Atualizar agendamento
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        // Pegar ID da URL
        $url_parts = explode('/', $_SERVER['REQUEST_URI']);
        $agendamento_id = end($url_parts);
        
        if (!is_numeric($agendamento_id)) {
            errorResponse('ID do agendamento inválido');
        }
        
        // Pegar dados do PUT
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            errorResponse('Dados inválidos');
        }
        
        // Verificar se agendamento existe
        $sql = "SELECT id FROM agendamentos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $agendamento_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            errorResponse('Agendamento não encontrado', 404);
        }
        
        // Se edição rápida do dashboard foi enviada, atualizar campos principais
        if (isset($input['data']) && isset($input['hora_inicio']) && isset($input['status'])) {
            $hora_fim = $input['hora_fim'] ?? null;
            $observacoes = $input['observacoes'] ?? '';
            
            $sql = "UPDATE agendamentos SET 
                        data_agendamento = ?,
                        hora_inicio = ?,
                        hora_fim = ?,
                        status = ?,
                        observacoes = ?
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                'sssssi',
                $input['data'],
                $input['hora_inicio'],
                $hora_fim,
                $input['status'],
                $observacoes,
                $agendamento_id
            );
            
            if ($stmt->execute()) {
                successResponse(['id' => $agendamento_id], 'Agendamento atualizado com sucesso');
            } else {
                errorResponse('Erro ao atualizar agendamento');
            }
        }
        
        // Se apenas status foi enviado (cancelar/reativar), atualizar apenas o status
        if (isset($input['status']) && count($input) === 1) {
            $sql = "UPDATE agendamentos SET status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $input['status'], $agendamento_id);
            
            if ($stmt->execute()) {
                successResponse(['id' => $agendamento_id], 'Status do agendamento atualizado com sucesso');
            } else {
                errorResponse('Erro ao atualizar status do agendamento');
            }
        } else {
            // Se dados completos foram enviados, validar e atualizar tudo
            validateAgendamento($input);
            
            // Atualizar agendamento completo
            $sql = "UPDATE agendamentos SET 
                        data = ?, 
                        hora = ?, 
                        servico = ?, 
                        professor = ?, 
                        nome = ?, 
                        email = ?, 
                        observacoes = ?
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            
            // Preparar observações
            $observacoes = $input['observacoes'] ?? '';
            
            $stmt->bind_param(
                'sssssssi',
                $input['data'],
                $input['hora'],
                $input['servico'],
                $input['professor'],
                $input['nome'],
                $input['email'],
                $observacoes,
                $agendamento_id
            );
            
            if ($stmt->execute()) {
                successResponse(['id' => $agendamento_id], 'Agendamento atualizado com sucesso');
            } else {
                errorResponse('Erro ao atualizar agendamento');
            }
        }
        
    } catch (Exception $e) {
        errorResponse('Erro ao atualizar agendamento: ' . $e->getMessage(), 500);
    }
}

// DELETE - Apagar agendamento
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        // Pegar ID da URL
        $url_parts = explode('/', $_SERVER['REQUEST_URI']);
        $agendamento_id = end($url_parts);
        
        if (!is_numeric($agendamento_id)) {
            errorResponse('ID do agendamento inválido');
        }
        
        // Verificar se agendamento existe
        $sql = "SELECT id FROM agendamentos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $agendamento_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            errorResponse('Agendamento não encontrado', 404);
        }
        
        // Apagar agendamento completamente (hard delete)
        $sql = "DELETE FROM agendamentos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $agendamento_id);
        
        if ($stmt->execute()) {
            successResponse(['id' => $agendamento_id], 'Aula removida com sucesso');
        } else {
            errorResponse('Erro ao remover aula');
        }
        
    } catch (Exception $e) {
        errorResponse('Erro ao cancelar agendamento: ' . $e->getMessage(), 500);
    }
}

// Método não suportado
errorResponse('Método não suportado', 405);
?>
