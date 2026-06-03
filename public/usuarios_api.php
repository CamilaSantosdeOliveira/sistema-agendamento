<?php
/**
 * EduConnect - API de Usuários
 * Versão: 3.0
 * 
 * API para gerenciamento de usuários com tratamento robusto de erros
 */

// Desabilitar exibição de erros para não quebrar JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

// Headers JSON primeiro (antes de qualquer output)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Função para resposta JSON padronizada
function jsonResponse($success, $message, $data = null, $http_code = 200) {
    http_response_code($http_code);
    header('Content-Type: application/json; charset=utf-8');
    
    $response = [
        'sucesso' => $success,
        'mensagem' => $message
    ];
    
    if ($data !== null) {
        $response = array_merge($response, $data);
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    // Conecta com o banco usando o mesmo db.php
    require_once 'db.php';
    
    // Verificar se a conexão foi estabelecida
    if ($conn === null) {
        jsonResponse(false, 'Erro de conexão com o banco de dados. Verifique se o MySQL está rodando.', null, 500);
    }
    
    // Verificar se a conexão está ativa
    if (!$conn->ping()) {
        jsonResponse(false, 'Conexão com banco de dados perdida. Tente novamente.', null, 500);
    }
    
    // Ler dados JSON
    $input = file_get_contents('php://input');
    $dados = json_decode($input, true);
    
    // Se não conseguiu decodificar JSON, tentar POST normal
    if ($dados === null && !empty($_POST)) {
        $dados = $_POST;
    }
    
    $acao = $dados['acao'] ?? $_GET['acao'] ?? '';
    
    if (empty($acao)) {
        jsonResponse(false, 'Ação não especificada', null, 400);
    }
    
    switch($acao) {
        case 'cadastrar':
            cadastrarUsuario($conn, $dados['dados'] ?? []);
            break;
            
        case 'login':
            fazerLogin($conn, $dados['dados'] ?? []);
            break;
            
        case 'logout':
            fazerLogout();
            break;
            
        case 'perfil':
            obterPerfil($conn);
            break;
            
        case 'listar_professores':
            listarProfessores($conn, $_GET['materia'] ?? '');
            break;
            
        case 'materias_professor':
            $professor_id = intval($_GET['professor_id'] ?? 0);
            if ($professor_id <= 0) {
                jsonResponse(false, 'ID do professor inválido', null, 400);
            }
            $stmt = $conn->prepare("SELECT materia FROM professor_materias WHERE professor_id = ?");
            $stmt->bind_param("i", $professor_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $materias = [];
            while ($row = $result->fetch_assoc()) {
                $materias[] = $row['materia'];
            }
            jsonResponse(true, 'Matérias obtidas com sucesso', ['materias' => $materias]);
            break;
            
        default:
            jsonResponse(false, 'Ação não reconhecida: ' . $acao, null, 400);
    }
    
} catch(Exception $e) {
    // Log do erro
    error_log('usuarios_api.php Error: ' . $e->getMessage());
    
    // Resposta de erro
    jsonResponse(false, 'Erro interno do servidor. Tente novamente mais tarde.', null, 500);
} catch(Error $e) {
    // Log do erro fatal
    error_log('usuarios_api.php Fatal Error: ' . $e->getMessage());
    
    // Resposta de erro
    jsonResponse(false, 'Erro interno do servidor. Tente novamente mais tarde.', null, 500);
}

function cadastrarUsuario($conn, $dados) {
    if (empty($dados['email']) || empty($dados['senha'])) {
        jsonResponse(false, 'Email e senha são obrigatórios', null, 400);
    }
    
    // Verificar se email já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $dados['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        jsonResponse(false, 'Este email já está cadastrado!', null, 400);
    }
    
    // Hash da senha
    $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);
    
    // Inserir usuário
    $sql = "INSERT INTO usuarios (nome, email, senha, telefone, tipo_usuario, formacao, experiencia, valor_hora, descricao, criado_em) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        jsonResponse(false, 'Erro ao preparar query: ' . $conn->error, null, 500);
    }
    
    $nome = $dados['nome'] ?? '';
    $telefone = $dados['telefone'] ?? '';
    $tipo_usuario = $dados['tipo_usuario'] ?? 'aluno';
    $formacao = $dados['formacao'] ?? '';
    $experiencia = $dados['experiencia'] ?? '';
    $valor_hora = $dados['valor_hora'] ?? 0;
    $descricao = $dados['descricao'] ?? '';
    
    $stmt->bind_param("ssssssds", $nome, $dados['email'], $senhaHash, $telefone, $tipo_usuario, $formacao, $experiencia, $valor_hora, $descricao);
    
    if (!$stmt->execute()) {
        jsonResponse(false, 'Erro ao cadastrar usuário: ' . $stmt->error, null, 500);
    }
    
    $usuarioId = $conn->insert_id;
    
    // Se for professor, inserir matérias
    if ($tipo_usuario === 'professor' && !empty($dados['materias']) && is_array($dados['materias'])) {
        $stmtMateria = $conn->prepare("INSERT INTO professor_materias (professor_id, materia) VALUES (?, ?)");
        foreach ($dados['materias'] as $materia) {
            $stmtMateria->bind_param("is", $usuarioId, $materia);
            $stmtMateria->execute();
        }
    }
    
    jsonResponse(true, 'Usuário cadastrado com sucesso!', ['usuario_id' => $usuarioId]);
}

function fazerLogin($conn, $dados) {
    if (empty($dados['email']) || empty($dados['senha'])) {
        jsonResponse(false, 'Email e senha são obrigatórios', null, 400);
    }
    
    $email = trim($dados['email']);
    $senha = $dados['senha'];
    
    // Buscar usuário
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    if (!$stmt) {
        jsonResponse(false, 'Erro ao preparar query: ' . $conn->error, null, 500);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    
    if (!$usuario) {
        jsonResponse(false, 'Email ou senha incorretos!', null, 401);
    }
    
    // Verificar senha
    if (!password_verify($senha, $usuario['senha'])) {
        jsonResponse(false, 'Email ou senha incorretos!', null, 401);
    }
    
    // Permitir acesso para admin, professor e aluno
    if (!in_array($usuario['tipo_usuario'], ['admin', 'professor', 'aluno'])) {
        jsonResponse(false, 'Tipo de usuário não permitido!', null, 403);
    }
    
    // Criar sessão
    $_SESSION['user_id'] = $usuario['id'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
    $_SESSION['email'] = $usuario['email'];
    
    jsonResponse(true, 'Login realizado com sucesso!', [
        'usuario' => [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'tipo' => $usuario['tipo_usuario']
        ]
    ]);
}

function fazerLogout() {
    session_destroy();
    jsonResponse(true, 'Logout realizado com sucesso!');
}

function obterPerfil($conn) {
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(false, 'Usuário não logado', null, 401);
    }
    
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    if (!$stmt) {
        jsonResponse(false, 'Erro ao preparar query: ' . $conn->error, null, 500);
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    
    if (!$usuario) {
        jsonResponse(false, 'Usuário não encontrado', null, 404);
    }
    
    // Remover senha do retorno
    unset($usuario['senha']);
    
    // Se for professor, buscar matérias
    if ($usuario['tipo_usuario'] === 'professor') {
        $stmtMaterias = $conn->prepare("SELECT materia FROM professor_materias WHERE professor_id = ?");
        $stmtMaterias->bind_param("i", $usuario['id']);
        $stmtMaterias->execute();
        $resultMaterias = $stmtMaterias->get_result();
        $materias = [];
        while ($row = $resultMaterias->fetch_assoc()) {
            $materias[] = $row['materia'];
        }
        $usuario['materias'] = $materias;
    }
    
    jsonResponse(true, 'Perfil obtido com sucesso', ['usuario' => $usuario]);
}

function listarProfessores($conn, $materia = '') {
    $sql = "SELECT u.*, GROUP_CONCAT(pm.materia) as materias 
            FROM usuarios u 
            LEFT JOIN professor_materias pm ON u.id = pm.professor_id 
            WHERE u.tipo_usuario = 'professor'";
    
    if (!empty($materia)) {
        $sql .= " AND u.id IN (SELECT professor_id FROM professor_materias WHERE materia = ?)";
    }
    
    $sql .= " GROUP BY u.id ORDER BY u.nome";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        jsonResponse(false, 'Erro ao preparar query: ' . $conn->error, null, 500);
    }
    
    if (!empty($materia)) {
        $stmt->bind_param("s", $materia);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $professores = [];
    
    while ($professor = $result->fetch_assoc()) {
        // Remover senha
        unset($professor['senha']);
        
        // Processar matérias
        $professor['materias'] = $professor['materias'] ? explode(',', $professor['materias']) : [];
        
        $professores[] = $professor;
    }
    
    jsonResponse(true, 'Professores obtidos com sucesso', ['professores' => $professores]);
}
?>


