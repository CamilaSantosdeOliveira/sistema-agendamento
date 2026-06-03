<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'aluno') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['agendamento_id'])) {
    header('Location: minhas_aulas_aluno.php');
    exit();
}

include 'db.php';

$aluno_id = $_SESSION['user_id'];
$agendamento_id = (int)$_GET['agendamento_id'];

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'aluno'");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$aluno = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare(
    "SELECT a.*, c.nome AS curso_nome, c.id AS curso_id, c.duracao_horas,
            u.nome AS professor_nome, u.id AS professor_id
     FROM agendamentos a
     JOIN cursos c ON a.curso_id = c.id
     JOIN usuarios u ON a.professor_id = u.id
     WHERE a.id = ? AND a.aluno_id = ?"
);
$stmt->bind_param("ii", $agendamento_id, $aluno_id);
$stmt->execute();
$aula = $stmt->get_result()->fetch_assoc();

if (!$aula) {
    header('Location: minhas_aulas_aluno.php');
    exit();
}

$inicio_ts = strtotime($aula['data_agendamento'] . ' ' . $aula['hora_inicio']);
$agora = time();
$ja_comecou = $inicio_ts <= $agora;
$diff = abs($inicio_ts - $agora);

// Participantes mock
$participantes = [
    ['nome' => $aula['professor_nome'], 'role' => 'Professor', 'inicial' => mb_substr($aula['professor_nome'], 0, 1), 'live' => true],
    ['nome' => $aluno['nome'], 'role' => 'Você', 'inicial' => mb_substr($aluno['nome'], 0, 1), 'live' => true, 'you' => true],
    ['nome' => 'Lucas Almeida', 'role' => 'Aluno', 'inicial' => 'L', 'live' => true],
    ['nome' => 'Marina Costa', 'role' => 'Aluno', 'inicial' => 'M', 'live' => true],
    ['nome' => 'Rafael Souza', 'role' => 'Aluno', 'inicial' => 'R', 'live' => false],
    ['nome' => 'Beatriz Lima', 'role' => 'Aluno', 'inicial' => 'B', 'live' => true],
];

// Mensagens mock iniciais
$mensagens = [
    ['autor' => $aula['professor_nome'], 'role' => 'Professor', 'texto' => 'Olá pessoal! Bem-vindos à aula de hoje. Em instantes vamos começar.', 'minutos' => 5],
    ['autor' => 'Lucas Almeida', 'role' => 'Aluno', 'texto' => 'Boa noite, professor!', 'minutos' => 4],
    ['autor' => 'Marina Costa', 'role' => 'Aluno', 'texto' => 'Estou animada para a aula 🚀', 'minutos' => 3],
    ['autor' => $aula['professor_nome'], 'role' => 'Professor', 'texto' => 'Hoje vamos cobrir o tema principal do módulo. Tenham caderno e papel à mão.', 'minutos' => 2],
    ['autor' => 'Beatriz Lima', 'role' => 'Aluno', 'texto' => 'Pode compartilhar a tela com os slides?', 'minutos' => 1],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aula ao vivo — <?php echo htmlspecialchars($aula['curso_nome']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e3a8a;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg: #0b1220;
            --surface: #111a2e;
            --surface-2: #1a2540;
            --border: rgba(255, 255, 255, 0.08);
            --text: #e2e8f0;
            --text-muted: #94a3b8;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex; flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Top bar */
        .live-topbar {
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px;
            padding: 14px 24px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }
        .live-topbar .left {
            display: flex; align-items: center; gap: 16px;
            min-width: 0;
        }
        .live-topbar .back {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: var(--text);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            transition: all 0.25s;
        }
        .live-topbar .back:hover { background: rgba(255, 255, 255, 0.14); }
        .live-topbar .info { min-width: 0; }
        .live-topbar .info .curso {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 2px;
        }
        .live-topbar .info h1 {
            font-size: 1.05rem;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 460px;
        }
        .live-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .live-badge.live { background: rgba(239, 68, 68, 0.18); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.35); }
        .live-badge.soon { background: rgba(245, 158, 11, 0.18); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.35); }
        .live-dot { width: 8px; height: 8px; border-radius: 50%; background: #ef4444; animation: pulse 1.6s infinite; }
        .live-badge.soon .live-dot { background: #f59e0b; }
        @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.3); } }
        .timer {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            font-variant-numeric: tabular-nums;
        }
        .timer i { color: var(--primary); }

        /* Main grid */
        .live-stage {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 20px;
            padding: 20px;
            min-height: 0;
        }

        /* Video area */
        .video-wrap {
            display: flex; flex-direction: column; gap: 16px;
            min-width: 0;
            min-height: 0;
        }
        .video-stage {
            position: relative;
            flex: 1;
            min-height: 380px;
            border-radius: 18px;
            overflow: hidden;
            background:
                radial-gradient(circle at 30% 30%, rgba(37, 99, 235, 0.4), transparent 50%),
                radial-gradient(circle at 70% 75%, rgba(16, 185, 129, 0.3), transparent 50%),
                linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
        }
        .video-stage::after {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 48px 48px;
            opacity: 0.5;
        }
        .video-center {
            position: relative; z-index: 1;
            text-align: center;
            color: #ffffff;
        }
        .video-center .avatar {
            width: 140px; height: 140px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            display: grid; place-items: center;
            font-size: 3.5rem;
            font-weight: 800;
            color: #ffffff;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.4), inset 0 0 0 4px rgba(255, 255, 255, 0.2);
        }
        .video-center h2 {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }
        .video-center p {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.95rem;
            font-weight: 500;
        }
        .video-overlay-top {
            position: absolute;
            top: 16px; left: 16px;
            display: flex; gap: 8px;
            z-index: 2;
        }
        .video-overlay-top .chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(10px);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 700;
        }
        .video-overlay-top .chip i { color: #ef4444; }
        .video-overlay-bottom {
            position: absolute;
            bottom: 16px; left: 16px;
            z-index: 2;
            display: flex; align-items: center; gap: 10px;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(12px);
            padding: 8px 14px;
            border-radius: 14px;
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 700;
        }

        /* Controls */
        .live-controls {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            padding: 14px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            flex-wrap: wrap;
        }
        .control-btn {
            width: 52px; height: 52px;
            display: grid; place-items: center;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: var(--surface-2);
            color: var(--text);
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.25s;
            position: relative;
        }
        .control-btn:hover { background: rgba(255, 255, 255, 0.08); transform: translateY(-2px); }
        .control-btn.muted { background: rgba(239, 68, 68, 0.2); color: #fca5a5; border-color: rgba(239, 68, 68, 0.4); }
        .control-btn.leave {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #ffffff;
            border-color: transparent;
            width: auto;
            padding: 0 22px;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 800;
            display: inline-flex; gap: 8px; align-items: center;
            box-shadow: 0 14px 28px rgba(239, 68, 68, 0.4);
        }
        .control-btn.leave:hover { transform: translateY(-2px); box-shadow: 0 18px 40px rgba(239, 68, 68, 0.5); }
        .control-tooltip {
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.9);
            color: #fff;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 0.72rem;
            font-weight: 700;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }
        .control-btn:hover .control-tooltip { opacity: 1; }

        /* Sidebar */
        .live-sidebar {
            display: flex; flex-direction: column;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            min-height: 0;
        }
        .tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid var(--border);
        }
        .tab-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 14px;
            font-family: inherit;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            cursor: pointer;
            transition: all 0.25s;
            border-bottom: 2px solid transparent;
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        }
        .tab-btn.active { color: #ffffff; border-bottom-color: var(--primary); background: rgba(37, 99, 235, 0.08); }
        .tab-btn:hover:not(.active) { color: var(--text); }
        .tab-btn .count {
            background: rgba(255, 255, 255, 0.1);
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.7rem;
        }
        .tab-pane { flex: 1; display: none; flex-direction: column; min-height: 0; }
        .tab-pane.active { display: flex; }

        /* Chat */
        .chat-messages {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            display: flex; flex-direction: column; gap: 14px;
            min-height: 200px;
        }
        .chat-messages::-webkit-scrollbar { width: 6px; }
        .chat-messages::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.12); border-radius: 999px; }
        .message {
            display: flex; gap: 10px;
            animation: msgIn 0.3s ease-out;
        }
        .message-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: grid; place-items: center;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: #fff;
            font-weight: 800;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .message.prof .message-avatar { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .message-content { flex: 1; min-width: 0; }
        .message-head {
            display: flex; gap: 8px; align-items: baseline;
            margin-bottom: 4px;
        }
        .message-author {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text);
        }
        .message.prof .message-author { color: #fcd34d; }
        .message-time {
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 600;
        }
        .message-text {
            font-size: 0.88rem;
            color: var(--text);
            line-height: 1.5;
            word-wrap: break-word;
        }
        @keyframes msgIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .chat-input {
            display: flex; gap: 8px; align-items: center;
            padding: 12px;
            border-top: 1px solid var(--border);
            background: var(--surface-2);
        }
        .chat-input input {
            flex: 1;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 10px 16px;
            color: var(--text);
            font-family: inherit;
            font-size: 0.88rem;
            outline: none;
            transition: all 0.25s;
        }
        .chat-input input:focus { border-color: var(--primary); background: rgba(255, 255, 255, 0.08); }
        .chat-input button {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            border: none;
            color: #fff;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.25s;
        }
        .chat-input button:hover { transform: translateY(-2px) scale(1.05); box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4); }

        /* Participants */
        .participants-list {
            padding: 12px;
            display: flex; flex-direction: column; gap: 6px;
            overflow-y: auto;
        }
        .participant {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .participant:hover { background: rgba(255, 255, 255, 0.06); border-color: var(--border); }
        .participant.you { border-color: rgba(37, 99, 235, 0.4); background: rgba(37, 99, 235, 0.08); }
        .participant-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            display: grid; place-items: center;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: #fff;
            font-weight: 800;
            font-size: 0.95rem;
            flex-shrink: 0;
            position: relative;
        }
        .participant.prof .participant-avatar { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .participant-avatar .status-dot {
            position: absolute;
            bottom: -2px; right: -2px;
            width: 12px; height: 12px;
            border-radius: 50%;
            border: 2px solid var(--surface);
            background: #64748b;
        }
        .participant.live .participant-avatar .status-dot { background: #10b981; }
        .participant-info { flex: 1; min-width: 0; }
        .participant-name {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .participant-role {
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* Toast */
        .toast-container {
            position: fixed; top: 24px; right: 24px; left: 24px; z-index: 13000;
            display: flex; flex-direction: column; gap: 12px; max-width: 400px;
            pointer-events: none;
            margin: 0 auto;
        }
        .toast {
            background: var(--surface);
            color: var(--text);
            padding: 14px 18px;
            border-radius: 14px;
            box-shadow: 0 24px 56px rgba(0, 0, 0, 0.4);
            border-left: 4px solid var(--primary);
            display: flex; gap: 12px; align-items: center;
            animation: slideIn 0.3s ease-out;
            pointer-events: auto;
            min-width: 280px;
        }
        .toast.success { border-left-color: var(--success); }
        .toast.warning { border-left-color: var(--warning); }
        .toast.error { border-left-color: var(--danger); }
        .toast-icon { font-size: 1.3rem; color: var(--primary); }
        .toast.success .toast-icon { color: var(--success); }
        .toast.warning .toast-icon { color: var(--warning); }
        .toast.error .toast-icon { color: var(--danger); }
        .toast-content { flex: 1; }
        .toast-title { font-size: 0.9rem; font-weight: 800; color: var(--text); margin-bottom: 2px; }
        .toast-message { font-size: 0.82rem; color: var(--text-muted); }
        @keyframes slideIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

        /* Modal de Confirmação */
        .confirm-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 14000;
            align-items: center;
            justify-content: center;
        }
        .confirm-modal.active {
            display: flex;
        }
        .confirm-modal-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
        }
        .confirm-modal-content {
            position: relative;
            background: var(--surface);
            border-radius: 20px;
            padding: 40px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: modalSlideIn 0.3s ease-out;
        }
        @keyframes modalSlideIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .confirm-modal-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .confirm-modal-icon i {
            font-size: 1.75rem;
            color: white;
        }
        .confirm-modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 12px;
        }
        .confirm-modal-message {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .confirm-modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .confirm-modal-btn {
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }
        .confirm-modal-btn-cancel {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .confirm-modal-btn-cancel:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .confirm-modal-btn-confirm {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        .confirm-modal-btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
        }

        @media (max-width: 960px) {
            .live-stage { grid-template-columns: 1fr; }
            .live-sidebar { min-height: 360px; }
        }
        @media (max-width: 640px) {
            .live-topbar { flex-wrap: wrap; padding: 12px 16px; }
            .live-topbar .info h1 { max-width: 220px; font-size: 0.95rem; }
            .live-stage { padding: 12px; gap: 12px; }
            .video-center .avatar { width: 90px; height: 90px; font-size: 2.2rem; }
            .video-center h2 { font-size: 1.15rem; }
            .control-btn { width: 44px; height: 44px; }
        }
    </style>
</head>
<body>
    <header class="live-topbar">
        <div class="left">
            <a href="minhas_aulas_aluno.php" class="back"><i class="fas fa-arrow-left"></i> Sair</a>
            <div class="info">
                <div class="curso"><?php echo htmlspecialchars($aula['curso_nome']); ?></div>
                <h1>Aula com <?php echo htmlspecialchars($aula['professor_nome']); ?></h1>
            </div>
        </div>
        <span class="live-badge <?php echo $ja_comecou ? 'live' : 'soon'; ?>" id="liveBadge">
            <span class="live-dot"></span>
            <?php echo $ja_comecou ? 'Ao vivo' : 'Em breve'; ?>
        </span>
        <div class="timer" id="timer">
            <i class="fas fa-clock"></i>
            <span id="timerText">--:--:--</span>
        </div>
    </header>

    <div class="live-stage">
        <div class="video-wrap">
            <div class="video-stage">
                <div class="video-overlay-top">
                    <span class="chip"><i class="fas fa-circle"></i> Sala da aula</span>
                    <span class="chip"><i class="fas fa-signal"></i> HD · 1080p</span>
                </div>
                <div class="video-overlay-bottom">
                    <i class="fas fa-user-tie"></i>
                    <span><?php echo htmlspecialchars($aula['professor_nome']); ?> · Professor</span>
                </div>
                <div class="video-center">
                    <div class="avatar"><?php echo strtoupper(mb_substr($aula['professor_nome'], 0, 1)); ?></div>
                    <h2><?php echo $ja_comecou ? 'Aguardando vídeo do professor' : 'Aula começa em breve'; ?></h2>
                    <p><?php echo $ja_comecou ? 'Sua câmera está desligada. O professor já está conectado.' : 'Aguarde o início da transmissão. Você está na sala virtual.'; ?></p>
                </div>
            </div>

            <div class="live-controls">
                <button class="control-btn" id="btnMic" onclick="toggleControl(this, 'mic')">
                    <i class="fas fa-microphone"></i>
                    <span class="control-tooltip">Microfone</span>
                </button>
                <button class="control-btn" id="btnCam" onclick="toggleControl(this, 'cam')">
                    <i class="fas fa-video"></i>
                    <span class="control-tooltip">Câmera</span>
                </button>
                <button class="control-btn" onclick="acaoControle('share')">
                    <i class="fas fa-desktop"></i>
                    <span class="control-tooltip">Compartilhar tela</span>
                </button>
                <button class="control-btn" onclick="acaoControle('raise')">
                    <i class="fas fa-hand-paper"></i>
                    <span class="control-tooltip">Levantar a mão</span>
                </button>
                <button class="control-btn" onclick="acaoControle('reaction')">
                    <i class="far fa-smile"></i>
                    <span class="control-tooltip">Reação</span>
                </button>
                <button class="control-btn leave" onclick="sairAula()">
                    <i class="fas fa-phone-slash"></i> Sair da aula
                </button>
            </div>
        </div>

        <aside class="live-sidebar">
            <div class="tabs">
                <button class="tab-btn active" data-tab="chat" onclick="trocarTab('chat', this)">
                    <i class="fas fa-comments"></i> Chat <span class="count" id="msgCount"><?php echo count($mensagens); ?></span>
                </button>
                <button class="tab-btn" data-tab="participants" onclick="trocarTab('participants', this)">
                    <i class="fas fa-users"></i> Pessoas <span class="count"><?php echo count($participantes); ?></span>
                </button>
            </div>

            <div class="tab-pane active" id="tab-chat">
                <div class="chat-messages" id="chatMessages">
                    <?php foreach ($mensagens as $m): ?>
                        <div class="message <?php echo $m['role'] === 'Professor' ? 'prof' : ''; ?>">
                            <div class="message-avatar"><?php echo strtoupper(mb_substr($m['autor'], 0, 1)); ?></div>
                            <div class="message-content">
                                <div class="message-head">
                                    <span class="message-author"><?php echo htmlspecialchars($m['autor']); ?></span>
                                    <span class="message-time">há <?php echo $m['minutos']; ?> min</span>
                                </div>
                                <div class="message-text"><?php echo htmlspecialchars($m['texto']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <form class="chat-input" onsubmit="enviarMensagem(event)">
                    <input type="text" id="msgInput" placeholder="Digite sua mensagem..." autocomplete="off" maxlength="280">
                    <button type="submit" aria-label="Enviar"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>

            <div class="tab-pane" id="tab-participants">
                <div class="participants-list">
                    <?php foreach ($participantes as $p): ?>
                        <div class="participant <?php echo $p['role'] === 'Professor' ? 'prof' : ''; ?> <?php echo $p['live'] ? 'live' : ''; ?> <?php echo isset($p['you']) ? 'you' : ''; ?>">
                            <div class="participant-avatar">
                                <?php echo strtoupper($p['inicial']); ?>
                                <span class="status-dot"></span>
                            </div>
                            <div class="participant-info">
                                <div class="participant-name"><?php echo htmlspecialchars($p['nome']); ?><?php echo isset($p['you']) ? ' (você)' : ''; ?></div>
                                <div class="participant-role"><?php echo $p['role']; ?> · <?php echo $p['live'] ? 'Online' : 'Conectando...'; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <!-- Modal de Confirmação -->
    <div class="confirm-modal" id="confirmModal">
        <div class="confirm-modal-backdrop"></div>
        <div class="confirm-modal-content">
            <div class="confirm-modal-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="confirm-modal-title">Sair da Aula</div>
            <div class="confirm-modal-message">Tem certeza que deseja sair da sala? Você perderá o acesso à aula ao vivo.</div>
            <div class="confirm-modal-actions">
                <button class="confirm-modal-btn confirm-modal-btn-cancel" onclick="closeConfirmModal()">Cancelar</button>
                <button class="confirm-modal-btn confirm-modal-btn-confirm" onclick="confirmSairAula()">Sair da Aula</button>
            </div>
        </div>
    </div>

    <script>
        const inicioTs = <?php echo $inicio_ts * 1000; ?>;
        const jaComecou = <?php echo $ja_comecou ? 'true' : 'false'; ?>;
        const alunoNome = <?php echo json_encode($aluno['nome']); ?>;
        const alunoInicial = <?php echo json_encode(strtoupper(mb_substr($aluno['nome'], 0, 1))); ?>;

        function showToast(title, message, type) {
            type = type || 'info';
            const c = document.getElementById('toastContainer');
            const t = document.createElement('div');
            t.className = 'toast ' + type;
            const icons = { success: 'fa-check-circle', info: 'fa-info-circle', warning: 'fa-exclamation-triangle', error: 'fa-exclamation-circle' };
            t.innerHTML = '<i class="fas ' + (icons[type] || icons.info) + ' toast-icon"></i>' +
                '<div class="toast-content"><div class="toast-title">' + title + '</div><div class="toast-message">' + message + '</div></div>';
            c.appendChild(t);
            setTimeout(() => { t.style.animation = 'slideIn 0.3s reverse'; setTimeout(() => t.remove(), 300); }, 3500);
        }

        function toggleControl(btn, name) {
            btn.classList.toggle('muted');
            const labels = {
                mic: btn.classList.contains('muted') ? 'Microfone desligado' : 'Microfone ligado',
                cam: btn.classList.contains('muted') ? 'Câmera desligada' : 'Câmera ligada'
            };
            const icon = btn.querySelector('i');
            if (name === 'mic') icon.className = btn.classList.contains('muted') ? 'fas fa-microphone-slash' : 'fas fa-microphone';
            if (name === 'cam') icon.className = btn.classList.contains('muted') ? 'fas fa-video-slash' : 'fas fa-video';
            showToast(labels[name], 'Configuração aplicada apenas localmente.', 'info');
        }
        function acaoControle(acao) {
            const msgs = {
                share: ['Compartilhamento de tela', 'Esta funcionalidade será habilitada quando integrarmos com WebRTC.'],
                raise: ['Mão levantada', 'O professor foi notificado.'],
                reaction: ['Reação enviada', 'Sua reação aparecerá na tela em instantes.']
            };
            const [t, m] = msgs[acao] || ['Ação', 'Em breve.'];
            showToast(t, m, 'success');
        }
        function sairAula() {
            document.getElementById('confirmModal').classList.add('active');
        }
        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.remove('active');
        }
        function confirmSairAula() {
            closeConfirmModal();
            showToast('Saindo da aula...', 'Você será redirecionado em instantes.', 'info');
            setTimeout(function(){
                window.location.href = 'minhas_aulas_aluno.php';
            }, 1500);
        }
        function trocarTab(tab, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-' + tab).classList.add('active');
        }
        function enviarMensagem(e) {
            e.preventDefault();
            const input = document.getElementById('msgInput');
            const txt = input.value.trim();
            if (!txt) return;
            const box = document.getElementById('chatMessages');
            const div = document.createElement('div');
            div.className = 'message';
            div.innerHTML = '<div class="message-avatar">' + alunoInicial + '</div>' +
                '<div class="message-content">' +
                '<div class="message-head"><span class="message-author">' + alunoNome + ' (você)</span><span class="message-time">agora</span></div>' +
                '<div class="message-text">' + txt.replace(/</g, '&lt;') + '</div>' +
                '</div>';
            box.appendChild(div);
            box.scrollTop = box.scrollHeight;
            input.value = '';
            const counter = document.getElementById('msgCount');
            counter.textContent = parseInt(counter.textContent) + 1;
        }
        function atualizarTimer() {
            const agora = Date.now();
            const diff = inicioTs - agora;
            const absDiff = Math.abs(diff);
            const h = Math.floor(absDiff / 3600000);
            const m = Math.floor((absDiff % 3600000) / 60000);
            const s = Math.floor((absDiff % 60000) / 1000);
            const fmt = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            const label = diff > 0 ? 'Começa em ' + fmt : 'Em andamento · ' + fmt;
            document.getElementById('timerText').textContent = label;
        }
        atualizarTimer();
        setInterval(atualizarTimer, 1000);

        // Mensagem boas-vindas
        setTimeout(() => showToast('Você entrou na sala', jaComecou ? 'Bem-vindo à aula ao vivo!' : 'Aguarde o início da transmissão.', 'success'), 500);
        // Scroll inicial do chat
        const chat = document.getElementById('chatMessages');
        if (chat) chat.scrollTop = chat.scrollHeight;
    </script>
</body>
</html>


