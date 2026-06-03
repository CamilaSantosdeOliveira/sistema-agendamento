<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Configurações do banco
$host = 'localhost';
$user = 'root';
$pass = '';
$port = 3306;

try {
    // Conectar sem especificar banco
    $conn = new mysqli($host, $user, $pass, '', $port);
    
    if ($conn->connect_error) {
        throw new Exception('Erro de conexão: ' . $conn->connect_error);
    }
    
    // Criar banco de dados
    $sql_criar_banco = "CREATE DATABASE IF NOT EXISTS sistema_agendamento";
    if (!$conn->query($sql_criar_banco)) {
        throw new Exception('Erro ao criar banco de dados: ' . $conn->error);
    }
    
    // Selecionar o banco
    $conn->select_db('sistema_agendamento');
    
    // Criar tabela de usuários
    $sql_usuarios = "
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            senha VARCHAR(255) NOT NULL,
            tipo_usuario ENUM('aluno', 'professor', 'admin') DEFAULT 'aluno',
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";
    
    if (!$conn->query($sql_usuarios)) {
        throw new Exception('Erro ao criar tabela usuarios: ' . $conn->error);
    }
    
    // Criar tabela de cursos
    $sql_cursos = "
        CREATE TABLE IF NOT EXISTS cursos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            carga_horaria INT DEFAULT 40,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";
    
    if (!$conn->query($sql_cursos)) {
        throw new Exception('Erro ao criar tabela cursos: ' . $conn->error);
    }
    
    // Criar tabela de certificados
    $sql_certificados = "
        CREATE TABLE IF NOT EXISTS certificados (
            id INT AUTO_INCREMENT PRIMARY KEY,
            aluno_id INT NOT NULL,
            curso_id INT NOT NULL,
            codigo_verificacao VARCHAR(50) UNIQUE NOT NULL,
            data_emissao DATE NOT NULL,
            data_conclusao_curso DATE NOT NULL,
            status ENUM('emitido', 'validado', 'revogado') DEFAULT 'emitido',
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (aluno_id) REFERENCES usuarios(id),
            FOREIGN KEY (curso_id) REFERENCES cursos(id)
        )
    ";
    
    if (!$conn->query($sql_certificados)) {
        throw new Exception('Erro ao criar tabela certificados: ' . $conn->error);
    }
    
    // Verificar se já existem dados
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    $row = $result->fetch_assoc();
    
    if ($row['total'] == 0) {
        // Inserir dados de teste
        $sql_usuarios_teste = "
            INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES
            ('João Silva', 'joao.silva@email.com', 'senha123', 'aluno'),
            ('Maria Santos', 'maria.santos@email.com', 'senha123', 'aluno'),
            ('Pedro Oliveira', 'pedro.oliveira@email.com', 'senha123', 'aluno'),
            ('Ana Costa', 'ana.costa@email.com', 'senha123', 'aluno'),
            ('Carlos Ferreira', 'carlos.ferreira@email.com', 'senha123', 'aluno')
        ";
        
        if (!$conn->query($sql_usuarios_teste)) {
            throw new Exception('Erro ao inserir usuários de teste: ' . $conn->error);
        }
        
        $sql_cursos_teste = "
            INSERT INTO cursos (nome, descricao, carga_horaria) VALUES
            ('Curso de Formação Profissional', 'Curso completo de formação profissional', 40),
            ('Desenvolvimento Web', 'Curso de desenvolvimento web moderno', 60),
            ('Gestão de Projetos', 'Curso de gestão e liderança de projetos', 50)
        ";
        
        if (!$conn->query($sql_cursos_teste)) {
            throw new Exception('Erro ao inserir cursos de teste: ' . $conn->error);
        }
        
        $sql_certificados_teste = "
            INSERT INTO certificados (aluno_id, curso_id, codigo_verificacao, data_emissao, data_conclusao_curso) VALUES
            (1, 1, 'CERT-A1B2C3D4-2024', '2024-01-15', '2024-01-15'),
            (2, 1, 'CERT-E5F6G7H8-2024', '2024-01-20', '2024-01-20'),
            (3, 1, 'CERT-I9J0K1L2-2024', '2024-01-25', '2024-01-25'),
            (4, 1, 'CERT-M3N4O5P6-2024', '2024-02-01', '2024-02-01'),
            (5, 1, 'CERT-Q7R8S9T0-2024', '2024-02-05', '2024-02-05')
        ";
        
        if (!$conn->query($sql_certificados_teste)) {
            throw new Exception('Erro ao inserir certificados de teste: ' . $conn->error);
        }
        
        $mensagem = 'Banco de dados criado com sucesso e dados de teste inseridos!';
    } else {
        $mensagem = 'Banco de dados já existe e contém dados!';
    }
    
    // Contar registros
    $usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
    $cursos = $conn->query("SELECT COUNT(*) as total FROM cursos")->fetch_assoc()['total'];
    $certificados = $conn->query("SELECT COUNT(*) as total FROM certificados")->fetch_assoc()['total'];
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => $mensagem,
        'data' => [
            'banco' => 'sistema_agendamento',
            'usuarios' => $usuarios,
            'cursos' => $cursos,
            'certificados' => $certificados
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>





















