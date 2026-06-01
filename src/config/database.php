<?php
/**
 * Configuração do Banco de Dados
 * Sistema de Agendamento
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'sistema_agendamento';
    private $username = 'root';
    private $password = '';
    private $conn;

    /**
     * Conecta ao banco de dados
     */
    public function conectar() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            echo "Erro de conexão: " . $e->getMessage();
        }

        return $this->conn;
    }
}

/**
 * Configurações gerais do sistema
 */
define('SITE_URL', 'http://localhost/xampp/htdocs/Sistema%20De%20Agendamento/public/');
define('API_URL', 'http://localhost/xampp/htdocs/Sistema%20De%20Agendamento/src/api/');

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Headers para API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Trata requisições OPTIONS (CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
