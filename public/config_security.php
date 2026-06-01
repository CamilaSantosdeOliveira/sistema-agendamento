<?php
/**
 * EduConnect - Configurações de Segurança
 * Versão: 3.0
 * 
 * Este arquivo contém configurações de segurança centralizadas
 */

// Configurações de Sessão Segura
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Mude para 1 em produção com HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Tempo de expiração da sessão (30 minutos)
ini_set('session.gc_maxlifetime', 1800);
session_set_cookie_params(1800);

/**
 * Função para validar e sanitizar entrada de dados
 * @param mixed $data Dados a serem validados
 * @param string $type Tipo de validação (email, int, string, etc)
 * @return mixed Dados validados ou false em caso de erro
 */
function validateInput($data, $type = 'string') {
    if ($data === null || $data === '') {
        return false;
    }
    
    switch ($type) {
        case 'email':
            $data = filter_var(trim($data), FILTER_SANITIZE_EMAIL);
            return filter_var($data, FILTER_VALIDATE_EMAIL) ? $data : false;
            
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT) !== false ? (int)$data : false;
            
        case 'float':
            return filter_var($data, FILTER_VALIDATE_FLOAT) !== false ? (float)$data : false;
            
        case 'url':
            $data = filter_var(trim($data), FILTER_SANITIZE_URL);
            return filter_var($data, FILTER_VALIDATE_URL) ? $data : false;
            
        case 'string':
        default:
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            return $data;
    }
}

/**
 * Função para validar se o usuário está autenticado
 * @param string $required_type Tipo de usuário requerido (aluno, professor, admin)
 * @return bool True se autenticado e autorizado
 */
function requireAuth($required_type = null) {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        return false;
    }
    
    if ($required_type !== null) {
        if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== $required_type) {
            return false;
        }
    }
    
    return true;
}

/**
 * Função para redirecionar se não autenticado
 * @param string $required_type Tipo de usuário requerido
 * @param string $redirect_url URL para redirecionar (padrão: login.php)
 */
function requireLogin($required_type = null, $redirect_url = 'login.php') {
    if (!requireAuth($required_type)) {
        header('Location: ' . $redirect_url);
        exit();
    }
}

/**
 * Função para gerar token CSRF
 * @return string Token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Função para validar token CSRF
 * @param string $token Token a ser validado
 * @return bool True se válido
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Função para sanitizar saída HTML
 * @param string $data Dados a serem sanitizados
 * @return string Dados sanitizados
 */
function sanitizeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Função para validar e preparar query SQL
 * @param mysqli $conn Conexão com banco
 * @param string $query Query SQL
 * @param array $params Parâmetros para bind
 * @return mysqli_stmt|false Statement preparado ou false
 */
function prepareSecureQuery($conn, $query, $params = []) {
    if ($conn === null) {
        return false;
    }
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log('Query preparation failed: ' . $conn->error);
        return false;
    }
    
    if (!empty($params)) {
        $types = '';
        $values = [];
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $param;
        }
        
        $stmt->bind_param($types, ...$values);
    }
    
    return $stmt;
}

/**
 * Função para rate limiting (proteção contra brute force)
 * @param string $identifier Identificador único (IP, email, etc)
 * @param int $max_attempts Número máximo de tentativas
 * @param int $time_window Janela de tempo em segundos
 * @return bool True se dentro do limite, false se excedido
 */
function checkRateLimit($identifier, $max_attempts = 5, $time_window = 300) {
    $key = 'rate_limit_' . md5($identifier);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'attempts' => 0,
            'reset_time' => time() + $time_window
        ];
    }
    
    // Resetar se passou o tempo
    if (time() > $_SESSION[$key]['reset_time']) {
        $_SESSION[$key] = [
            'attempts' => 0,
            'reset_time' => time() + $time_window
        ];
    }
    
    // Incrementar tentativas
    $_SESSION[$key]['attempts']++;
    
    // Verificar se excedeu o limite
    if ($_SESSION[$key]['attempts'] > $max_attempts) {
        return false;
    }
    
    return true;
}

// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


