<?php
/**
 * EduConnect - Sistema de Agendamento de Aulas
 * Arquivo de Conexão com Banco de Dados
 * Versão: 3.0
 * 
 * Melhorias implementadas:
 * - Tratamento robusto de erros
 * - Configurações de segurança
 * - Timeout configurável
 * - Charset UTF-8
 * - Modo de erro estrito
 */

// Configurações de ambiente
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_agendamento');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// Configurações de segurança para produção
define('DB_TIMEOUT', 10); // segundos
define('DB_RETRY_ATTEMPTS', 3);

// Configuração de erros (desabilitar em produção)
$is_development = true; // Mude para false em produção
if (!$is_development) {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 1);
}

// Variável global de conexão
$conn = null;

/**
 * Função para estabelecer conexão com o banco de dados
 * @return mysqli|null Retorna a conexão ou null em caso de erro
 */
function getDatabaseConnection() {
    global $conn;
    
    // Se já existe conexão ativa, retornar
    if ($conn !== null && $conn->ping()) {
        return $conn;
    }
    
    $attempts = 0;
    $max_attempts = DB_RETRY_ATTEMPTS;
    
    while ($attempts < $max_attempts) {
        try {
            // Criar nova conexão
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            
            // Verificar erros de conexão
            if ($conn->connect_error) {
                throw new Exception('Erro de conexão: ' . $conn->connect_error);
            }
            
            // Configurar charset UTF-8
            if (!$conn->set_charset(DB_CHARSET)) {
                throw new Exception('Erro ao definir charset: ' . $conn->error);
            }
            
            // Configurar timeout
            $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, DB_TIMEOUT);
            
            // Habilitar modo de erro estrito
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            
            return $conn;
            
        } catch (Exception $e) {
            $attempts++;
            
            // Se é a última tentativa, tratar o erro
            if ($attempts >= $max_attempts) {
                handleDatabaseError($e);
                return null;
            }
            
            // Aguardar antes de tentar novamente
            usleep(500000); // 0.5 segundos
        }
    }
    
    return null;
}

/**
 * Função para tratar erros de banco de dados
 * @param Exception $e Exceção capturada
 */
function handleDatabaseError($e) {
    global $conn;
    
    // Fechar conexão se existir
    if ($conn !== null) {
        $conn->close();
        $conn = null;
    }
    
    // Log do erro (em produção, usar sistema de logs)
    error_log('Database Error: ' . $e->getMessage());
    
    // Se estamos em uma API ou requisição AJAX
    if (headers_sent() === false) {
        // Verificar se é requisição AJAX
        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if ($is_ajax || strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Banco de dados não está disponível. Verifique se o MySQL está rodando.',
                'error' => $is_development ? $e->getMessage() : 'Erro interno do servidor'
            ]);
            exit;
        }
    }
}

// Estabelecer conexão inicial
$conn = getDatabaseConnection();

// Se não conseguiu conectar e não é API, mostrar mensagem amigável
if ($conn === null && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    // Não fazer nada aqui - deixar cada página tratar seu próprio erro
    // Isso evita quebrar páginas que não precisam do banco
}
?>