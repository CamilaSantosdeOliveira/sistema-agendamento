<?php
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conectar ao banco de dados
include 'db.php';

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verificar Estrutura de Certificados</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f5f5f5; 
            margin: 0;
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border-radius: 8px;
        }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .info { color: #3b82f6; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        .section { 
            margin: 20px 0; 
            padding: 20px; 
            border: 1px solid #e5e7eb; 
            border-radius: 8px;
            background: #f8fafc;
        }
        .btn {
            background: #3b82f6;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover {
            background: #2563eb;
        }
        .btn-success {
            background: #10b981;
        }
        .btn-success:hover {
            background: #059669;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .data-table th, .data-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .data-table th {
            background: #f9fafb;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔍 Verificar Estrutura de Certificados</h1>
            <p>Verificando se a tabela certificados existe no banco de dados</p>
        </div>";

try {
    // 1. VERIFICAR SE A TABELA CERTIFICADOS EXISTE
    echo "<div class='section'>
        <h2>📋 Verificar Tabela Certificados</h2>";
    
    $tabela_certificados = $conn->query("SHOW TABLES LIKE 'certificados'");
    if ($tabela_certificados && $tabela_certificados->num_rows > 0) {
        echo "<p class='success'>✅ Tabela 'certificados' existe no banco de dados!</p>";
        
        // Verificar estrutura da tabela
        $estrutura = $conn->query("DESCRIBE certificados");
        if ($estrutura && $estrutura->num_rows > 0) {
            echo "<h3>📊 Estrutura da Tabela Certificados:</h3>";
            echo "<table class='data-table'>
                <thead>
                    <tr>
                        <th>Campo</th>
                        <th>Tipo</th>
                        <th>Nulo</th>
                        <th>Chave</th>
                        <th>Padrão</th>
                        <th>Extra</th>
                    </tr>
                </thead>
                <tbody>";
            
            while ($row = $estrutura->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['Field']}</td>
                        <td>{$row['Type']}</td>
                        <td>{$row['Null']}</td>
                        <td>{$row['Key']}</td>
                        <td>{$row['Default']}</td>
                        <td>{$row['Extra']}</td>
                      </tr>";
            }
            echo "</tbody></table>";
        }
        
        // Verificar quantos certificados existem
        $total_certificados = $conn->query("SELECT COUNT(*) as total FROM certificados");
        $count = $total_certificados ? $total_certificados->fetch_assoc()['total'] : 0;
        echo "<p class='info'>📊 Total de certificados na tabela: $count</p>";
        
    } else {
        echo "<p class='error'>❌ Tabela 'certificados' NÃO existe no banco de dados!</p>";
        echo "<p class='warning'>Criando tabela certificados...</p>";
        
        $sql_criar_tabela = "CREATE TABLE IF NOT EXISTS certificados (
            id INT AUTO_INCREMENT PRIMARY KEY,
            aluno_id INT NOT NULL,
            curso_id INT NOT NULL,
            codigo_verificacao VARCHAR(50) UNIQUE NOT NULL,
            data_emissao DATE NOT NULL,
            data_conclusao DATE NOT NULL,
            status ENUM('emitido', 'validado', 'revogado') DEFAULT 'emitido',
            carga_horaria VARCHAR(50),
            observacoes TEXT,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (aluno_id) REFERENCES usuarios(id),
            FOREIGN KEY (curso_id) REFERENCES cursos(id)
        )";
        
        if ($conn->query($sql_criar_tabela)) {
            echo "<p class='success'>✅ Tabela 'certificados' criada com sucesso!</p>";
        } else {
            echo "<p class='error'>❌ Erro ao criar tabela certificados: " . $conn->error . "</p>";
        }
    }
    echo "</div>";

    // 2. VERIFICAR TODAS AS TABELAS DO SISTEMA
    echo "<div class='section'>
        <h2>🗄️ Todas as Tabelas do Sistema</h2>";
    
    $todas_tabelas = $conn->query("SHOW TABLES");
    if ($todas_tabelas && $todas_tabelas->num_rows > 0) {
        echo "<p class='info'>📊 Tabelas encontradas no banco:</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>Nome da Tabela</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>";
        
        $tabelas_necessarias = ['usuarios', 'cursos', 'professores', 'certificados', 'inscricoes', 'agendamentos'];
        
        while ($row = $todas_tabelas->fetch_assoc()) {
            $tabela = array_values($row)[0];
            $status = in_array($tabela, $tabelas_necessarias) ? 'success' : 'info';
            $status_text = in_array($tabela, $tabelas_necessarias) ? '✅ Necessária' : 'ℹ️ Sistema';
            
            echo "<tr>
                    <td>$tabela</td>
                    <td class='$status'>$status_text</td>
                  </tr>";
        }
        echo "</tbody></table>";
    }
    echo "</div>";

    // 3. VERIFICAR DADOS NECESSÁRIOS
    echo "<div class='section'>
        <h2>📊 Verificar Dados Necessários</h2>";
    
    $alunos = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'")->fetch_assoc()['total'];
    $cursos = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'")->fetch_assoc()['total'];
    $certificados = $conn->query("SELECT COUNT(*) as total FROM certificados")->fetch_assoc()['total'];
    
    echo "<div style='background: #dcfce7; padding: 15px; border-radius: 8px; margin: 10px 0;'>
        <h3>📈 Resumo dos Dados:</h3>
        <p><strong>Alunos:</strong> $alunos</p>
        <p><strong>Cursos Ativos:</strong> $cursos</p>
        <p><strong>Certificados:</strong> $certificados</p>
    </div>";
    
    if ($alunos >= 3 && $cursos >= 3) {
        echo "<p class='success'>🎉 Sistema pronto para criar certificados!</p>";
    } else {
        echo "<p class='error'>❌ Sistema precisa de mais dados</p>";
        if ($alunos < 3) echo "<p class='warning'>⚠️ Precisa de pelo menos 3 alunos (tem $alunos)</p>";
        if ($cursos < 3) echo "<p class='warning'>⚠️ Precisa de pelo menos 3 cursos (tem $cursos)</p>";
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Processo</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='adicionar_certificados_teste.php' class='btn btn-success'>🎓 Criar Certificados</a>
        <a href='certificados.php' class='btn'>📜 Ir para Certificados</a>
        <a href='dashboard_final.php' class='btn'>🏠 Dashboard</a>
    </div>
</div>
</body>
</html>";
?>







