<?php
echo "<h2>🔍 Debug do Agendamento</h2>";

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>📝 Dados recebidos via POST:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    // Verificar dados brutos
    $raw_input = file_get_contents('php://input');
    echo "<h3>📄 Dados brutos:</h3>";
    echo "<pre>" . htmlspecialchars($raw_input) . "</pre>";
    
    // Verificar headers
    echo "<h3>📋 Headers:</h3>";
    echo "<pre>" . print_r(getallheaders(), true) . "</pre>";
    
} else {
    echo "<h3>📋 Simular dados do formulário:</h3>";
    
    // Simular dados que podem estar sendo enviados
    $dados_possiveis = [
        'nome' => 'João Silva',
        'email' => 'joao@teste.com',
        'telefone' => '(11) 99999-9999',
        'professor' => 'Maria Santos',
        'data' => '2024-12-25',
        'hora' => '14:00',
        'servico' => 'Curso de PHP',
        'observacoes' => 'Teste'
    ];
    
    echo "<p>Dados que deveriam ser enviados:</p>";
    echo "<pre>" . json_encode($dados_possiveis, JSON_PRETTY_PRINT) . "</pre>";
    
    echo "<h3>🎯 Teste de envio:</h3>";
    echo "<form method='POST' action='debug_agendamento.php'>";
    echo "<input type='hidden' name='nome' value='João Silva'>";
    echo "<input type='hidden' name='email' value='joao@teste.com'>";
    echo "<input type='hidden' name='telefone' value='(11) 99999-9999'>";
    echo "<input type='hidden' name='professor' value='Maria Santos'>";
    echo "<input type='hidden' name='data' value='2024-12-25'>";
    echo "<input type='hidden' name='hora' value='14:00'>";
    echo "<input type='hidden' name='servico' value='Curso de PHP'>";
    echo "<input type='hidden' name='observacoes' value='Teste'>";
    echo "<button type='submit'>Testar Envio</button>";
    echo "</form>";
}

echo "<br><h3>🎯 Próximo passo:</h3>";
echo "<p>Com essas informações, posso corrigir exatamente o problema!</p>";
?>




