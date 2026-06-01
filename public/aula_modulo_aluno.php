<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'aluno') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['curso_id']) || !isset($_GET['modulo'])) {
    header('Location: meus_cursos_aluno.php');
    exit();
}

include 'db.php';

$aluno_id = $_SESSION['user_id'];
$curso_id = (int)$_GET['curso_id'];
$modulo_num = max(1, (int)$_GET['modulo']);

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'aluno'");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$aluno = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("SELECT * FROM cursos WHERE id = ?");
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();

if (!$curso) {
    header('Location: meus_cursos_aluno.php');
    exit();
}

// Estrutura mock dos módulos e aulas
$modulos = [
    1 => [
        'titulo' => 'Introdução ao Curso',
        'descricao' => 'Visão geral, objetivos e o que você vai aprender ao longo desta jornada.',
        'aulas' => [
            ['titulo' => 'Apresentação do instrutor', 'tipo' => 'video', 'duracao' => '12 min', 'descricao' => 'Conheça quem vai te guiar nesta jornada e o que esperar das próximas aulas.', 'topicos' => ['Quem é o instrutor', 'Experiência profissional', 'Filosofia de ensino']],
            ['titulo' => 'Estrutura do curso', 'tipo' => 'video', 'duracao' => '8 min', 'descricao' => 'Como o curso está organizado, o que cada módulo aborda e dicas para tirar o máximo proveito.', 'topicos' => ['Roteiro dos módulos', 'Tempo estimado por semana', 'Materiais complementares']],
            ['titulo' => 'Recursos e materiais', 'tipo' => 'leitura', 'duracao' => '5 min', 'descricao' => 'Lista de materiais de apoio, ferramentas recomendadas e links úteis para acompanhar o curso.', 'topicos' => ['Ferramentas recomendadas', 'Materiais para download', 'Comunidade do curso']]
        ]
    ],
    2 => [
        'titulo' => 'Fundamentos Básicos',
        'descricao' => 'Conceitos centrais e teoria que sustentam todo o conhecimento adiante.',
        'aulas' => [
            ['titulo' => 'Conceitos centrais', 'tipo' => 'video', 'duracao' => '22 min', 'descricao' => 'Base teórica essencial para entender o restante do conteúdo.', 'topicos' => ['Vocabulário técnico', 'Princípios fundamentais', 'Exemplos práticos']],
            ['titulo' => 'Boas práticas', 'tipo' => 'video', 'duracao' => '18 min', 'descricao' => 'Padrões e convenções adotados pela comunidade profissional.', 'topicos' => ['Padrões de mercado', 'Erros comuns a evitar', 'Checklist de qualidade']],
            ['titulo' => 'Exemplos guiados', 'tipo' => 'pratica', 'duracao' => '30 min', 'descricao' => 'Vamos construir junto exemplos simples para fixar os conceitos.', 'topicos' => ['Exemplo passo a passo', 'Variações do mesmo problema', 'Análise do resultado']],
            ['titulo' => 'Recapitulação', 'tipo' => 'leitura', 'duracao' => '7 min', 'descricao' => 'Resumo do módulo com os principais aprendizados.', 'topicos' => ['Pontos-chave', 'Mapa mental', 'Próximos passos']],
            ['titulo' => 'Quiz de fixação', 'tipo' => 'quiz', 'duracao' => '10 min', 'descricao' => 'Teste seu entendimento com 10 perguntas rápidas.', 'topicos' => ['10 perguntas', 'Feedback imediato', 'Revisão das respostas']]
        ]
    ],
    3 => [
        'titulo' => 'Prática e Exercícios',
        'descricao' => 'Mão na massa: aplicação prática dos conceitos com desafios reais.',
        'aulas' => [
            ['titulo' => 'Exercícios guiados', 'tipo' => 'pratica', 'duracao' => '45 min', 'descricao' => 'Resolva exercícios com instrutor explicando passo a passo.', 'topicos' => ['5 exercícios', 'Solução comentada', 'Variações sugeridas']],
            ['titulo' => 'Desafios práticos', 'tipo' => 'pratica', 'duracao' => '60 min', 'descricao' => 'Resolva sozinho desafios mais complexos.', 'topicos' => ['3 desafios', 'Tempo livre', 'Solução disponível']],
            ['titulo' => 'Code review', 'tipo' => 'video', 'duracao' => '20 min', 'descricao' => 'Análise de soluções enviadas pela turma.', 'topicos' => ['Boas soluções', 'O que melhorar', 'Padrões observados']],
            ['titulo' => 'Discussão em grupo', 'tipo' => 'leitura', 'duracao' => '15 min', 'descricao' => 'Compartilhe sua solução e dê feedback nos colegas.', 'topicos' => ['Fórum de discussão', 'Regras de feedback', 'Exemplo de bom feedback']]
        ]
    ],
    4 => [
        'titulo' => 'Projeto Final',
        'descricao' => 'Desenvolvimento do projeto que vai compor seu portfólio.',
        'aulas' => [
            ['titulo' => 'Briefing do projeto', 'tipo' => 'video', 'duracao' => '15 min', 'descricao' => 'Entenda o escopo, requisitos e critérios de avaliação.', 'topicos' => ['Escopo do projeto', 'Requisitos técnicos', 'Critérios de aprovação']],
            ['titulo' => 'Implementação guiada', 'tipo' => 'pratica', 'duracao' => '90 min', 'descricao' => 'Acompanhe o instrutor construindo o projeto base.', 'topicos' => ['Setup inicial', 'Implementação core', 'Refinamento']],
            ['titulo' => 'Apresentação', 'tipo' => 'video', 'duracao' => '20 min', 'descricao' => 'Aprenda a apresentar seu projeto de forma profissional.', 'topicos' => ['Storytelling técnico', 'Demonstração', 'Q&A']],
            ['titulo' => 'Certificado de conclusão', 'tipo' => 'leitura', 'duracao' => '5 min', 'descricao' => 'Como gerar e baixar seu certificado oficial.', 'topicos' => ['Validação automática', 'Download em PDF', 'Compartilhar no LinkedIn']]
        ]
    ]
];

if (!isset($modulos[$modulo_num])) {
    header("Location: detalhes_curso_aluno.php?id={$curso_id}");
    exit();
}

$modulo = $modulos[$modulo_num];
$total_modulos = count($modulos);
$aulas = $modulo['aulas'];
$total_aulas = count($aulas);

$aula_atual = max(1, min((int)($_GET['aula'] ?? 1), $total_aulas));
$aula = $aulas[$aula_atual - 1];

$tipo_icons = [
    'video' => ['icon' => 'fa-play-circle', 'label' => 'Vídeo'],
    'leitura' => ['icon' => 'fa-book-open', 'label' => 'Leitura'],
    'pratica' => ['icon' => 'fa-code', 'label' => 'Prática'],
    'quiz' => ['icon' => 'fa-question-circle', 'label' => 'Quiz']
];
$tipoInfo = $tipo_icons[$aula['tipo']] ?? $tipo_icons['video'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect — <?php echo htmlspecialchars($aula['titulo']); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary-color: #1e3a8a;
            --primary-dark: #0f172a;
            --primary-light: #2563eb;
            --success-color: #059669;
            --success-light: #10b981;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --border-light: #f1f5f9;
            --font-family-base: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--font-family-base);
            background:
                radial-gradient(circle at 8% 4%, rgba(59, 130, 246, 0.12), transparent 24%),
                radial-gradient(circle at 92% 8%, rgba(37, 99, 235, 0.08), transparent 26%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #eef2ff 100%);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Top bar (header escuro) */
        .top-bar {
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 32px;
            background:
                radial-gradient(circle at 8% 18%, rgba(255, 255, 255, 0.18), transparent 30%),
                linear-gradient(135deg, #0f172a 0%, #1e3a8a 58%, #2563eb 100%);
            color: #ffffff;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.18);
        }
        .top-bar a.back-link {
            display: inline-flex; align-items: center; gap: 10px;
            color: #ffffff;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            font-weight: 700;
            font-size: 0.875rem;
            transition: all 0.25s;
        }
        .top-bar a.back-link:hover { background: rgba(255, 255, 255, 0.2); transform: translateX(-2px); }
        .top-bar .breadcrumb {
            flex: 1;
            min-width: 0;
            display: flex; flex-direction: column;
            gap: 2px;
        }
        .top-bar .breadcrumb .crumb {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 700;
        }
        .top-bar .breadcrumb h1 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .top-bar .global-progress {
            display: flex; flex-direction: column; align-items: flex-end; gap: 6px;
            min-width: 200px;
        }
        .top-bar .global-progress .label {
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .top-bar .global-progress .bar {
            width: 200px;
            height: 6px;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            overflow: hidden;
        }
        .top-bar .global-progress .bar .fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #34d399);
            width: 0%;
            transition: width 0.4s ease;
        }

        /* Layout */
        .layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
            padding: 24px 32px 80px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Sidebar de aulas */
        .lessons-panel {
            position: sticky;
            top: 96px;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 22px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08);
            padding: 20px;
        }
        .lessons-panel-title {
            display: flex; align-items: center; gap: 10px;
            font-size: 0.78rem;
            font-weight: 800;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 8px;
        }
        .lessons-panel-subtitle {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }
        .lesson-list { list-style: none; display: flex; flex-direction: column; gap: 6px; }
        .lesson-item {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid transparent;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.25s;
            cursor: pointer;
            background: transparent;
        }
        .lesson-item:hover {
            background: rgba(37, 99, 235, 0.06);
            border-color: rgba(37, 99, 235, 0.18);
            transform: translateX(2px);
        }
        .lesson-item.active {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.08), rgba(37, 99, 235, 0.08));
            border-color: rgba(37, 99, 235, 0.28);
            color: var(--text-primary);
        }
        .lesson-check {
            width: 22px; height: 22px;
            display: grid; place-items: center;
            border: 2px solid rgba(148, 163, 184, 0.6);
            border-radius: 50%;
            color: transparent;
            font-size: 0.65rem;
            background: #ffffff;
            transition: all 0.25s;
        }
        .lesson-item.done .lesson-check {
            background: linear-gradient(135deg, #059669, #10b981);
            border-color: #059669;
            color: #ffffff;
        }
        .lesson-item.done .lesson-title { text-decoration: line-through; color: var(--text-muted); }
        .lesson-title {
            font-size: 0.88rem;
            font-weight: 700;
            color: inherit;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .lesson-meta {
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 2px;
            display: flex; align-items: center; gap: 6px;
        }
        .lesson-tipo-icon {
            font-size: 0.78rem;
            color: var(--primary-light);
            width: 22px; text-align: center;
        }
        .lesson-item.active .lesson-tipo-icon { color: var(--primary-color); }

        /* Conteúdo principal */
        .content {
            display: flex; flex-direction: column; gap: 22px;
            min-width: 0;
        }
        .lesson-header {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 22px;
            padding: 26px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08);
        }
        .lesson-tipo-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 12px;
        }
        .lesson-title-main {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.04em;
            margin-bottom: 8px;
            line-height: 1.2;
        }
        .lesson-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.65;
            font-weight: 500;
        }
        .lesson-info-row {
            display: flex; gap: 18px; flex-wrap: wrap;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid var(--border-light);
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 600;
        }
        .lesson-info-row span { display: inline-flex; align-items: center; gap: 8px; }
        .lesson-info-row i { color: var(--primary-light); }

        /* Player placeholder */
        .player {
            position: relative;
            aspect-ratio: 16 / 9;
            background:
                radial-gradient(circle at 30% 30%, rgba(37, 99, 235, 0.5), transparent 50%),
                radial-gradient(circle at 70% 70%, rgba(16, 185, 129, 0.4), transparent 50%),
                linear-gradient(135deg, #0f172a, #1e3a8a 60%, #2563eb);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 28px 80px rgba(15, 23, 42, 0.2);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: transform 0.35s;
        }
        .player:hover { transform: scale(1.005); }
        .player::after {
            content: '';
            position: absolute; inset: 0;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.06) 1px, transparent 1px),
                linear-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px);
            background-size: 48px 48px;
            opacity: 0.5;
        }
        .player-play {
            position: relative; z-index: 1;
            width: 96px; height: 96px;
            display: grid; place-items: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            color: var(--primary-color);
            font-size: 2rem;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.3);
            transition: all 0.3s;
        }
        .player:hover .player-play { transform: scale(1.08); }
        .player-label {
            position: absolute;
            bottom: 18px; left: 18px;
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 700;
            background: rgba(0, 0, 0, 0.4);
            padding: 6px 12px;
            border-radius: 999px;
            backdrop-filter: blur(8px);
            z-index: 1;
            letter-spacing: 0.05em;
        }

        /* Tópicos */
        .topics-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 22px;
            padding: 26px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08);
        }
        .topics-card h3 {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 14px;
            display: flex; align-items: center; gap: 10px;
        }
        .topics-card h3 i {
            width: 32px; height: 32px;
            display: grid; place-items: center;
            border-radius: 10px;
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-light);
            font-size: 0.85rem;
        }
        .topics-list { list-style: none; display: grid; gap: 8px; }
        .topics-list li {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            background: rgba(248, 250, 252, 0.7);
            border: 1px solid var(--border-light);
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 600;
        }
        .topics-list li i {
            color: var(--success-light);
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        /* Footer actions */
        .action-bar {
            display: flex; justify-content: space-between; align-items: center; gap: 14px;
            flex-wrap: wrap;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 22px;
            padding: 18px 22px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08);
        }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 22px;
            border-radius: 999px;
            font-size: 0.88rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            font-family: var(--font-family-base);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: #ffffff;
            box-shadow: 0 14px 28px rgba(30, 58, 138, 0.25);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 18px 36px rgba(30, 58, 138, 0.32); }
        .btn-success {
            background: linear-gradient(135deg, var(--success-color), var(--success-light));
            color: #ffffff;
            box-shadow: 0 14px 28px rgba(5, 150, 105, 0.25);
        }
        .btn-success:hover { transform: translateY(-2px); }
        .btn-ghost {
            background: rgba(248, 250, 252, 0.9);
            color: var(--primary-color);
            border: 1px solid rgba(37, 99, 235, 0.25);
        }
        .btn-ghost:hover { background: rgba(37, 99, 235, 0.08); }
        .btn-disabled {
            background: rgba(226, 232, 240, 0.6);
            color: #94a3b8;
            cursor: not-allowed;
        }
        .nav-buttons { display: flex; gap: 10px; }
        .marcar-btn.is-done {
            background: linear-gradient(135deg, #059669, #10b981);
            color: #ffffff;
        }

        /* Toast (reaproveitado) */
        .toast-container {
            position: fixed; bottom: 24px; right: 24px; z-index: 13000;
            display: flex; flex-direction: column; gap: 12px; max-width: 400px;
            pointer-events: none;
        }
        .toast {
            background: #ffffff; padding: 16px 20px; border-radius: 14px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.14);
            border-left: 4px solid var(--primary-light);
            display: flex; align-items: center; gap: 12px;
            animation: aulaToastIn 0.3s ease-out;
            pointer-events: auto;
            min-width: 300px;
            position: relative;
            overflow: hidden;
        }
        .toast::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, #1e3a8a, #2563eb);
            transform: scaleX(0);
            transform-origin: left;
            animation: aulaToastBar 3s linear forwards;
        }
        .toast.success { border-left-color: var(--success-color); }
        .toast.success::before { background: linear-gradient(90deg, #059669, #10b981); }
        .toast.info { border-left-color: var(--primary-light); }
        .toast-icon { font-size: 1.4rem; color: var(--primary-light); }
        .toast.success .toast-icon { color: var(--success-color); }
        .toast-content { flex: 1; }
        .toast-title { font-weight: 800; color: var(--text-primary); font-size: 0.95rem; margin-bottom: 2px; }
        .toast-message { font-size: 0.85rem; color: var(--text-secondary); font-weight: 500; }
        .toast-close { background: transparent; border: none; color: #94a3b8; cursor: pointer; font-size: 1rem; }
        @keyframes aulaToastIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes aulaToastBar { to { transform: scaleX(1); } }

        @media (max-width: 1024px) {
            .layout { grid-template-columns: 1fr; padding: 20px; }
            .lessons-panel { position: static; max-height: none; }
        }
        @media (max-width: 640px) {
            .top-bar { padding: 14px 18px; flex-wrap: wrap; }
            .top-bar .global-progress { min-width: 100%; align-items: flex-start; }
            .lesson-title-main { font-size: 1.4rem; }
            .action-bar { flex-direction: column; align-items: stretch; }
            .nav-buttons { width: 100%; justify-content: space-between; }
            .btn { justify-content: center; }
        }
    </style>
</head>
<body data-curso="<?php echo $curso_id; ?>" data-modulo="<?php echo $modulo_num; ?>" data-total="<?php echo $total_aulas; ?>">
    <header class="top-bar">
        <a href="detalhes_curso_aluno.php?id=<?php echo $curso_id; ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar ao curso
        </a>
        <div class="breadcrumb">
            <div class="crumb">Módulo <?php echo $modulo_num; ?> de <?php echo $total_modulos; ?> · <?php echo htmlspecialchars($curso['nome']); ?></div>
            <h1><?php echo htmlspecialchars($modulo['titulo']); ?></h1>
        </div>
        <div class="global-progress">
            <span class="label"><span id="progressLabel">0 de <?php echo $total_aulas; ?></span> aulas concluídas</span>
            <div class="bar"><div class="fill" id="progressFill"></div></div>
        </div>
    </header>

    <div class="layout">
        <aside class="lessons-panel">
            <div class="lessons-panel-title"><i class="fas fa-list-check"></i> Aulas do módulo</div>
            <div class="lessons-panel-subtitle"><?php echo htmlspecialchars($modulo['titulo']); ?></div>
            <ul class="lesson-list">
                <?php foreach ($aulas as $i => $a):
                    $n = $i + 1;
                    $ti = $tipo_icons[$a['tipo']] ?? $tipo_icons['video'];
                ?>
                    <li>
                        <a class="lesson-item <?php echo $n === $aula_atual ? 'active' : ''; ?>"
                           data-aula="<?php echo $n; ?>"
                           href="aula_modulo_aluno.php?curso_id=<?php echo $curso_id; ?>&modulo=<?php echo $modulo_num; ?>&aula=<?php echo $n; ?>">
                            <span class="lesson-check"><i class="fas fa-check"></i></span>
                            <span>
                                <span class="lesson-title"><?php echo $n; ?>. <?php echo htmlspecialchars($a['titulo']); ?></span>
                                <span class="lesson-meta">
                                    <i class="fas <?php echo $ti['icon']; ?>"></i> <?php echo $ti['label']; ?> · <?php echo $a['duracao']; ?>
                                </span>
                            </span>
                            <i class="fas <?php echo $ti['icon']; ?> lesson-tipo-icon"></i>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <main class="content">
            <section class="lesson-header">
                <span class="lesson-tipo-badge"><i class="fas <?php echo $tipoInfo['icon']; ?>"></i> <?php echo $tipoInfo['label']; ?></span>
                <h2 class="lesson-title-main"><?php echo $aula_atual; ?>. <?php echo htmlspecialchars($aula['titulo']); ?></h2>
                <p class="lesson-subtitle"><?php echo htmlspecialchars($aula['descricao']); ?></p>
                <div class="lesson-info-row">
                    <span><i class="fas fa-clock"></i> <?php echo $aula['duracao']; ?></span>
                    <span><i class="fas fa-layer-group"></i> Aula <?php echo $aula_atual; ?> de <?php echo $total_aulas; ?></span>
                    <span><i class="fas fa-book"></i> Módulo <?php echo $modulo_num; ?></span>
                </div>
            </section>

            <div class="player" onclick="iniciarPlayer()">
                <div class="player-play"><i class="fas fa-play"></i></div>
                <div class="player-label"><i class="fas fa-circle" style="font-size: 0.5rem; color: #ef4444;"></i> &nbsp;Player placeholder · <?php echo $aula['duracao']; ?></div>
            </div>

            <?php if (!empty($aula['topicos'])): ?>
                <section class="topics-card">
                    <h3><i class="fas fa-bookmark"></i> O que você vai aprender</h3>
                    <ul class="topics-list">
                        <?php foreach ($aula['topicos'] as $t): ?>
                            <li><i class="fas fa-check"></i><?php echo htmlspecialchars($t); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <div class="action-bar">
                <button type="button" class="btn btn-success marcar-btn" id="marcarBtn" onclick="toggleConcluido()">
                    <i class="fas fa-check-circle"></i> <span id="marcarLabel">Marcar como concluído</span>
                </button>
                <div class="nav-buttons">
                    <?php if ($aula_atual > 1): ?>
                        <a class="btn btn-ghost" href="aula_modulo_aluno.php?curso_id=<?php echo $curso_id; ?>&modulo=<?php echo $modulo_num; ?>&aula=<?php echo $aula_atual - 1; ?>">
                            <i class="fas fa-arrow-left"></i> Aula anterior
                        </a>
                    <?php else: ?>
                        <span class="btn btn-disabled"><i class="fas fa-arrow-left"></i> Aula anterior</span>
                    <?php endif; ?>

                    <?php if ($aula_atual < $total_aulas): ?>
                        <a class="btn btn-primary" href="aula_modulo_aluno.php?curso_id=<?php echo $curso_id; ?>&modulo=<?php echo $modulo_num; ?>&aula=<?php echo $aula_atual + 1; ?>">
                            Próxima aula <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <a class="btn btn-primary" href="detalhes_curso_aluno.php?id=<?php echo $curso_id; ?>">
                            Concluir módulo <i class="fas fa-flag-checkered"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        const cursoId = <?php echo $curso_id; ?>;
        const moduloNum = <?php echo $modulo_num; ?>;
        const totalAulas = <?php echo $total_aulas; ?>;
        const aulaAtual = <?php echo $aula_atual; ?>;
        const storageKey = 'aluno_progresso_curso_' + cursoId + '_modulo_' + moduloNum;

        function getProgresso() {
            try { return JSON.parse(localStorage.getItem(storageKey) || '[]'); }
            catch(e) { return []; }
        }
        function saveProgresso(arr) {
            localStorage.setItem(storageKey, JSON.stringify(arr));
        }
        function isDone(n) { return getProgresso().includes(n); }
        function toggleConcluido() {
            const arr = getProgresso();
            const idx = arr.indexOf(aulaAtual);
            if (idx >= 0) {
                arr.splice(idx, 1);
                saveProgresso(arr);
                showToast('Marcação removida', 'Aula desmarcada como concluída.', 'info');
            } else {
                arr.push(aulaAtual);
                saveProgresso(arr);
                showToast('Aula concluída!', 'Parabéns, continue assim. ' + arr.length + '/' + totalAulas + ' aulas completas.', 'success');
            }
            renderState();
        }
        function renderState() {
            const arr = getProgresso();
            // Lista lateral
            document.querySelectorAll('.lesson-item').forEach(el => {
                const n = parseInt(el.dataset.aula);
                if (arr.includes(n)) el.classList.add('done');
                else el.classList.remove('done');
            });
            // Botão atual
            const btn = document.getElementById('marcarBtn');
            const lbl = document.getElementById('marcarLabel');
            if (arr.includes(aulaAtual)) {
                btn.classList.add('is-done');
                lbl.textContent = 'Concluída · clique para desfazer';
            } else {
                btn.classList.remove('is-done');
                lbl.textContent = 'Marcar como concluído';
            }
            // Barra topo
            const pct = (arr.length / totalAulas) * 100;
            document.getElementById('progressFill').style.width = pct + '%';
            document.getElementById('progressLabel').textContent = arr.length + ' de ' + totalAulas;
        }
        function iniciarPlayer() {
            showToast('Player em breve', 'A reprodução do vídeo será habilitada em uma próxima entrega.', 'info');
        }
        function showToast(title, message, type) {
            type = type || 'info';
            const c = document.getElementById('toastContainer');
            const t = document.createElement('div');
            t.className = 'toast ' + type;
            const icons = { success: 'fa-check-circle', info: 'fa-info-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle' };
            t.innerHTML = '<i class="fas ' + (icons[type] || icons.info) + ' toast-icon"></i>' +
                '<div class="toast-content"><div class="toast-title">' + title + '</div><div class="toast-message">' + message + '</div></div>' +
                '<button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>';
            c.appendChild(t);
            setTimeout(() => { if (t.parentElement) { t.style.animation = 'aulaToastIn 0.3s ease-out reverse'; setTimeout(() => t.remove(), 300); } }, 3500);
        }

        document.addEventListener('DOMContentLoaded', renderState);
    </script>
</body>
</html>
