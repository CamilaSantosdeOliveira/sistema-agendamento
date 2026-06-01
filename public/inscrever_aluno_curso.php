<?php
require_once 'config_security.php';
require_once 'db.php';
require_once 'utils.php';
require_once 'cache.php';

// Verificar se o usuário está logado e é aluno
if (!requireAuth('aluno')) {
    jsonResponse(false, 'Usuário não autorizado', null, 401);
}

// Validar entrada
$curso_id = validateInput($_POST['curso_id'] ?? '', 'int');
if (!$curso_id) {
    jsonResponse(false, 'ID do curso inválido', null, 400);
}

$aluno_id = $_SESSION['user_id'];

try {
    // Verificar se o aluno já está inscrito no curso (com cache)
    $cache_key = "aluno_{$aluno_id}_curso_{$curso_id}_inscrito";
    $ja_inscrito = cache()->remember($cache_key, function() use ($conn, $curso_id, $aluno_id) {
        $stmt = prepareSecureQuery($conn, 
            "SELECT COUNT(*) as ja_inscrito FROM agendamentos WHERE curso_id = ? AND aluno_id = ?",
            [$curso_id, $aluno_id]
        );
        if (!$stmt) {
            return false;
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['ja_inscrito'] > 0;
    }, 300); // Cache por 5 minutos
    
    if ($ja_inscrito) {
        jsonResponse(false, 'Você já está inscrito neste curso', null, 400);
    }
    
    // Buscar dados do curso (com cache)
    $curso = cache()->remember("curso_{$curso_id}", function() use ($conn, $curso_id) {
        $stmt = prepareSecureQuery($conn, "SELECT * FROM cursos WHERE id = ?", [$curso_id]);
        if (!$stmt) {
            return null;
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }, 3600); // Cache por 1 hora
    
    if (!$curso) {
        jsonResponse(false, 'Curso não encontrado', null, 404);
    }
    
    // Buscar professores disponíveis (com cache)
    $professores = cache()->remember('professores_disponiveis', function() use ($conn) {
        $stmt = prepareSecureQuery($conn, 
            "SELECT id, nome FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1 ORDER BY id",
            []
        );
        if (!$stmt) {
            return [];
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }, 1800); // Cache por 30 minutos
    
    if (empty($professores)) {
        jsonResponse(false, 'Nenhum professor disponível', null, 503);
    }
    
    // Distribuir professores baseado no ID do curso (módulo)
    $professor_index = ($curso_id - 1) % count($professores);
    $professor = $professores[$professor_index];
    $professor_id = $professor['id'];
    
    // Calcular data para a aula (próxima semana)
    $data_atual = new DateTime();
    $data_aula = clone $data_atual;
    $data_aula->add(new DateInterval('P7D')); // Adiciona 7 dias (próxima semana)
    $data_aulas = [$data_aula->format('Y-m-d')];
    
    // Criar agendamentos para as aulas
    $stmt = prepareSecureQuery($conn,
        "INSERT INTO agendamentos (aluno_id, professor_id, curso_id, data_agendamento, hora_inicio, hora_fim, status, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        []
    );
    
    if (!$stmt) {
        throw new Exception("Erro ao preparar query: " . $conn->error);
    }
    
    $hora_inicio = '14:00:00';
    $hora_fim = '16:00:00';
    $status = 'agendado';
    $observacoes = 'Aula do curso ' . sanitizeOutput($curso['nome']) . ' com ' . sanitizeOutput($professor['nome']);
    
    $aulas_criadas = 0;
    foreach ($data_aulas as $data) {
        $stmt = prepareSecureQuery($conn,
            "INSERT INTO agendamentos (aluno_id, professor_id, curso_id, data_agendamento, hora_inicio, hora_fim, status, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$aluno_id, $professor_id, $curso_id, $data, $hora_inicio, $hora_fim, $status, $observacoes]
        );
        
        if (!$stmt || !$stmt->execute()) {
            throw new Exception("Erro ao inserir agendamento: " . ($stmt ? $stmt->error : $conn->error));
        }
        
        $aulas_criadas++;
    }
    
    // Limpar cache relacionado
    cache()->delete("aluno_{$aluno_id}_cursos");
    cache()->delete("aluno_{$aluno_id}_aulas");
    cache()->delete($cache_key);
    
    // Log da ação
    logAction('curso_inscrito', $aluno_id, "Curso ID: $curso_id, Professor: {$professor['nome']}");
    
    jsonResponse(true, 
        'Inscrição realizada com sucesso! Foi criada 1 aula para você com ' . sanitizeOutput($professor['nome']) . '.',
        [
            'aulas_criadas' => count($data_aulas),
            'professor' => sanitizeOutput($professor['nome'])
        ]
    );
    
} catch (Exception $e) {
    // Log do erro
    error_log('Erro em inscrever_aluno_curso.php: ' . $e->getMessage());
    
    jsonResponse(false, 
        'Erro ao realizar inscrição. Tente novamente mais tarde.', 
        null, 
        500
    );
}
?>
