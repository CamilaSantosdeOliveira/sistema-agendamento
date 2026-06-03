<?php
header('Content-Type: application/json');
include 'db.php';

try {
    // Verificar se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    // Obter dados do formulário
    $curso_id = $_POST['curso_id'] ?? '';
    $professor_id = $_POST['professor_id'] ?? '';
    $data = $_POST['data'] ?? '';
    $horario = $_POST['horario'] ?? '';
    $titulo = $_POST['titulo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $tipo_evento = $_POST['tipo_evento'] ?? '';
    $link_reuniao = $_POST['link_reuniao'] ?? '';
    $duracao = $_POST['duracao'] ?? 90;
    $capacidade = $_POST['capacidade'] ?? 100;

    // Se o link da reunião estiver vazio, definir como NULL
    if (empty($link_reuniao)) {
        $link_reuniao = null;
    }

    // Validações básicas
    if (empty($curso_id) || empty($data) || empty($horario)) {
        throw new Exception('Preencha todos os campos obrigatórios');
    }

    // Validar formato da data
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        throw new Exception('Formato de data inválido');
    }

    // Validar formato do horário
    if (!preg_match('/^\d{2}:\d{2}$/', $horario)) {
        throw new Exception('Formato de horário inválido');
    }

    // Verificar se a data não é passada
    if (strtotime($data) < strtotime(date('Y-m-d'))) {
        throw new Exception('Não é possível agendar para datas passadas');
    }

    // Verificar se o curso existe
    $check_curso = "SELECT id FROM cursos WHERE id = ? AND status = 'ativo'";
    $stmt_curso = $conn->prepare($check_curso);
    $stmt_curso->bind_param("i", $curso_id);
    $stmt_curso->execute();
    $result_curso = $stmt_curso->get_result();
    
    if ($result_curso->num_rows === 0) {
        throw new Exception('Curso não encontrado ou inativo');
    }
    $stmt_curso->close();

    // Verificar se o professor existe (se fornecido)
    if (!empty($professor_id)) {
        $check_professor = "SELECT id FROM professores WHERE id = ? AND status = 'ativo'";
        $stmt_professor = $conn->prepare($check_professor);
        $stmt_professor->bind_param("i", $professor_id);
        $stmt_professor->execute();
        $result_professor = $stmt_professor->get_result();
        
        if ($result_professor->num_rows === 0) {
            throw new Exception('Professor não encontrado ou inativo');
        }
        $stmt_professor->close();
    }

    // Verificar se não há conflito de horário para o mesmo curso
    $check_conflito = "SELECT id FROM agendamentos WHERE curso_id = ? AND data = ? AND horario = ? AND status != 'cancelado'";
    $stmt_conflito = $conn->prepare($check_conflito);
    $stmt_conflito->bind_param("iss", $curso_id, $data, $horario);
    $stmt_conflito->execute();
    $result_conflito = $stmt_conflito->get_result();
    
    if ($result_conflito->num_rows > 0) {
        throw new Exception('Já existe um agendamento para este curso, data e horário');
    }
    $stmt_conflito->close();

    // Inserir o agendamento
    $sql = "INSERT INTO agendamentos (curso_id, professor_id, data, horario, titulo, descricao, tipo_evento, link_reuniao, duracao, capacidade, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendente')";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("iissssssis", $curso_id, $professor_id, $data, $horario, $titulo, $descricao, $tipo_evento, $link_reuniao, $duracao, $capacidade);
    
    if ($stmt->execute()) {
        $agendamento_id = $conn->insert_id;
        
        // Buscar informações do curso para a resposta
        $curso_info = "SELECT nome FROM cursos WHERE id = ?";
        $stmt_curso_info = $conn->prepare($curso_info);
        $stmt_curso_info->bind_param("i", $curso_id);
        $stmt_curso_info->execute();
        $result_curso_info = $stmt_curso_info->get_result();
        $curso_nome = $result_curso_info->fetch_assoc()['nome'];
        $stmt_curso_info->close();
        
        echo json_encode([
            'success' => true,
            'message' => 'Agendamento criado com sucesso!',
            'data' => [
                'id' => $agendamento_id,
                'curso' => $curso_nome,
                'data' => $data,
                'horario' => $horario,
                'status' => 'pendente'
            ]
        ]);
    } else {
        throw new Exception('Erro ao inserir agendamento: ' . $conn->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor: ' . $e->getMessage()
    ]);
}

$conn->close();
?>

