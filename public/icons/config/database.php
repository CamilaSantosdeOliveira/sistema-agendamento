<?php
/**
 * Configuração do Banco de Dados - EduConnect
 * Sistema de Agendamento de Cursos de Tecnologia
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'educonnect');
define('DB_CHARSET', 'utf8mb4');

// Configurações de conexão
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
]);

// Configurações de sessão
define('SESSION_LIFETIME', 3600); // 1 hora
define('SESSION_NAME', 'educonnect_session');

// Configurações de segurança
define('HASH_COST', 12); // Custo do bcrypt
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutos

// Configurações de upload
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);
define('UPLOAD_PATH', '../uploads/');

// Configurações de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'seu-email@gmail.com');
define('SMTP_PASS', 'sua-senha-app');
define('SMTP_SECURE', 'tls');

// Configurações do sistema
define('SYSTEM_NAME', 'EduConnect');
define('SYSTEM_VERSION', '2.0.0');
define('SYSTEM_URL', 'http://localhost/Sistema%20De%20Agendamento/public/');

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

/**
 * Classe de conexão com o banco de dados
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            error_log("Erro de conexão com banco: " . $e->getMessage());
            throw new Exception("Erro ao conectar com o banco de dados");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erro na query: " . $e->getMessage());
            throw new Exception("Erro ao executar consulta no banco");
        }
    }
    
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") VALUES ({$placeholders})";
        
        $this->query($sql, $data);
        return $this->connection->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $fields = array_map(function($field) {
            return "{$field} = :{$field}";
        }, array_keys($data));
        
        $sql = "UPDATE {$table} SET " . implode(', ', $fields) . " WHERE {$where}";
        $params = array_merge($data, $whereParams);
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        return $this->connection->rollback();
    }
    
    public function close() {
        $this->connection = null;
    }
}

/**
 * Função helper para obter conexão
 */
function getDB() {
    return Database::getInstance();
}

/**
 * Função helper para executar queries simples
 */
function dbQuery($sql, $params = []) {
    $db = getDB();
    return $db->query($sql, $params);
}

/**
 * Função helper para buscar um registro
 */
function dbFetch($sql, $params = []) {
    $db = getDB();
    return $db->fetch($sql, $params);
}

/**
 * Função helper para buscar todos os registros
 */
function dbFetchAll($sql, $params = []) {
    $db = getDB();
    return $db->fetchAll($sql, $params);
}

/**
 * Função helper para inserir dados
 */
function dbInsert($table, $data) {
    $db = getDB();
    return $db->insert($table, $data);
}

/**
 * Função helper para atualizar dados
 */
function dbUpdate($table, $data, $where, $whereParams = []) {
    $db = getDB();
    return $db->update($table, $data, $where, $whereParams);
}

/**
 * Função helper para deletar dados
 */
function dbDelete($table, $where, $params = []) {
    $db = getDB();
    return $db->delete($table, $where, $params);
}

// Teste de conexão (remover em produção)
if (isset($_GET['test_db'])) {
    try {
        $db = getDB();
        echo "✅ Conexão com banco estabelecida com sucesso!";
        echo "<br>Versão do MySQL: " . $db->getConnection()->getAttribute(PDO::ATTR_SERVER_VERSION);
    } catch (Exception $e) {
        echo "❌ Erro na conexão: " . $e->getMessage();
    }
}
?>
