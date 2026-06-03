<?php
/**
 * API para Sistema de Avaliações
 * Gerencia avaliações e comentários dos professores
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
        case 'POST':
            // Criar nova avaliação
            if (isset($entrada['agendamento_id'])) {
                $stmt = $pdo->prepare("
                    INSERT INTO avaliacoes (
                        agendamento_id, aluno_nome, professor_nome, 
                        nota, comentario, criado_em
                    ) VALUES (?, ?, ?, ?, ?, NOW())
                ");
                
                $sucesso = $stmt->execute([
                    $entrada['agendamento_id'],
                    $entrada['aluno_nome'],
                    $entrada['professor_nome'],
                    $entrada['nota'],
                    $entrada['comentario']
                ]);
                
                if ($sucesso) {
                    // Atualizar status do agendamento
                    $stmt = $pdo->prepare("
                        UPDATE agendamentos 
                        SET status = 'Concluída' 
                        WHERE id = ?
                    ");
                    $stmt->execute([$entrada['agendamento_id']]);
                    
                    echo json_encode([
                        'sucesso' => true,
                        'mensagem' => 'Avaliação enviada com sucesso!'
                    ]);
                } else {
                    throw new Exception('Erro ao salvar avaliação');
                }
            }
            break;
            
        case 'GET':
            if (isset($_GET['professor'])) {
                // Buscar avaliações de um professor específico
                $stmt = $pdo->prepare("
                    SELECT * FROM avaliacoes 
                    WHERE professor_nome = ? 
                    ORDER BY criado_em DESC
                ");
                $stmt->execute([$_GET['professor']]);
                $avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Calcular média
                $media = 0;
                if (count($avaliacoes) > 0) {
                    $soma = array_sum(array_column($avaliacoes, 'nota'));
                    $media = round($soma / count($avaliacoes), 1);
                }
                
                echo json_encode([
                    'avaliacoes' => $avaliacoes,
                    'media' => $media,
                    'total' => count($avaliacoes)
                ]);
                
            } elseif (isset($_GET['agendamento_id'])) {
                // Verificar se já existe avaliação para este agendamento
                $stmt = $pdo->prepare("
                    SELECT * FROM avaliacoes 
                    WHERE agendamento_id = ?
                ");
                $stmt->execute([$_GET['agendamento_id']]);
                $avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'existe' => $avaliacao !== false,
                    'avaliacao' => $avaliacao
                ]);
                
            } elseif (isset($_GET['aulas_concluidas'])) {
                // Buscar aulas concluídas que ainda não foram avaliadas
                $stmt = $pdo->query("
                    SELECT a.*, 
                           av.id as avaliacao_id,
                           av.nota,
                           av.comentario
                    FROM agendamentos a
                    LEFT JOIN avaliacoes av ON a.id = av.agendamento_id
                    WHERE a.status IN ('Concluída', 'Realizada')
                    AND a.data < CURDATE()
                    ORDER BY a.data DESC, a.hora DESC
                ");
                
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
                
            } else {
                // Buscar todas as avaliações para dashboard admin
                $stmt = $pdo->query("
                    SELECT av.*, a.data, a.hora, a.servico
                    FROM avaliacoes av
                    JOIN agendamentos a ON av.agendamento_id = a.id
                    ORDER BY av.criado_em DESC
                    LIMIT 50
                ");
                
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            break;
            
        case 'DELETE':
            // Deletar avaliação (apenas admin)
            if (isset($_GET['id'])) {
                $stmt = $pdo->prepare("DELETE FROM avaliacoes WHERE id = ?");
                $sucesso = $stmt->execute([$_GET['id']]);
                
                echo json_encode([
                    'sucesso' => $sucesso,
                    'mensagem' => $sucesso ? 'Avaliação removida' : 'Erro ao remover'
                ]);
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
?>


