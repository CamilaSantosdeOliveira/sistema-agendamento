<?php
/**
 * Instalador do EduConnect
 * Configura automaticamente o banco de dados e verifica a conexão
 */

// Configurações
$config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db_name' => 'educonnect',
    'charset' => 'utf8mb4'
];

// Função para exibir mensagens
function showMessage($message, $type = 'info') {
    $colors = [
        'success' => '#28a745',
        'error' => '#dc3545',
        'warning' => '#ffc107',
        'info' => '#17a2b8'
    ];
    
    $color = $colors[$type] ?? '#6c757d';
    echo "<div style='color: {$color}; margin: 10px 0; padding: 10px; border: 1px solid {$color}; border-radius: 5px;'>";
    echo "<strong>" . strtoupper($type) . ":</strong> {$message}";
    echo "</div>";
}

// Função para testar conexão MySQL
function testMySQLConnection($host, $user, $pass) {
    try {
        $pdo = new PDO("mysql:host={$host}", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        return false;
    }
}

// Função para criar banco de dados
function createDatabase($pdo, $db_name) {
    try {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Função para execututar script SQL
function executeSQLFile($pdo, $db_name, $sql_file) {
    try {
        $pdo->exec("USE `{$db_name}`");
        
        if (!file_exists($sql_file)) {
            return false;
        }
        
        $sql = file_get_contents($sql_file);
        $statements = explode(';', $sql);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Função para verificar requisitos
function checkRequirements() {
    $requirements = [
        'PHP Version (>= 7.4)' => version_compare(PHP_VERSION, '7.4.0', '>='),
        'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
        'JSON Extension' => extension_loaded('json'),
        'cURL Extension' => extension_loaded('curl'),
        'GD Extension' => extension_loaded('gd'),
        'Fileinfo Extension' => extension_loaded('fileinfo')
    ];
    
    $all_ok = true;
    
    foreach ($requirements as $requirement => $status) {
        if ($status) {
            showMessage("✅ {$requirement}: OK", 'success');
        } else {
            showMessage("❌ {$requirement}: FALHOU", 'error');
            $all_ok = false;
        }
    }
    
    return $all_ok;
}

// Função para criar arquivo de configuração
function createConfigFile($config) {
    $config_content = "<?php
/**
 * Configuração do Banco de Dados - EduConnect
 * Sistema de Agendamento de Cursos de Tecnologia
 */

// Configurações do banco de dados
define('DB_HOST', '{$config['host']}');
define('DB_USER', '{$config['user']}');
define('DB_PASS', '{$config['pass']}');
define('DB_NAME', '{$config['db_name']}');
define('DB_CHARSET', '{$config['charset']}');

// Configurações de conexão
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => \"SET NAMES \" . DB_CHARSET
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
    private static \$instance = null;
    private \$connection;
    
    private function __construct() {
        try {
            \$dsn = \"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=\" . DB_CHARSET;
            \$this->connection = new PDO(\$dsn, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException \$e) {
            error_log(\"Erro de conexão com banco: \" . \$e->getMessage());
            throw new Exception(\"Erro ao conectar com o banco de dados\");
        }
    }
    
    public static function getInstance() {
        if (self::\$instance === null) {
            self::\$instance = new self();
        }
        return self::\$instance;
    }
    
    public function getConnection() {
        return \$this->connection;
    }
    
    public function query(\$sql, \$params = []) {
        try {
            \$stmt = \$this->connection->prepare(\$sql);
            \$stmt->execute(\$params);
            return \$stmt;
        } catch (PDOException \$e) {
            error_log(\"Erro na query: \" . \$e->getMessage());
            throw new Exception(\"Erro ao executar consulta no banco\");
        }
    }
    
    public function fetch(\$sql, \$params = []) {
        \$stmt = \$this->query(\$sql, \$params);
        return \$stmt->fetch();
    }
    
    public function fetchAll(\$sql, \$params = []) {
        \$stmt = \$this->query(\$sql, \$params);
        return \$stmt->fetchAll();
    }
    
    public function insert(\$table, \$data) {
        \$fields = array_keys(\$data);
        \$placeholders = ':' . implode(', :', \$fields);
        \$sql = \"INSERT INTO {\$table} (\" . implode(', ', \$fields) . \") VALUES ({\$placeholders})\";
        
        \$this->query(\$sql, \$data);
        return \$this->connection->lastInsertId();
    }
    
    public function update(\$table, \$data, \$where, \$whereParams = []) {
        \$fields = array_map(function(\$field) {
            return \"{\$field} = :{\$field}\";
        }, array_keys(\$data));
        
        \$sql = \"UPDATE {\$table} SET \" . implode(', ', \$fields) . \" WHERE {\$where}\";
        \$params = array_merge(\$data, \$whereParams);
        
        \$stmt = \$this->query(\$sql, \$params);
        return \$stmt->rowCount();
    }
    
    public function delete(\$table, \$where, \$params = []) {
        \$sql = \"DELETE FROM {\$table} WHERE {\$where}\";
        \$stmt = \$this->query(\$sql, \$params);
        return \$stmt->rowCount();
    }
    
    public function beginTransaction() {
        return \$this->connection->beginTransaction();
    }
    
    public function commit() {
        return \$this->connection->commit();
    }
    
    public function rollback() {
        return \$this->connection->rollback();
    }
    
    public function close() {
        \$this->connection = null;
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
function dbQuery(\$sql, \$params = []) {
    \$db = getDB();
    return \$db->query(\$sql, \$params);
}

/**
 * Função helper para buscar um registro
 */
function dbFetch(\$sql, \$params = []) {
    \$db = getDB();
    return \$db->fetch(\$sql, \$params);
}

/**
 * Função helper para buscar todos os registros
 */
function dbFetchAll(\$sql, \$params = []) {
    \$db = getDB();
    return \$db->fetchAll(\$sql, \$params);
}

/**
 * Função helper para inserir dados
 */
function dbInsert(\$table, \$data) {
    \$db = getDB();
    return \$db->insert(\$table, \$data);
}

/**
 * Função helper para atualizar dados
 */
function dbUpdate(\$table, \$data, \$where, \$whereParams = []) {
    \$db = getDB();
    return \$db->update(\$table, \$data, \$where, \$whereParams);
}

/**
 * Função helper para deletar dados
 */
function dbDelete(\$table, \$where, \$params = []) {
    \$db = getDB();
    return \$db->delete(\$table, \$where, \$params);
}
?>";
    
    $config_dir = 'config';
    if (!is_dir($config_dir)) {
        mkdir($config_dir, 0755, true);
    }
    
    return file_put_contents("{$config_dir}/database.php", $config_content);
}

// Função para criar diretórios necessários
function createDirectories() {
    $directories = [
        'uploads',
        'logs',
        'temp'
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                showMessage("✅ Diretório '{$dir}' criado com sucesso", 'success');
            } else {
                showMessage("❌ Erro ao criar diretório '{$dir}'", 'error');
            }
        } else {
            showMessage("✅ Diretório '{$dir}' já existe", 'info');
        }
    }
}

// Função para testar APIs
function testAPIs() {
    $apis = [
        'Dashboard API' => 'api/dashboard_api.php?action=stats',
        'Agendamentos API' => 'api/agendamentos_api.php?action=list'
    ];
    
    foreach ($apis as $name => $url) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if ($data && isset($data['success'])) {
                showMessage("✅ {$name}: Funcionando", 'success');
            } else {
                showMessage("⚠️ {$name}: Resposta inválida", 'warning');
            }
        } else {
            showMessage("❌ {$name}: Erro de conexão", 'error');
        }
    }
}

// Início da instalação
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador EduConnect</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .step {
            background: #e9ecef;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .step h3 {
            margin: 0 0 10px 0;
            color: #495057;
        }
        .form-group {
            margin: 15px 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #495057;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px 5px;
        }
        button:hover {
            background: #0056b3;
        }
        button:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .status {
            margin: 20px 0;
            padding: 20px;
            border-radius: 5px;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Instalador EduConnect</h1>
        <p style="text-align: center; color: #6c757d; margin-bottom: 30px;">
            Sistema de Agendamento de Cursos de Tecnologia
        </p>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="status">
                <h3>📋 Verificação de Requisitos</h3>
                <?php
                if (!checkRequirements()) {
                    showMessage("❌ Alguns requisitos não foram atendidos. Verifique e tente novamente.", 'error');
                    echo '<a href="install.php"><button>Voltar</button></a>';
                    exit;
                }
                showMessage("✅ Todos os requisitos foram atendidos!", 'success');
                ?>

                <h3>🔌 Teste de Conexão MySQL</h3>
                <?php
                $pdo = testMySQLConnection($config['host'], $config['user'], $config['pass']);
                if (!$pdo) {
                    showMessage("❌ Erro ao conectar com MySQL. Verifique as credenciais.", 'error');
                    echo '<a href="install.php"><button>Voltar</button></a>';
                    exit;
                }
                showMessage("✅ Conexão com MySQL estabelecida com sucesso!", 'success');
                ?>

                <h3>🗄️ Criação do Banco de Dados</h3>
                <?php
                if (!createDatabase($pdo, $config['db_name'])) {
                    showMessage("❌ Erro ao criar banco de dados.", 'error');
                    echo '<a href="install.php"><button>Voltar</button></a>';
                    exit;
                }
                showMessage("✅ Banco de dados '{$config['db_name']}' criado com sucesso!", 'success');
                ?>

                <h3>📊 Executando Script SQL</h3>
                <?php
                if (!executeSQLFile($pdo, $config['db_name'], 'database/educonnect_complete.sql')) {
                    showMessage("❌ Erro ao executar script SQL.", 'error');
                    echo '<a href="install.php"><button>Voltar</button></a>';
                    exit;
                }
                showMessage("✅ Script SQL executado com sucesso!", 'success');
                ?>

                <h3>⚙️ Criando Arquivo de Configuração</h3>
                <?php
                if (!createConfigFile($config)) {
                    showMessage("❌ Erro ao criar arquivo de configuração.", 'error');
                } else {
                    showMessage("✅ Arquivo de configuração criado com sucesso!", 'success');
                }
                ?>

                <h3>📁 Criando Diretórios</h3>
                <?php createDirectories(); ?>

                <h3>🧪 Testando APIs</h3>
                <?php testAPIs(); ?>

                <div style="text-align: center; margin: 30px 0;">
                    <h2>🎉 Instalação Concluída com Sucesso!</h2>
                    <p>O EduConnect foi instalado e configurado com sucesso.</p>
                    <p><strong>Credenciais padrão:</strong></p>
                    <p>Email: admin@educonnect.com</p>
                    <p>Senha: password</p>
                    <br>
                    <a href="dashboard.html"><button style="background: #28a745;">Acessar Dashboard</button></a>
                    <a href="index.html"><button style="background: #17a2b8;">Ver Landing Page</button></a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="step">
                    <h3>⚙️ Configurações do Banco de Dados</h3>
                    <div class="form-group">
                        <label for="host">Host MySQL:</label>
                        <input type="text" id="host" name="host" value="<?php echo $config['host']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="user">Usuário MySQL:</label>
                        <input type="text" id="user" name="user" value="<?php echo $config['user']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pass">Senha MySQL:</label>
                        <input type="password" id="pass" name="pass" value="<?php echo $config['pass']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="db_name">Nome do Banco:</label>
                        <input type="text" id="db_name" name="db_name" value="<?php echo $config['db_name']; ?>" required>
                    </div>
                </div>

                <div class="step">
                    <h3>📋 Requisitos do Sistema</h3>
                    <ul>
                        <li>PHP 7.4 ou superior</li>
                        <li>MySQL 5.7 ou superior</li>
                        <li>Extensões PHP: PDO, JSON, cURL, GD, Fileinfo</li>
                        <li>Servidor web (Apache/Nginx)</li>
                    </ul>
                </div>

                <div class="step">
                    <h3>⚠️ Importante</h3>
                    <ul>
                        <li>Certifique-se de que o MySQL está rodando</li>
                        <li>O usuário deve ter permissões para criar bancos de dados</li>
                        <li>Faça backup de dados existentes antes de prosseguir</li>
                        <li>Este instalador criará todas as tabelas necessárias</li>
                    </ul>
                </div>

                <div style="text-align: center; margin: 30px 0;">
                    <button type="submit">🚀 Iniciar Instalação</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>







































