<?php
session_start();
header('Content-Type: application/json');

// Verificar se o usuário está logado e é professor
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'professor') {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit();
}

include 'db.php';

try {
    // Receber dados JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit();
    }

    $professor_id = $_SESSION['user_id'];
    
    // Converter booleanos para inteiros (0 ou 1)
    $notificacoes_aulas = $input['notificacoes_aulas'] ? 1 : 0;
    $lembretes_aulas = $input['lembretes_aulas'] ? 1 : 0;
    $relatorios_semanais = $input['relatorios_semanais'] ? 1 : 0;
    $notificacoes_email = $input['notificacoes_email'] ? 1 : 0;

    // Verificar se já existe registro de preferências
    $stmt = $conn->prepare("SELECT id FROM preferencias_professor WHERE professor_id = ?");
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Atualizar preferências existentes
        $stmt = $conn->prepare("UPDATE preferencias_professor SET 
            notificacoes_aulas = ?, 
            lembretes_aulas = ?, 
            relatorios_semanais = ?, 
            notificacoes_email = ?,
            atualizado_em = NOW()
            WHERE professor_id = ?");
        $stmt->bind_param("iiiii", $notificacoes_aulas, $lembretes_aulas, $relatorios_semanais, $notificacoes_email, $professor_id);
    } else {
        // Criar novas preferências
        $stmt = $conn->prepare("INSERT INTO preferencias_professor 
            (professor_id, notificacoes_aulas, lembretes_aulas, relatorios_semanais, notificacoes_email, criado_em, atualizado_em) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("iiiii", $professor_id, $notificacoes_aulas, $lembretes_aulas, $relatorios_semanais, $notificacoes_email);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Preferências salvas com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar preferências']);
    }

} catch (Exception $e) {
    // Se a tabela não existir, criar ela
    if (strpos($e->getMessage(), "doesn't exist") !== false) {
        $sql = "CREATE TABLE IF NOT EXISTS preferencias_professor (
            id INT AUTO_INCREMENT PRIMARY KEY,
            professor_id INT NOT NULL,
            notificacoes_aulas TINYINT(1) DEFAULT 1,
            lembretes_aulas TINYINT(1) DEFAULT 1,
            relatorios_semanais TINYINT(1) DEFAULT 0,
            notificacoes_email TINYINT(1) DEFAULT 1,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE CASCADE
        )";
        
        if ($conn->query($sql)) {
            // Tentar salvar novamente
            $stmt = $conn->prepare("INSERT INTO preferencias_professor 
                (professor_id, notificacoes_aulas, lembretes_aulas, relatorios_semanais, notificacoes_email) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiii", $professor_id, $notificacoes_aulas, $lembretes_aulas, $relatorios_semanais, $notificacoes_email);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Preferências salvas com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao salvar preferências']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao criar tabela de preferências']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
    }
}
?>






