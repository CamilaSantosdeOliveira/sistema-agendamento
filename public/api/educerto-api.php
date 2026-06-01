<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration (adjust according to your XAMPP setup)
$config = [
    'host' => 'localhost',
    'dbname' => 'educerto',
    'username' => 'root',
    'password' => ''
];

try {
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['dbname']}", 
                   $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If database doesn't exist or connection fails, use file-based storage
    $pdo = null;
}

// Get request data
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$action = isset($_GET['action']) ? $_GET['action'] : (isset($input['action']) ? $input['action'] : '');

// Response function
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit();
}

// File-based storage functions (fallback when no database)
function saveToFile($filename, $data) {
    $dataDir = __DIR__ . '/data/';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    file_put_contents($dataDir . $filename . '.json', json_encode($data, JSON_PRETTY_PRINT));
}

function loadFromFile($filename) {
    $filePath = __DIR__ . '/data/' . $filename . '.json';
    if (file_exists($filePath)) {
        return json_decode(file_get_contents($filePath), true);
    }
    return [];
}

// Função para enviar email de confirmação de agendamento
function sendScheduleEmail($email, $classData, $username) {
    try {
        // Configurações de email (ajuste conforme seu servidor SMTP)
        $to = $email;
        $subject = "✅ Confirmação de Agendamento - EduCerto";
        
        $message = "
        <html>
        <head>
            <title>Confirmação de Agendamento</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .class-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
                .btn { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📚 EduCerto</h1>
                    <h2>Confirmação de Agendamento</h2>
                </div>
                <div class='content'>
                    <p>Olá <strong>" . htmlspecialchars($username) . "</strong>,</p>
                    
                    <p>Sua aula foi agendada com sucesso! Aqui estão os detalhes:</p>
                    
                    <div class='class-info'>
                        <h3>📅 Detalhes da Aula</h3>
                        <p><strong>📚 Matéria:</strong> " . htmlspecialchars($classData['subject']) . "</p>
                        <p><strong>📅 Data:</strong> " . htmlspecialchars($classData['date']) . "</p>
                        <p><strong>⏰ Horário:</strong> " . htmlspecialchars($classData['time']) . "</p>
                        <p><strong>👨‍🏫 Professor:</strong> " . htmlspecialchars($classData['teacher'] ?? 'A definir') . "</p>
                        " . (isset($classData['description']) && $classData['description'] ? "<p><strong>📝 Descrição:</strong> " . htmlspecialchars($classData['description']) . "</p>" : "") . "
                    </div>
                    
                    <p>🔔 <strong>Lembrete:</strong> Você receberá uma notificação 15 minutos antes do início da aula.</p>
                    
                    <div style='text-align: center;'>
                        <a href='http://localhost/Sistema%20De%20Agendamento/public/educerto.html' class='btn'>🚀 Acessar Sistema</a>
                    </div>
                    
                    <p>Se você não pode comparecer, por favor cancele com antecedência através do sistema.</p>
                </div>
                <div class='footer'>
                    <p>Este é um email automático do sistema EduCerto v3.0</p>
                    <p>Data de envio: " . date('d/m/Y H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>";

        // Headers para HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: EduCerto Sistema <noreply@educerto.com>" . "\r\n";
        $headers .= "Reply-To: suporte@educerto.com" . "\r\n";

        // Tentar enviar email
        $emailSent = mail($to, $subject, $message, $headers);
        
        // Log do email (para debug)
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'to' => $email,
            'subject' => $subject,
            'class_data' => $classData,
            'success' => $emailSent,
            'error' => $emailSent ? null : error_get_last()
        ];
        
        $logFile = __DIR__ . '/data/email_log.json';
        $existingLog = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];
        $existingLog[] = $logData;
        file_put_contents($logFile, json_encode($existingLog, JSON_PRETTY_PRINT));
        
        return $emailSent;
        
    } catch (Exception $e) {
        error_log("Erro ao enviar email: " . $e->getMessage());
        return false;
    }
}

// Handle different actions
switch ($action) {
    case 'login':
        // Validate user credentials
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';
        
        // Default users (in production, these should be in database)
        $validUsers = [
            'admin' => ['password' => 'admin123', 'name' => 'Administrador', 'role' => 'admin'],
            'prof' => ['password' => 'prof123', 'name' => 'Professor', 'role' => 'professor'],
            'aluno' => ['password' => 'aluno123', 'name' => 'Estudante', 'role' => 'student'],
            'teste' => ['password' => '123', 'name' => 'Usuário Teste', 'role' => 'test']
        ];
        
        if (isset($validUsers[$username]) && $validUsers[$username]['password'] === $password) {
            $user = $validUsers[$username];
            unset($user['password']); // Don't send password back
            $user['username'] = $username;
            $user['loginTime'] = date('Y-m-d H:i:s');
            
            sendResponse([
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'user' => $user
            ]);
        } else {
            sendResponse([
                'success' => false,
                'message' => 'Usuário ou senha incorretos'
            ], 401);
        }
        break;
        
    case 'save_schedule':
        // Save class schedule
        $schedule = $input['schedule'] ?? [];
        $username = $input['username'] ?? 'guest';
        $userEmail = $input['email'] ?? '';
        
        // Salvar no arquivo
        saveToFile('schedule_' . $username, $schedule);
        
        // Enviar email de confirmação se email fornecido
        if ($userEmail && !empty($schedule)) {
            $lastClass = end($schedule);
            $emailSent = sendScheduleEmail($userEmail, $lastClass, $username);
            
            sendResponse([
                'success' => true,
                'message' => 'Agenda salva com sucesso!' . ($emailSent ? ' Email de confirmação enviado!' : ''),
                'emailSent' => $emailSent
            ]);
        } else {
            sendResponse([
                'success' => true,
                'message' => 'Agenda salva com sucesso!'
            ]);
        }
        break;
        
    case 'schedule_single_class':
        // Agendar uma única aula
        $classData = [
            'id' => uniqid(),
            'subject' => $input['subject'] ?? '',
            'date' => $input['date'] ?? '',
            'time' => $input['time'] ?? '',
            'duration' => $input['duration'] ?? 60,
            'teacher' => $input['teacher'] ?? '',
            'description' => $input['description'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'scheduled'
        ];
        
        $username = $input['username'] ?? 'guest';
        $userEmail = $input['email'] ?? '';
        
        // Carregar agenda existente
        $existingSchedule = loadFromFile('schedule_' . $username);
        $existingSchedule[] = $classData;
        
        // Salvar agenda atualizada
        saveToFile('schedule_' . $username, $existingSchedule);
        
        // Enviar email de confirmação
        $emailSent = false;
        if ($userEmail) {
            $emailSent = sendScheduleEmail($userEmail, $classData, $username);
        }
        
        sendResponse([
            'success' => true,
            'message' => 'Aula agendada com sucesso!' . ($emailSent ? ' Email de confirmação enviado!' : ''),
            'classData' => $classData,
            'emailSent' => $emailSent
        ]);
        break;
        
    case 'load_schedule':
        // Load class schedule
        $username = $_GET['username'] ?? 'guest';
        $schedule = loadFromFile('schedule_' . $username);
        
        sendResponse([
            'success' => true,
            'schedule' => $schedule
        ]);
        break;
        
    case 'save_profile':
        // Save user profile
        $profile = $input['profile'] ?? [];
        $username = $input['username'] ?? 'guest';
        saveToFile('profile_' . $username, $profile);
        
        sendResponse([
            'success' => true,
            'message' => 'Perfil salvo com sucesso!'
        ]);
        break;
        
    case 'load_profile':
        // Load user profile
        $username = $_GET['username'] ?? 'guest';
        $profile = loadFromFile('profile_' . $username);
        
        sendResponse([
            'success' => true,
            'profile' => $profile
        ]);
        break;
        
    case 'get_analytics':
        // Get analytics data
        $analytics = [
            'totalClasses' => rand(150, 300),
            'totalStudents' => rand(500, 1000),
            'completionRate' => rand(75, 95),
            'monthlyGrowth' => rand(5, 25),
            'weeklyData' => [
                'labels' => ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                'datasets' => [
                    [
                        'label' => 'Aulas Ministradas',
                        'data' => [rand(10, 30), rand(15, 35), rand(20, 40), rand(25, 45), rand(15, 35), rand(5, 15), rand(0, 10)]
                    ]
                ]
            ],
            'monthlyData' => [
                'labels' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                'datasets' => [
                    [
                        'label' => 'Novos Alunos',
                        'data' => [rand(50, 100), rand(60, 120), rand(70, 140), rand(80, 160), rand(90, 180), rand(100, 200)]
                    ]
                ]
            ]
        ];
        
        sendResponse([
            'success' => true,
            'analytics' => $analytics
        ]);
        break;
        
    case 'send_message':
        // Save message
        $messages = loadFromFile('messages');
        $message = [
            'id' => uniqid(),
            'from' => $input['from'] ?? 'unknown',
            'to' => $input['to'] ?? 'all',
            'message' => $input['message'] ?? '',
            'timestamp' => date('Y-m-d H:i:s'),
            'read' => false
        ];
        
        $messages[] = $message;
        saveToFile('messages', $messages);
        
        sendResponse([
            'success' => true,
            'message' => 'Mensagem enviada com sucesso!'
        ]);
        break;
        
    case 'get_messages':
        // Get messages
        $messages = loadFromFile('messages');
        $username = $_GET['username'] ?? '';
        
        // Filter messages for user
        if ($username) {
            $messages = array_filter($messages, function($msg) use ($username) {
                return $msg['to'] === $username || $msg['to'] === 'all' || $msg['from'] === $username;
            });
        }
        
        sendResponse([
            'success' => true,
            'messages' => array_values($messages)
        ]);
        break;
        
    case 'export_data':
        // Export user data
        $username = $_GET['username'] ?? 'guest';
        $data = [
            'profile' => loadFromFile('profile_' . $username),
            'schedule' => loadFromFile('schedule_' . $username),
            'exported_at' => date('Y-m-d H:i:s')
        ];
        
        sendResponse([
            'success' => true,
            'data' => $data,
            'filename' => 'educerto_export_' . $username . '_' . date('Y-m-d') . '.json'
        ]);
        break;
        
    default:
        sendResponse([
            'success' => false,
            'message' => 'Ação não reconhecida'
        ], 400);
}
?>
