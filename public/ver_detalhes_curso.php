<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: login.php');
    exit();
}

include 'db.php';

$curso_id = $_GET['id'] ?? 0;

if (!$curso_id) {
    header('Location: cursos_completo.php');
    exit;
}

$sql = "SELECT * FROM cursos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $curso_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: cursos_completo.php');
    exit;
}

$curso = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Curso - <?php echo htmlspecialchars($curso['nome']); ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --dark-color: #0f172a;
            --light-color: #f8fafc;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --border-radius-lg: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --font-family-base: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-family-base);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
            background-attachment: fixed;
            color: var(--dark-color);
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 24px;
            transition: var(--transition);
        }
        
        .back-link:hover {
            color: var(--primary-color);
            transform: translateX(-4px);
        }
        
        .course-header {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 40px;
            box-shadow: var(--shadow-lg);
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
        }
        
        .course-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light), var(--success-color));
        }
        
        .course-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }
        
        .course-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .course-status.active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .course-status.inactive {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }
        
        .course-description {
            font-size: 1.125rem;
            color: var(--secondary-color);
            line-height: 1.8;
            margin-top: 24px;
            padding: 24px;
            background: var(--light-color);
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .info-card {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }
        
        .info-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        
        .info-card-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        
        .info-card-icon i {
            font-size: 1.5rem;
            color: white;
        }
        
        .info-card-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--secondary-color);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        
        .info-card-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .info-card-value.price {
            color: var(--success-color);
        }
        
        .actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 32px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            border-radius: 9999px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9375rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }
        
        .btn-secondary:hover {
            background: var(--primary-color);
            color: white;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px 16px;
            }
            
            .course-header {
                padding: 24px;
            }
            
            .course-title {
                font-size: 1.75rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="cursos_completo.php" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Voltar aos Cursos
        </a>
        
        <div class="course-header">
            <h1 class="course-title"><?php echo htmlspecialchars($curso['nome']); ?></h1>
            <span class="course-status <?php echo $curso['status'] === 'ativo' ? 'active' : 'inactive'; ?>">
                <i class="fas fa-circle"></i>
                <?php echo ucfirst($curso['status']); ?>
            </span>
            
            <div class="course-description">
                <?php echo nl2br(htmlspecialchars($curso['descricao'])); ?>
            </div>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="info-card-label">Categoria</div>
                <div class="info-card-value"><?php echo htmlspecialchars($curso['categoria'] ?: 'Tecnologia'); ?></div>
            </div>
            
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="info-card-label">Nível</div>
                <div class="info-card-value"><?php echo htmlspecialchars($curso['nivel'] ?: 'Intermediário'); ?></div>
            </div>
            
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="info-card-label">Duração</div>
                <div class="info-card-value"><?php echo htmlspecialchars($curso['duracao_horas']); ?> horas</div>
            </div>
            
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="info-card-label">Preço</div>
                <div class="info-card-value price">R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></div>
            </div>
            
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="fas fa-hashtag"></i>
                </div>
                <div class="info-card-label">ID do Curso</div>
                <div class="info-card-value">#<?php echo $curso['id']; ?></div>
            </div>
        </div>
        
        <div class="actions">
            <a href="cursos_completo.php" class="btn btn-primary">
                <i class="fas fa-list"></i>
                Ver Todos os Cursos
            </a>
            <a href="dashboard_final.php" class="btn btn-secondary">
                <i class="fas fa-tachometer-alt"></i>
                Voltar ao Dashboard
            </a>
        </div>
    </div>
</body>
</html>


