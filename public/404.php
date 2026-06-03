<?php http_response_code(404); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada — EduConnect Tech</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at 12% 8%, rgba(37, 99, 235, 0.18), transparent 28%),
                radial-gradient(circle at 88% 10%, rgba(16, 185, 129, 0.14), transparent 28%),
                radial-gradient(circle at 50% 96%, rgba(99, 102, 241, 0.12), transparent 30%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #ecfeff 100%);
            padding: 24px;
        }

        .card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.84);
            border-radius: 32px;
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.1);
            backdrop-filter: blur(20px);
            padding: 64px 56px;
            max-width: 560px;
            width: 100%;
            text-align: center;
        }

        .error-code {
            font-size: clamp(5rem, 16vw, 8rem);
            font-weight: 800;
            letter-spacing: -0.06em;
            line-height: 1;
            background: linear-gradient(135deg, #2563eb 0%, #6366f1 50%, #0891b2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .icon-wrap {
            width: 80px;
            height: 80px;
            border-radius: 24px;
            background: linear-gradient(135deg, #2563eb, #6366f1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 28px;
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.22);
        }

        h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
            letter-spacing: -0.04em;
        }

        p {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.65;
            margin-bottom: 36px;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.25s ease;
            font-family: inherit;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.22);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 36px rgba(37, 99, 235, 0.3);
        }

        .btn-outline {
            background: white;
            color: #475569;
            border: 1.5px solid rgba(203, 213, 225, 0.9);
        }

        .btn-outline:hover {
            border-color: #2563eb;
            color: #2563eb;
            transform: translateY(-2px);
        }

        .divider {
            width: 48px;
            height: 4px;
            border-radius: 999px;
            background: linear-gradient(90deg, #2563eb, #6366f1);
            margin: 20px auto 28px;
        }

        @media (max-width: 480px) {
            .card { padding: 40px 28px; }
            .actions { flex-direction: column; }
            .btn { justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <i class="fas fa-compass"></i>
        </div>
        <div class="error-code">404</div>
        <div class="divider"></div>
        <h1>Página não encontrada</h1>
        <p>A página que você está procurando não existe ou foi movida. Verifique o endereço ou volte para o início.</p>
        <div class="actions">
            <a href="login.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Ir para o início
            </a>
            <button class="btn btn-outline" onclick="history.back()">
                <i class="fas fa-arrow-left"></i> Voltar
            </button>
        </div>
    </div>
</body>
</html>
