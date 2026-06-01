<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    include 'db.php';
    
    // Verificar se já existem dados
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    $row = $result->fetch_assoc();
    
    if ($row['total'] > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Dados de teste já existem no banco',
            'data' => [
                'usuarios' => $row['total']
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Inserir usuários de teste
    $sql_usuarios = "
        INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES
        ('João Silva', 'joao.silva@email.com', 'senha123', 'aluno'),
        ('Maria Santos', 'maria.santos@email.com', 'senha123', 'aluno'),
        ('Pedro Oliveira', 'pedro.oliveira@email.com', 'senha123', 'aluno'),
        ('Ana Costa', 'ana.costa@email.com', 'senha123', 'aluno'),
        ('Carlos Ferreira', 'carlos.ferreira@email.com', 'senha123', 'aluno'),
        ('Lucia Mendes', 'lucia.mendes@email.com', 'senha123', 'aluno'),
        ('Roberto Alves', 'roberto.alves@email.com', 'senha123', 'aluno'),
        ('Fernanda Lima', 'fernanda.lima@email.com', 'senha123', 'aluno')
    ";
    
    if (!$conn->query($sql_usuarios)) {
        throw new Exception('Erro ao inserir usuários: ' . $conn->error);
    }
    
    // Inserir cursos de teste
    $sql_cursos = "
        INSERT INTO cursos (nome, descricao, carga_horaria) VALUES
        ('Curso de Formação Profissional', 'Curso completo de formação profissional com foco em desenvolvimento de carreira', 40),
        ('Desenvolvimento Web', 'Curso de desenvolvimento web moderno com HTML, CSS, JavaScript e frameworks', 60),
        ('Gestão de Projetos', 'Curso de gestão e liderança de projetos com metodologias ágeis', 50),
        ('Marketing Digital', 'Curso de marketing digital e estratégias de crescimento online', 45),
        ('Design Gráfico', 'Curso de design gráfico e ferramentas de criação visual', 55)
    ";
    
    if (!$conn->query($sql_cursos)) {
        throw new Exception('Erro ao inserir cursos: ' . $conn->error);
    }
    
    // Inserir certificados de teste
    $sql_certificados = "
        INSERT INTO certificados (aluno_id, curso_id, codigo_verificacao, data_emissao, data_conclusao_curso) VALUES
        (1, 1, 'CERT-A1B2C3D4-2024', '2024-01-15', '2024-01-15'),
        (2, 1, 'CERT-E5F6G7H8-2024', '2024-01-20', '2024-01-20'),
        (3, 2, 'CERT-I9J0K1L2-2024', '2024-01-25', '2024-01-25'),
        (4, 2, 'CERT-M3N4O5P6-2024', '2024-02-01', '2024-02-01'),
        (5, 3, 'CERT-Q7R8S9T0-2024', '2024-02-05', '2024-02-05'),
        (6, 3, 'CERT-U1V2W3X4-2024', '2024-02-10', '2024-02-10'),
        (7, 4, 'CERT-Y5Z6A7B8-2024', '2024-02-15', '2024-02-15'),
        (8, 5, 'CERT-C9D0E1F2-2024', '2024-02-20', '2024-02-20')
    ";
    
    if (!$conn->query($sql_certificados)) {
        throw new Exception('Erro ao inserir certificados: ' . $conn->error);
    }
    
    // Contar registros inseridos
    $usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
    $cursos = $conn->query("SELECT COUNT(*) as total FROM cursos")->fetch_assoc()['total'];
    $certificados = $conn->query("SELECT COUNT(*) as total FROM certificados")->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Dados de teste inseridos com sucesso!',
        'data' => [
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

















