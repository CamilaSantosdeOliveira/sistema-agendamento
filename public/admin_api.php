<?php
/**
 * API para Painel Administrativo
 * Gerencia todas as funcionalidades administrativas
 */

require_once 'config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $metodo = $_SERVER['REQUEST_METHOD'];
    $entrada = json_decode(file_get_contents('php://input'), true);
    
    switch ($metodo) {
        case 'GET':
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'estatisticas':
                        echo json_encode(obterEstatisticas($pdo));
                        break;
                        
                    case 'usuarios':
                        echo json_encode(listarUsuarios($pdo));
                        break;
                        
                    case 'agendamentos':
                        echo json_encode(listarTodosAgendamentos($pdo));
                        break;
                        
                    case 'avaliacoes':
                        echo json_encode(listarTodasAvaliacoes($pdo));
                        break;
                        
                    case 'relatorio_mensal':
                        $mes = $_GET['mes'] ?? date('Y-m');
                        echo json_encode(relatorioMensal($pdo, $mes));
                        break;
                        
                    default:
                        throw new Exception('Ação não reconhecida');
                }
            }
            break;
            
        case 'POST':
            if (isset($entrada['action'])) {
                switch ($entrada['action']) {
                    case 'aprovar_professor':
                        echo json_encode(aprovarProfessor($pdo, $entrada['usuario_id']));
                        break;
                        
                    case 'banir_usuario':
                        echo json_encode(banirUsuario($pdo, $entrada['usuario_id']));
                        break;
                        
                    case 'alterar_status_agendamento':
                        echo json_encode(alterarStatusAgendamento($pdo, $entrada['agendamento_id'], $entrada['status']));
                        break;
                        
                    default:
                        throw new Exception('Ação não reconhecida');
                }
            }
            break;
            
        case 'DELETE':
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'usuario':
                        echo json_encode(removerUsuario($pdo, $_GET['id']));
                        break;
                        
                    case 'agendamento':
                        echo json_encode(removerAgendamento($pdo, $_GET['id']));
                        break;
                        
                    default:
                        throw new Exception('Ação não reconhecida');
                }
            }
            break;
            
        default:
            throw new Exception('Método não permitido');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);
}

// Funções auxiliares

function obterEstatisticas($pdo) {
    $stats = [];
    
    // Total de usuários
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $stats['total_usuarios'] = $stmt->fetch()['total'];
    
    // Usuários por tipo
    $stmt = $pdo->query("SELECT tipo_usuario, COUNT(*) as total FROM usuarios GROUP BY tipo_usuario");
    $stats['usuarios_por_tipo'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Total de agendamentos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM agendamentos");
    $stats['total_agendamentos'] = $stmt->fetch()['total'];
    
    // Agendamentos por status
    $stmt = $pdo->query("SELECT status, COUNT(*) as total FROM agendamentos GROUP BY status");
    $stats['agendamentos_por_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Agendamentos este mês
    $stmt = $pdo->query("
        SELECT COUNT(*) as total 
        FROM agendamentos 
        WHERE MONTH(criado_em) = MONTH(CURRENT_DATE()) 
        AND YEAR(criado_em) = YEAR(CURRENT_DATE())
    ");
    $stats['agendamentos_mes'] = $stmt->fetch()['total'];
    
    // Matérias mais populares
    $stmt = $pdo->query("
        SELECT servico, COUNT(*) as total 
        FROM agendamentos 
        GROUP BY servico 
        ORDER BY total DESC 
        LIMIT 5
    ");
    $stats['materias_populares'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Média de avaliações
    $stmt = $pdo->query("SELECT AVG(nota) as media FROM avaliacoes");
    $result = $stmt->fetch();
    $stats['media_avaliacoes'] = round($result['media'] ?: 0, 1);
    
    // Total de avaliações
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM avaliacoes");
    $stats['total_avaliacoes'] = $stmt->fetch()['total'];
    
    // Agendamentos por dia (últimos 7 dias)
    $stmt = $pdo->query("
        SELECT DATE(criado_em) as data, COUNT(*) as total
        FROM agendamentos 
        WHERE criado_em >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)
        GROUP BY DATE(criado_em)
        ORDER BY data
    ");
    $stats['agendamentos_por_dia'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $stats;
}

function listarUsuarios($pdo) {
    $stmt = $pdo->query("
        SELECT id, nome, email, tipo_usuario, criado_em, status
        FROM usuarios 
        ORDER BY criado_em DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function listarTodosAgendamentos($pdo) {
    $stmt = $pdo->query("
        SELECT a.*, 
               u.email as professor_email,
               av.nota as avaliacao_nota
        FROM agendamentos a
        LEFT JOIN usuarios u ON u.nome = a.professor AND u.tipo_usuario = 'professor'
        LEFT JOIN avaliacoes av ON av.agendamento_id = a.id
        ORDER BY a.criado_em DESC
        LIMIT 100
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function listarTodasAvaliacoes($pdo) {
    $stmt = $pdo->query("
        SELECT av.*, a.data, a.hora, a.servico
        FROM avaliacoes av
        JOIN agendamentos a ON av.agendamento_id = a.id
        ORDER BY av.criado_em DESC
        LIMIT 50
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function relatorioMensal($pdo, $mes) {
    $relatorio = [];
    
    // Agendamentos do mês
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total, status
        FROM agendamentos 
        WHERE DATE_FORMAT(data, '%Y-%m') = ?
        GROUP BY status
    ");
    $stmt->execute([$mes]);
    $relatorio['agendamentos'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Receita estimada (simulada)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) * 50 as receita_estimada
        FROM agendamentos 
        WHERE DATE_FORMAT(data, '%Y-%m') = ?
        AND status IN ('Concluída', 'Realizada')
    ");
    $stmt->execute([$mes]);
    $relatorio['receita_estimada'] = $stmt->fetch()['receita_estimada'];
    
    // Professores mais ativos
    $stmt = $pdo->prepare("
        SELECT professor, COUNT(*) as total_aulas
        FROM agendamentos 
        WHERE DATE_FORMAT(data, '%Y-%m') = ?
        AND professor IS NOT NULL
        GROUP BY professor
        ORDER BY total_aulas DESC
        LIMIT 5
    ");
    $stmt->execute([$mes]);
    $relatorio['professores_ativos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $relatorio;
}

function aprovarProfessor($pdo, $usuarioId) {
    $stmt = $pdo->prepare("UPDATE usuarios SET status = 'ativo' WHERE id = ? AND tipo_usuario = 'professor'");
    $sucesso = $stmt->execute([$usuarioId]);
    
    return [
        'sucesso' => $sucesso,
        'mensagem' => $sucesso ? 'Professor aprovado' : 'Erro ao aprovar professor'
    ];
}

function banirUsuario($pdo, $usuarioId) {
    $stmt = $pdo->prepare("UPDATE usuarios SET status = 'banido' WHERE id = ?");
    $sucesso = $stmt->execute([$usuarioId]);
    
    return [
        'sucesso' => $sucesso,
        'mensagem' => $sucesso ? 'Usuário banido' : 'Erro ao banir usuário'
    ];
}

function alterarStatusAgendamento($pdo, $agendamentoId, $status) {
    $stmt = $pdo->prepare("UPDATE agendamentos SET status = ? WHERE id = ?");
    $sucesso = $stmt->execute([$status, $agendamentoId]);
    
    return [
        'sucesso' => $sucesso,
        'mensagem' => $sucesso ? 'Status alterado' : 'Erro ao alterar status'
    ];
}

function removerUsuario($pdo, $usuarioId) {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $sucesso = $stmt->execute([$usuarioId]);
    
    return [
        'sucesso' => $sucesso,
        'mensagem' => $sucesso ? 'Usuário removido' : 'Erro ao remover usuário'
    ];
}

function removerAgendamento($pdo, $agendamentoId) {
    $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = ?");
    $sucesso = $stmt->execute([$agendamentoId]);
    
    return [
        'sucesso' => $sucesso,
        'mensagem' => $sucesso ? 'Agendamento removido' : 'Erro ao remover agendamento'
    ];
}
?>


