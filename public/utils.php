<?php
/**
 * EduConnect - Funções Utilitárias
 * Versão: 3.0
 * 
 * Funções auxiliares para uso em todo o sistema
 */

/**
 * Função para formatar data no padrão brasileiro
 * @param string $date Data no formato MySQL (YYYY-MM-DD)
 * @return string Data formatada (DD/MM/YYYY)
 */
function formatDateBR($date) {
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }
    
    $timestamp = strtotime($date);
    return date('d/m/Y', $timestamp);
}

/**
 * Função para formatar data e hora
 * @param string $datetime Data e hora no formato MySQL
 * @return string Data e hora formatada
 */
function formatDateTimeBR($datetime) {
    if (empty($datetime)) {
        return '-';
    }
    
    $timestamp = strtotime($datetime);
    return date('d/m/Y H:i', $timestamp);
}

/**
 * Função para formatar moeda brasileira
 * @param float $value Valor numérico
 * @return string Valor formatado (R$ X.XXX,XX)
 */
function formatCurrency($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

/**
 * Função para calcular tempo decorrido (há X minutos/horas/dias)
 * @param string $datetime Data e hora
 * @return string Tempo decorrido formatado
 */
function timeAgo($datetime) {
    if (empty($datetime)) {
        return '-';
    }
    
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'há ' . $diff . ' segundos';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return 'há ' . $minutes . ' minuto' . ($minutes > 1 ? 's' : '');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return 'há ' . $hours . ' hora' . ($hours > 1 ? 's' : '');
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return 'há ' . $days . ' dia' . ($days > 1 ? 's' : '');
    } else {
        return formatDateBR($datetime);
    }
}

/**
 * Função para truncar texto
 * @param string $text Texto a ser truncado
 * @param int $length Tamanho máximo
 * @param string $suffix Sufixo (padrão: ...)
 * @return string Texto truncado
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Função para gerar slug de URL
 * @param string $text Texto a ser convertido
 * @return string Slug gerado
 */
function generateSlug($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Função para validar CPF
 * @param string $cpf CPF a ser validado
 * @return bool True se válido
 */
function validateCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) != 11) {
        return false;
    }
    
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    
    return true;
}

/**
 * Função para validar CNPJ
 * @param string $cnpj CNPJ a ser validado
 * @return bool True se válido
 */
function validateCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    
    if (strlen($cnpj) != 14) {
        return false;
    }
    
    if (preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }
    
    $length = strlen($cnpj) - 2;
    $numbers = substr($cnpj, 0, $length);
    $digits = substr($cnpj, $length);
    $sum = 0;
    $pos = $length - 7;
    
    for ($i = $length; $i >= 1; $i--, $pos--) {
        if ($pos < 2) {
            $pos = 9;
        }
        $sum += $numbers[$length - $i] * $pos;
    }
    
    $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
    
    if ($result != $digits[0]) {
        return false;
    }
    
    $length = $length + 1;
    $numbers = substr($cnpj, 0, $length);
    $sum = 0;
    $pos = $length - 7;
    
    for ($i = $length; $i >= 1; $i--, $pos--) {
        if ($pos < 2) {
            $pos = 9;
        }
        $sum += $numbers[$length - $i] * $pos;
    }
    
    $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
    
    return $result == $digits[1];
}

/**
 * Função para gerar resposta JSON padronizada
 * @param bool $success Sucesso da operação
 * @param string $message Mensagem
 * @param mixed $data Dados adicionais
 * @param int $http_code Código HTTP
 */
function jsonResponse($success, $message, $data = null, $http_code = 200) {
    http_response_code($http_code);
    header('Content-Type: application/json; charset=utf-8');
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Função para log de ações do sistema
 * @param string $action Ação realizada
 * @param int $user_id ID do usuário
 * @param string $details Detalhes adicionais
 */
function logAction($action, $user_id = null, $details = '') {
    $log_file = __DIR__ . '/logs/system.log';
    $log_dir = dirname($log_file);
    
    // Criar diretório se não existir
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $user_id = $user_id ?? ($_SESSION['user_id'] ?? 'system');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $log_entry = sprintf(
        "[%s] User: %s | IP: %s | Action: %s | Details: %s\n",
        $timestamp,
        $user_id,
        $ip,
        $action,
        $details
    );
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}
?>




