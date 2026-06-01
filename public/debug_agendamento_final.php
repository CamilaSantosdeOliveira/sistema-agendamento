<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>🔍 Debug Final do Agendamento</h2>";

// Simular dados que o formulário envia
$dados_teste = [
    'nome' => 'João Silva',
    'email' => 'joao@teste.com',
    'telefone' => '(11) 99999-9999',
    'professor' => 'Maria Santos',
    'data' => '2024-12-25',
    'hora' => '14:00',
    'servico' => 'Curso de PHP',
    'observacoes' => 'Teste final'
];

echo "<h3>📝 Dados que serão enviados:</h3>";
echo "<pre>" . print_r($dados_teste, true) . "</pre>";

echo "<h3>🔧 Testando agendar_direto.php:</h3>";

try {
    // Simular POST
    $_POST = $dados_teste;
    
    ob_start();
    include 'agendar_direto.php';
    $output = ob_get_clean();
    
    echo "<h4>📄 Resposta da API:</h4>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    $data = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($data['success']) && $data['success']) {
            echo "<h4>✅ SUCESSO!</h4>";
            echo "<p>Agendamento funcionando perfeitamente!</p>";
            echo "<p>ID criado: " . $data['id'] . "</p>";
        } else {
            echo "<h4>❌ ERRO</h4>";
            echo "<p>Erro: " . ($data['message'] ?? 'Erro desconhecido') . "</p>";
            if (isset($data['debug'])) {
                echo "<h5>Debug:</h5>";
                echo "<pre>" . print_r($data['debug'], true) . "</pre>";
            }
        }
    } else {
        echo "<h4>❌ ERRO JSON</h4>";
        echo "<p>Erro ao decodificar JSON: " . json_last_error_msg() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<h4>❌ EXCEÇÃO</h4>";
    echo "<p>Erro: " . $e->getMessage() . "</p>";
}

echo "<br><h3>🎯 Próximo passo:</h3>";
echo "<p>Se o teste passou, o dashboard deve funcionar!</p>";
echo "<p><a href='dashboard_corrigido.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 TESTAR DASHBOARD</a></p>";
?>


