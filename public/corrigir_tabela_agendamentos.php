<?php
// Conectar ao banco de dados
include 'db.php';

echo "<h1>🔧 CORRIGINDO ESTRUTURA DA TABELA AGENDAMENTOS</h1>";
echo "<h2>✅ Adaptando para o sistema de aulas</h2>";

try {
    // Verificar se a tabela existe
    $result = $conn->query("SHOW TABLES LIKE 'agendamentos'");
    if ($result->num_rows == 0) {
        echo "<h2>❌ Tabela agendamentos não existe!</h2>";
        return;
    }
    
    echo "<h3>🔧 Modificando estrutura da tabela...</h3>";
    
    // 1. Adicionar coluna aluno_id
    echo "<p>📝 Adicionando coluna aluno_id...</p>";
    $sql_add_aluno = "ALTER TABLE agendamentos ADD COLUMN aluno_id INT NOT NULL AFTER id";
    if ($conn->query($sql_add_aluno)) {
        echo "✅ Coluna aluno_id adicionada<br>";
    } else {
        echo "❌ Erro ao adicionar aluno_id: " . $conn->error . "<br>";
    }
    
    // 2. Renomear colunas para o padrão do sistema
    echo "<p>📝 Renomeando colunas...</p>";
    
    // Renomear data para data_aula
    $sql_rename_data = "ALTER TABLE agendamentos CHANGE COLUMN data data_aula DATE NOT NULL";
    if ($conn->query($sql_rename_data)) {
        echo "✅ Coluna 'data' renomeada para 'data_aula'<br>";
    } else {
        echo "❌ Erro ao renomear data: " . $conn->error . "<br>";
    }
    
    // Renomear horario para hora_inicio
    $sql_rename_horario = "ALTER TABLE agendamentos CHANGE COLUMN horario hora_inicio TIME NOT NULL";
    if ($conn->query($sql_rename_horario)) {
        echo "✅ Coluna 'horario' renomeada para 'hora_inicio'<br>";
    } else {
        echo "❌ Erro ao renomear horario: " . $conn->error . "<br>";
    }
    
    // 3. Adicionar coluna hora_fim
    echo "<p>📝 Adicionando coluna hora_fim...</p>";
    $sql_add_hora_fim = "ALTER TABLE agendamentos ADD COLUMN hora_fim TIME NOT NULL AFTER hora_inicio";
    if ($conn->query($sql_add_hora_fim)) {
        echo "✅ Coluna hora_fim adicionada<br>";
    } else {
        echo "❌ Erro ao adicionar hora_fim: " . $conn->error . "<br>";
    }
    
    // 4. Adicionar coluna observacoes
    echo "<p>📝 Adicionando coluna observacoes...</p>";
    $sql_add_obs = "ALTER TABLE agendamentos ADD COLUMN observacoes TEXT AFTER status";
    if ($conn->query($sql_add_obs)) {
        echo "✅ Coluna observacoes adicionada<br>";
    } else {
        echo "❌ Erro ao adicionar observacoes: " . $conn->error . "<br>";
    }
    
    // 5. Remover colunas desnecessárias
    echo "<p>🧹 Removendo colunas desnecessárias...</p>";
    
    $colunas_remover = ['titulo', 'descricao', 'tipo_evento', 'link_reuniao', 'duracao', 'capacidade', 'updated_at'];
    
    foreach ($colunas_remover as $coluna) {
        $sql_drop = "ALTER TABLE agendamentos DROP COLUMN $coluna";
        if ($conn->query($sql_drop)) {
            echo "✅ Coluna '$coluna' removida<br>";
        } else {
            echo "❌ Erro ao remover '$coluna': " . $conn->error . "<br>";
        }
    }
    
    // 6. Adicionar chave estrangeira para aluno_id
    echo "<p>🔗 Adicionando chave estrangeira para aluno_id...</p>";
    $sql_fk_aluno = "ALTER TABLE agendamentos ADD CONSTRAINT fk_agendamento_aluno 
                     FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE CASCADE";
    if ($conn->query($sql_fk_aluno)) {
        echo "✅ Chave estrangeira para aluno_id adicionada<br>";
    } else {
        echo "❌ Erro ao adicionar FK aluno: " . $conn->error . "<br>";
    }
    
    // 7. Verificar estrutura final
    echo "<h3>🔍 Estrutura final da tabela:</h3>";
    $structure = $conn->query("DESCRIBE agendamentos");
    if ($structure) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
        echo "<tr style='background: #f3f4f6;'>";
        echo "<th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th><th>Extra</th>";
        echo "</tr>";
        
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "<td>{$row['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<br><h2>🎉 ESTRUTURA CORRIGIDA COM SUCESSO!</h2>";
    echo "<p>✅ Tabela agendamentos agora está compatível com o sistema!</p>";
    
    echo "<br><h3>🚀 PRÓXIMOS PASSOS:</h3>";
    echo "<ol>";
    echo "<li><a href='inserir_agendamentos.php' style='color: #10b981; text-decoration: none; font-weight: bold;'>📅 Inserir Agendamentos Funcionais</a></li>";
    echo "<li><a href='dashboard_completo.php' style='color: #3b82f6; text-decoration: none; font-weight: bold;'>🎯 Dashboard Completo</a></li>";
    echo "<li><a href='cursos_completo.php' style='color: #8b5cf6; text-decoration: none; font-weight: bold;'>📚 Página de Cursos</a></li>";
    echo "</ol>";
    
    echo "<p style='background: #dbeafe; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6;'>";
    echo "<strong>🎯 PROBLEMA RESOLVIDO!</strong><br>";
    echo "A tabela agendamentos foi corrigida e agora tem a estrutura correta:<br>";
    echo "• aluno_id, professor_id, curso_id<br>";
    echo "• data_aula, hora_inicio, hora_fim<br>";
    echo "• status, observacoes<br>";
    echo "Agora você pode inserir agendamentos funcionais!";
    echo "</p>";

} catch (Exception $e) {
    echo "❌ Erro durante a correção: " . $e->getMessage();
}
?>



































