<?php
// Corrige mojibake (UTF-8 lido como Latin-1 e re-encodado)
$targets = [
    'detalhes_curso_aluno.php',
    'meus_cursos_aluno.php',
    'minhas_aulas_aluno.php',
    'certificados_aluno.php',
    'perfil_aluno.php',
    'dashboard_aluno.php',
    'buscar_cursos_aluno.php',
];

// Pares mojibake => correto. As chaves estão expressas como bytes UTF-8
// que representam a sequência mojibake quando vista como UTF-8.
$map = [
    "\xC3\x83\xC2\xA7" => "\xC3\xA7", // ç
    "\xC3\x83\xC2\xA3" => "\xC3\xA3", // ã
    "\xC3\x83\xC2\xA1" => "\xC3\xA1", // á
    "\xC3\x83\xC2\xA9" => "\xC3\xA9", // é
    "\xC3\x83\xC2\xAD" => "\xC3\xAD", // í
    "\xC3\x83\xC2\xB3" => "\xC3\xB3", // ó
    "\xC3\x83\xC2\xBA" => "\xC3\xBA", // ú
    "\xC3\x83\xC2\xA2" => "\xC3\xA2", // â
    "\xC3\x83\xC2\xAA" => "\xC3\xAA", // ê
    "\xC3\x83\xC2\xB4" => "\xC3\xB4", // ô
    "\xC3\x83\xC2\xA0" => "\xC3\xA0", // à
    "\xC3\x83\xC2\xBC" => "\xC3\xBC", // ü
    "\xC3\x83\xE2\x80\xA1" => "\xC3\x87", // Ç
    "\xC3\x83\xE2\x80\xB0" => "\xC3\x89", // É
    "\xC3\x83\xE2\x80\x9C" => "\xC3\x93", // Ó
    "\xC3\x83\xC5\xA0" => "\xC3\x8A", // Ê
    "\xC3\x83\xE2\x80\x9D" => "\xC3\x94", // Ô
    "\xC3\x83\xE2\x80\x93" => "\xC3\x93", // alt
    "\xC3\x83\xC5\xA1" => "\xC3\x9A", // Ú
    "\xC3\x83\xE2\x80\x9A" => "\xC3\x82", // Â
    // Emoji 📚 mojibake (ðŸ"š)
    "\xC3\xB0\xC5\xB8\xE2\x80\x9C\xC5\xA1" => "\xF0\x9F\x93\x9A",
    // Emoji 📖
    "\xC3\xB0\xC5\xB8\xE2\x80\x9C\xE2\x80\x93" => "\xF0\x9F\x93\x96",
    // Emoji 🎓
    "\xC3\xB0\xC5\xB8\xC5\xBD\xE2\x80\x9C" => "\xF0\x9F\x8E\x93",
    // Emoji 📊
    "\xC3\xB0\xC5\xB8\xE2\x80\x9C\xC5\xA0" => "\xF0\x9F\x93\x8A",
    // Emoji 📅
    "\xC3\xB0\xC5\xB8\xE2\x80\x9C\xE2\x80\xA6" => "\xF0\x9F\x93\x85",
    // Emoji 👥
    "\xC3\xB0\xC5\xB8\xE2\x80\x99\xC2\xA5" => "\xF0\x9F\x91\xA5",
];

foreach ($targets as $f) {
    $p = __DIR__ . '/public/' . $f;
    if (!file_exists($p)) { echo "MISSING $f\n"; continue; }
    $c = file_get_contents($p);
    $orig = $c;
    foreach ($map as $bad => $good) {
        $c = str_replace($bad, $good, $c);
    }
    if ($c !== $orig) {
        file_put_contents($p, $c);
        echo "FIXED $f\n";
    } else {
        echo "CLEAN $f\n";
    }
}
