<?php
/**
 * Sistema de Pagamentos
 * Integração com APIs de pagamento (simulado para desenvolvimento)
 */

require_once 'config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

class PagamentoService {
    private $pdo;
    
    // Valores das aulas (pode ser configurável)
    private $precos = [
        'matematica' => 45.00,
        'portugues' => 40.00,
        'ingles' => 50.00,
        'fisica' => 45.00,
        'quimica' => 45.00,
        'biologia' => 40.00,
        'historia' => 35.00,
        'geografia' => 35.00,
        'filosofia' => 35.00,
        'sociologia' => 35.00,
        'redacao' => 40.00,
        'informatica' => 55.00
    ];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Criar transação de pagamento
     */
    public function criarPagamento($agendamentoId, $metodoPagamento = 'cartao') {
        try {
            // Buscar dados do agendamento
            $stmt = $this->pdo->prepare("SELECT * FROM agendamentos WHERE id = ?");
            $stmt->execute([$agendamentoId]);
            $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$agendamento) {
                throw new Exception('Agendamento não encontrado');
            }
            
            // Calcular valor
            $valor = $this->precos[$agendamento['servico']] ?? 40.00;
            
            // Simular integração com gateway de pagamento
            $transacaoId = 'TXN_' . time() . '_' . rand(1000, 9999);
            $status = 'pendente';
            
            // Em produção, aqui seria feita a chamada para a API real
            if (rand(1, 10) > 2) { // 80% de sucesso (simulado)
                $status = 'aprovado';
            } else {
                $status = 'rejeitado';
            }
            
            // Salvar transação no banco
            $stmt = $this->pdo->prepare("
                INSERT INTO pagamentos (
                    agendamento_id, transacao_id, valor, metodo_pagamento,
                    status, criado_em
                ) VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $agendamentoId,
                $transacaoId,
                $valor,
                $metodoPagamento,
                $status
            ]);
            
            $pagamentoId = $this->pdo->lastInsertId();
            
            // Se aprovado, confirmar agendamento
            if ($status === 'aprovado') {
                $stmt = $this->pdo->prepare("
                    UPDATE agendamentos 
                    SET status = 'Confirmada - Pago' 
                    WHERE id = ?
                ");
                $stmt->execute([$agendamentoId]);
            }
            
            return [
                'sucesso' => true,
                'pagamento_id' => $pagamentoId,
                'transacao_id' => $transacaoId,
                'valor' => $valor,
                'status' => $status,
                'mensagem' => $this->getMensagemStatus($status)
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Consultar status do pagamento
     */
    public function consultarPagamento($transacaoId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, a.nome, a.servico, a.data, a.hora
                FROM pagamentos p
                JOIN agendamentos a ON p.agendamento_id = a.id
                WHERE p.transacao_id = ?
            ");
            $stmt->execute([$transacaoId]);
            $pagamento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pagamento) {
                throw new Exception('Pagamento não encontrado');
            }
            
            return [
                'sucesso' => true,
                'pagamento' => $pagamento
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Listar pagamentos de um período
     */
    public function listarPagamentos($inicio = null, $fim = null) {
        try {
            $sql = "
                SELECT p.*, a.nome, a.servico, a.data, a.hora, a.professor
                FROM pagamentos p
                JOIN agendamentos a ON p.agendamento_id = a.id
            ";
            
            $params = [];
            
            if ($inicio && $fim) {
                $sql .= " WHERE DATE(p.criado_em) BETWEEN ? AND ?";
                $params = [$inicio, $fim];
            }
            
            $sql .= " ORDER BY p.criado_em DESC LIMIT 50";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return [
                'sucesso' => true,
                'pagamentos' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Processar reembolso
     */
    public function processarReembolso($pagamentoId, $motivo = '') {
        try {
            // Buscar pagamento
            $stmt = $this->pdo->prepare("SELECT * FROM pagamentos WHERE id = ?");
            $stmt->execute([$pagamentoId]);
            $pagamento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pagamento) {
                throw new Exception('Pagamento não encontrado');
            }
            
            if ($pagamento['status'] !== 'aprovado') {
                throw new Exception('Só é possível reembolsar pagamentos aprovados');
            }
            
            // Simular processo de reembolso
            $reembolsoId = 'REF_' . time() . '_' . rand(1000, 9999);
            
            // Atualizar status do pagamento
            $stmt = $this->pdo->prepare("
                UPDATE pagamentos 
                SET status = 'reembolsado', 
                    reembolso_id = ?,
                    motivo_reembolso = ?,
                    reembolsado_em = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([$reembolsoId, $motivo, $pagamentoId]);
            
            // Atualizar status do agendamento
            $stmt = $this->pdo->prepare("
                UPDATE agendamentos 
                SET status = 'Cancelada - Reembolsada' 
                WHERE id = ?
            ");
            $stmt->execute([$pagamento['agendamento_id']]);
            
            return [
                'sucesso' => true,
                'reembolso_id' => $reembolsoId,
                'mensagem' => 'Reembolso processado com sucesso'
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obter preços das matérias
     */
    public function obterPrecos() {
        return [
            'sucesso' => true,
            'precos' => $this->precos
        ];
    }
    
    private function getMensagemStatus($status) {
        $mensagens = [
            'pendente' => 'Pagamento pendente de processamento',
            'aprovado' => 'Pagamento aprovado com sucesso!',
            'rejeitado' => 'Pagamento rejeitado. Tente outro cartão.',
            'reembolsado' => 'Pagamento reembolsado'
        ];
        
        return $mensagens[$status] ?? 'Status desconhecido';
    }
}

try {
    $metodo = $_SERVER['REQUEST_METHOD'];
    $entrada = json_decode(file_get_contents('php://input'), true);
    $pagamentoService = new PagamentoService($pdo);
    
    switch ($metodo) {
        case 'POST':
            if (isset($entrada['action'])) {
                switch ($entrada['action']) {
                    case 'criar_pagamento':
                        echo json_encode($pagamentoService->criarPagamento(
                            $entrada['agendamento_id'],
                            $entrada['metodo_pagamento'] ?? 'cartao'
                        ));
                        break;
                        
                    case 'reembolso':
                        echo json_encode($pagamentoService->processarReembolso(
                            $entrada['pagamento_id'],
                            $entrada['motivo'] ?? ''
                        ));
                        break;
                        
                    default:
                        throw new Exception('Ação não reconhecida');
                }
            }
            break;
            
        case 'GET':
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'consultar':
                        echo json_encode($pagamentoService->consultarPagamento($_GET['transacao_id']));
                        break;
                        
                    case 'listar':
                        echo json_encode($pagamentoService->listarPagamentos(
                            $_GET['inicio'] ?? null,
                            $_GET['fim'] ?? null
                        ));
                        break;
                        
                    case 'precos':
                        echo json_encode($pagamentoService->obterPrecos());
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
?>


