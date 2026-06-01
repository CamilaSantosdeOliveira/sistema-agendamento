<?php
// Teste simples da API de certificados
header('Content-Type: application/json');

// Simular dados POST
$_POST['action'] = 'emitir_certificado_individual';
$_POST['aluno_id'] = 10;
$_POST['curso_id'] = 1;
$_POST['data_conclusao'] = date('Y-m-d');

// Simular dados JSON
$json_data = json_encode([
    'action' => 'emitir_certificado_individual',
    'aluno_id' => 10,
    'curso_id' => 1,
    'data_conclusao' => date('Y-m-d')
]);

// Incluir a API
ob_start();
include 'api/certificados.php';
$output = ob_get_clean();

echo $output;
?>







