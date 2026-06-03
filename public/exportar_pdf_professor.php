<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header('HTTP/1.1 403 Forbidden');
    exit('Acesso negado');
}

include 'db.php';

function pdf_escape($text) {
    $text = (string)($text ?? '');
    $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    $text = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
    return $text ?: '';
}

function pdf_text($x, $y, $size, $text) {
    return "BT /F1 {$size} Tf {$x} {$y} Td (" . pdf_escape($text) . ") Tj ET\n";
}

try {
    $professor_id = $_SESSION['user_id'];
    $data_exportacao = date('Y-m-d_H-i-s');

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'");
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $professor = $stmt->get_result()->fetch_assoc();

    if (!$professor) {
        header('HTTP/1.1 404 Not Found');
        exit('Professor não encontrado');
    }

    $stmt = $conn->prepare("SELECT a.*, c.nome as curso_nome, u.nome as aluno_nome
                           FROM agendamentos a
                           JOIN cursos c ON a.curso_id = c.id
                           JOIN usuarios u ON a.aluno_id = u.id
                           WHERE a.professor_id = ?
                           ORDER BY a.data_agendamento DESC
                           LIMIT 12");
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $agendamentos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $total_agendamentos = count($agendamentos);
    $concluidos = count(array_filter($agendamentos, function($a) { return ($a['status'] ?? '') === 'concluido'; }));
    $agendados = count(array_filter($agendamentos, function($a) { return ($a['status'] ?? '') === 'agendado'; }));

    $content = '';
    $content .= pdf_text(50, 790, 22, 'EduConnect - Relatório do Professor');
    $content .= pdf_text(50, 766, 10, 'Exportado em ' . date('d/m/Y H:i'));
    $content .= "0.12 0.23 0.45 rg 50 742 495 2 re f\n";

    $content .= pdf_text(50, 710, 16, 'Dados do Professor');
    $content .= pdf_text(50, 686, 11, 'Nome: ' . ($professor['nome'] ?? '-'));
    $content .= pdf_text(50, 668, 11, 'Email: ' . ($professor['email'] ?? '-'));
    $content .= pdf_text(50, 650, 11, 'Formação: ' . ($professor['formacao'] ?? '-'));
    $content .= pdf_text(50, 632, 11, 'Valor por hora: R$ ' . number_format((float)($professor['valor_hora'] ?? 0), 2, ',', '.'));
    $content .= pdf_text(50, 614, 11, 'Telefone: ' . ($professor['telefone'] ?? '-'));

    $content .= pdf_text(50, 576, 16, 'Resumo');
    $content .= pdf_text(50, 552, 11, 'Agendamentos listados: ' . $total_agendamentos);
    $content .= pdf_text(50, 534, 11, 'Aulas concluídas: ' . $concluidos);
    $content .= pdf_text(50, 516, 11, 'Aulas agendadas: ' . $agendados);

    $content .= pdf_text(50, 478, 16, 'Últimos Agendamentos');
    $y = 452;

    if (empty($agendamentos)) {
        $content .= pdf_text(50, $y, 11, 'Nenhum agendamento encontrado para este professor.');
    } else {
        foreach ($agendamentos as $aula) {
            $data = !empty($aula['data_agendamento']) ? date('d/m/Y', strtotime($aula['data_agendamento'])) : '-';
            $linha1 = ($aula['curso_nome'] ?? '-') . ' - ' . ($aula['aluno_nome'] ?? '-');
            $linha2 = $data . ' às ' . ($aula['hora_inicio'] ?? '-') . ' | Status: ' . ucfirst($aula['status'] ?? '-');
            $content .= pdf_text(50, $y, 10, $linha1);
            $content .= pdf_text(70, $y - 14, 9, $linha2);
            $y -= 36;
            if ($y < 70) {
                break;
            }
        }
    }

    $content .= "0.39 0.45 0.55 rg 50 42 495 1 re f\n";
    $content .= pdf_text(50, 26, 8, 'Relatório gerado automaticamente pelo EduConnect.');

    $objects = [];
    $objects[] = "<< /Type /Catalog /Pages 2 0 R >>";
    $objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
    $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>";
    $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
    $objects[] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "endstream";

    $pdf = "%PDF-1.4\n";
    $offsets = [0];
    foreach ($objects as $index => $object) {
        $offsets[] = strlen($pdf);
        $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
    }

    $xref = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";
    for ($i = 1; $i <= count($objects); $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }
    $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
    $pdf .= "startxref\n" . $xref . "\n%%EOF";

    $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $professor['nome'] ?? 'professor');

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="relatorio_professor_' . $safe_name . '_' . $data_exportacao . '.pdf"');
    header('Content-Length: ' . strlen($pdf));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    echo $pdf;
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    exit('Erro interno do servidor');
}
?>


