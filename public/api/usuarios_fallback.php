<?php
// Suprimir exibição de erros para garantir JSON válido
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Processar requisições
$method = $_SERVER['REQUEST_METHOD'];

// Para requisições POST com JSON, pegar a ação do JSON
$input = '';
$json_data = [];
if ($method === 'POST' && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $input = file_get_contents('php://input');
    $json_data = json_decode($input, true);
    $action = $json_data['action'] ?? '';
} else {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
}

try {
    switch ($action) {
        case 'desativar_usuario':
            if ($method === 'POST') {
                $data = $json_data;
                $id = $data['id'] ?? 0;
                
                // Simular sucesso (sem banco)
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuário desativado com sucesso! (modo simulação)',
                    'id' => $id,
                    'note' => 'Banco de dados não disponível - ação simulada'
                ]);
            }
            break;
            
        case 'ativar_usuario':
            if ($method === 'POST') {
                $data = $json_data;
                $id = $data['id'] ?? 0;
                
                // Simular sucesso (sem banco)
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuário ativado com sucesso! (modo simulação)',
                    'id' => $id,
                    'note' => 'Banco de dados não disponível - ação simulada'
                ]);
            }
            break;
            
        case 'excluir_usuario':
            if ($method === 'POST') {
                $data = $json_data;
                $id = $data['id'] ?? 0;
                
                // Simular sucesso (sem banco)
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuário excluído com sucesso! (modo simulação)',
                    'id' => $id,
                    'note' => 'Banco de dados não disponível - ação simulada'
                ]);
            }
            break;
            
        case 'editar_usuario':
            if ($method === 'POST') {
                $data = $json_data;
                $id = $data['id'] ?? 0;
                
                // Simular sucesso (sem banco)
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuário atualizado com sucesso! (modo simulação)',
                    'id' => $id,
                    'note' => 'Banco de dados não disponível - ação simulada'
                ]);
            }
            break;
            
        case 'buscar_usuario':
            if ($method === 'GET') {
                $id = $_GET['id'] ?? 0;
                
                // Simular dados de usuário
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'id' => $id,
                        'nome' => 'Usuário Teste',
                        'email' => 'teste@email.com',
                        'telefone' => '(11) 99999-9999',
                        'tipo_usuario' => 'aluno',
                        'ativo' => 1,
                        'note' => 'Dados simulados - banco não disponível'
                    ]
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Ação não reconhecida: "' . $action . '"',
                'note' => 'API em modo simulação - banco não disponível',
                'debug' => [
                    'method' => $method,
                    'action' => $action,
                    'get' => $_GET,
                    'post' => $_POST,
                    'raw_input' => $input
                ]
            ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno: ' . $e->getMessage(),
        'note' => 'API em modo simulação'
    ]);
}
?>
















