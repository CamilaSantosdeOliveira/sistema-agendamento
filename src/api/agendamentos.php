<?php
/**
 * API para Agendamentos
 * Sistema de Agendamento
 */

require_once '../config/database.php';
require_once '../models/Agendamento.php';

// Instância do modelo
$agendamento = new Agendamento();

// Obtém o método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Obtém o corpo da requisição para POST/PUT
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch($method) {
        
        case 'GET':
            // Se tem ID na URL, busca um agendamento específico
            if (isset($_GET['id'])) {
                $resultado = $agendamento->buscarPorId($_GET['id']);
                if ($resultado) {
                    echo json_encode($resultado);
                } else {
                    http_response_code(404);
                    echo json_encode(['erro' => 'Agendamento não encontrado']);
                }
            } 
            // Se tem email na URL, busca por email
            elseif (isset($_GET['email'])) {
                $resultados = $agendamento->buscarPorEmail($_GET['email']);
                echo json_encode($resultados);
            }
            // Se tem data na URL, busca por data
            elseif (isset($_GET['data'])) {
                $resultados = $agendamento->buscarPorData($_GET['data']);
                echo json_encode($resultados);
            }
            // Senão, lista todos
            else {
                $resultados = $agendamento->listar();
                echo json_encode($resultados);
            }
            break;

        case 'POST':
            // Valida dados obrigatórios
            if (!isset($input['nome']) || !isset($input['email']) || 
                !isset($input['data']) || !isset($input['hora']) || 
                !isset($input['servico'])) {
                
                http_response_code(400);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Dados obrigatórios não informados'
                ]);
                break;
            }

            // Valida formato do email
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Email inválido'
                ]);
                break;
            }

            // Valida data (não pode ser no passado)
            $dataAgendamento = new DateTime($input['data']);
            $hoje = new DateTime();
            $hoje->setTime(0, 0, 0);

            if ($dataAgendamento < $hoje) {
                http_response_code(400);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Não é possível agendar para datas passadas'
                ]);
                break;
            }

            // Verifica disponibilidade do horário
            if (!$agendamento->verificarDisponibilidade($input['data'], $input['hora'])) {
                http_response_code(409);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Horário já está ocupado'
                ]);
                break;
            }

            // Define os dados no modelo
            $agendamento->nome = $input['nome'];
            $agendamento->email = $input['email'];
            $agendamento->data = $input['data'];
            $agendamento->hora = $input['hora'];
            $agendamento->servico = $input['servico'];

            // Tenta criar o agendamento
            if ($agendamento->criar()) {
                http_response_code(201);
                echo json_encode([
                    'sucesso' => true,
                    'mensagem' => 'Agendamento criado com sucesso',
                    'id' => $agendamento->id
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Erro interno do servidor'
                ]);
            }
            break;

        case 'PUT':
            // Verifica se foi informado o ID
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'ID do agendamento é obrigatório'
                ]);
                break;
            }

            // Verifica se o agendamento existe
            $agendamentoExistente = $agendamento->buscarPorId($_GET['id']);
            if (!$agendamentoExistente) {
                http_response_code(404);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Agendamento não encontrado'
                ]);
                break;
            }

            // Valida dados obrigatórios
            if (!isset($input['nome']) || !isset($input['email']) || 
                !isset($input['data']) || !isset($input['hora']) || 
                !isset($input['servico']) || !isset($input['status'])) {
                
                http_response_code(400);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Dados obrigatórios não informados'
                ]);
                break;
            }

            // Verifica disponibilidade (excluindo o próprio agendamento)
            if (!$agendamento->verificarDisponibilidade($input['data'], $input['hora'], $_GET['id'])) {
                http_response_code(409);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Horário já está ocupado'
                ]);
                break;
            }

            // Define os dados no modelo
            $agendamento->id = $_GET['id'];
            $agendamento->nome = $input['nome'];
            $agendamento->email = $input['email'];
            $agendamento->data = $input['data'];
            $agendamento->hora = $input['hora'];
            $agendamento->servico = $input['servico'];
            $agendamento->status = $input['status'];

            // Tenta atualizar
            if ($agendamento->atualizar()) {
                echo json_encode([
                    'sucesso' => true,
                    'mensagem' => 'Agendamento atualizado com sucesso'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Erro interno do servidor'
                ]);
            }
            break;

        case 'DELETE':
            // Verifica se foi informado o ID
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'ID do agendamento é obrigatório'
                ]);
                break;
            }

            // Verifica se o agendamento existe
            $agendamentoExistente = $agendamento->buscarPorId($_GET['id']);
            if (!$agendamentoExistente) {
                http_response_code(404);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Agendamento não encontrado'
                ]);
                break;
            }

            // Tenta deletar
            if ($agendamento->deletar($_GET['id'])) {
                echo json_encode([
                    'sucesso' => true,
                    'mensagem' => 'Agendamento cancelado com sucesso'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Erro interno do servidor'
                ]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode([
                'erro' => 'Método não permitido'
            ]);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro interno do servidor',
        'erro' => $e->getMessage()
    ]);
}
?>
