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
    <title>Verificar Estrutura Cursos</title>
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
            background: linear-gradient(135deg, #f59e0b, #d97706);
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
            <h1>🔍 Verificar Estrutura Cursos</h1>
            <p>Verificando a estrutura real da tabela cursos</p>
        </div>";

try {
    // 1. VERIFICAR ESTRUTURA DA TABELA CURSOS
    echo "<div class='section'>
        <h2>📋 Estrutura da Tabela Cursos</h2>";
    
    $estrutura = $conn->query("DESCRIBE cursos");
    if ($estrutura && $estrutura->num_rows > 0) {
        echo "<h3>📊 Campos da Tabela Cursos:</h3>";
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
    } else {
        echo "<p class='error'>❌ Erro ao verificar estrutura da tabela cursos</p>";
    }
    echo "</div>";

    // 2. VERIFICAR DADOS DA TABELA CURSOS
    echo "<div class='section'>
        <h2>📚 Dados da Tabela Cursos</h2>";
    
    $cursos = $conn->query("SELECT * FROM cursos LIMIT 5");
    if ($cursos && $cursos->num_rows > 0) {
        echo "<p class='info'>📊 Total de cursos: " . $cursos->num_rows . "</p>";
        
        // Pegar nomes das colunas
        $colunas = [];
        $primeira_linha = $cursos->fetch_assoc();
        if ($primeira_linha) {
            $colunas = array_keys($primeira_linha);
            
            echo "<table class='data-table'>
                <thead>
                    <tr>";
            foreach ($colunas as $coluna) {
                echo "<th>$coluna</th>";
            }
            echo "</tr></thead><tbody>";
            
            // Mostrar primeira linha
            echo "<tr>";
            foreach ($primeira_linha as $valor) {
                echo "<td>$valor</td>";
            }
            echo "</tr>";
            
            // Mostrar outras linhas
            while ($row = $cursos->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $valor) {
                    echo "<td>$valor</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
        }
    } else {
        echo "<p class='error'>❌ Nenhum curso encontrado ou erro na query</p>";
    }
    echo "</div>";

    // 3. CORRIGIR O SCRIPT DE CERTIFICADOS
    echo "<div class='section'>
        <h2>🔧 Correção Necessária</h2>";
    
    echo "<p class='warning'>⚠️ O script adicionar_certificados_teste.php está tentando usar a coluna 'carga_horaria' que não existe!</p>";
    
    echo "<h3>📝 Query que está falhando:</h3>";
    echo "<div style='background: #fef2f2; padding: 10px; border-radius: 6px; font-family: monospace; margin: 10px 0; border-left: 4px solid #ef4444;'>
        SELECT id, nome, carga_horaria FROM cursos WHERE status = 'ativo' LIMIT 3
    </div>";
    
    echo "<h3>✅ Query corrigida (sem carga_horaria):</h3>";
    echo "<div style='background: #dcfce7; padding: 10px; border-radius: 6px; font-family: monospace; margin: 10px 0; border-left: 4px solid #10b981;'>
        SELECT id, nome FROM cursos WHERE status = 'ativo' LIMIT 3
    </div>";
    
    echo "<p class='info'>💡 Vamos corrigir o script para funcionar com a estrutura real da tabela!</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Processo</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='corrigir_script_certificados.php' class='btn'>🔧 Corrigir Script</a>
        <a href='certificados.php' class='btn'>📜 Ir para Certificados</a>
        <a href='dashboard_final.php' class='btn'>🏠 Dashboard</a>
    </div>
</div>
</body>
</html>";
?>









