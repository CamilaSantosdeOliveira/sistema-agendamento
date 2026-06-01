<?php
// Conectar ao banco de dados
include 'db.php';

// Buscar dados reais
$mensagens_count = 0;
$notificacoes_count = 0;
$usuarios_ativos = 0;

try {
    // Contar usuários ativos
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE ativo = 1");
    if ($result) {
        $usuarios_ativos = $result->fetch_assoc()['total'];
    }

    // Simular contadores (você pode criar tabelas reais)
    $mensagens_count = $usuarios_ativos * 5; // Simulado
    $notificacoes_count = $usuarios_ativos * 3; // Simulado

    // Buscar usuários para exibir
    $usuarios_result = $conn->query("SELECT id, nome, tipo_usuario, email FROM usuarios WHERE ativo = 1 ORDER BY nome LIMIT 10");

} catch (Exception $e) {
    // Em caso de erro, usar valores padrão
    $mensagens_count = 0;
    $notificacoes_count = 0;
    $usuarios_ativos = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Comunicação</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .header {
            background: white;
            padding: 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
            border: 1px solid var(--border-color);
        }

        .header h1 {
            color: var(--dark-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            color: var(--secondary-color);
            font-size: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            text-align: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin: 0 auto 16px;
        }

        .stat-card.primary .stat-icon {
            background-color: var(--primary-color);
        }

        .stat-card.success .stat-icon {
            background-color: var(--success-color);
        }

        .stat-card.warning .stat-icon {
            background-color: var(--warning-color);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .section-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            background-color: var(--light-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-outline:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .usuarios-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            padding: 24px;
        }

        .usuario-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 20px;
            transition: var(--transition);
        }

        .usuario-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }

        .usuario-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .usuario-avatar {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .usuario-info h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 4px;
        }

        .usuario-tipo {
            color: var(--secondary-color);
            font-size: 0.9rem;
            text-transform: capitalize;
        }

        .usuario-email {
            color: var(--secondary-color);
            font-size: 0.9rem;
            margin-bottom: 16px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 16px;
            transition: var(--transition);
        }

        .back-btn:hover {
            color: var(--primary-dark);
            transform: translateX(-4px);
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 16px;
            color: var(--border-color);
        }

        .empty-state h4 {
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: var(--dark-color);
        }

        .empty-state p {
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back Button -->
        <a href="dashboard_corrigido.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Voltar ao Dashboard
        </a>

        <!-- Header -->
        <div class="header">
            <h1>💬 Sistema de Comunicação</h1>
            <p>Gerencie mensagens, notificações e interações entre usuários</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-value"><?php echo $mensagens_count; ?></div>
                <div class="stat-label">Mensagens</div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-value"><?php echo $notificacoes_count; ?></div>
                <div class="stat-label">Notificações</div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo $usuarios_ativos; ?></div>
                <div class="stat-label">Usuários Ativos</div>
            </div>
        </div>

        <!-- Usuários Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-users"></i> Usuários do Sistema</h3>
                <button class="btn btn-primary" onclick="enviarMensagem()">
                    <i class="fas fa-paper-plane"></i> Enviar Mensagem
                </button>
            </div>
            
            <?php if ($usuarios_result && $usuarios_result->num_rows > 0): ?>
                <div class="usuarios-grid">
                    <?php while ($usuario = $usuarios_result->fetch_assoc()): ?>
                        <div class="usuario-card">
                            <div class="usuario-header">
                                <div class="usuario-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="usuario-info">
                                    <h4><?php echo htmlspecialchars($usuario['nome']); ?></h4>
                                    <div class="usuario-tipo"><?php echo htmlspecialchars($usuario['tipo_usuario']); ?></div>
                                </div>
                            </div>
                            
                            <div class="usuario-email">
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($usuario['email']); ?>
                            </div>
                            
                            <div style="display: flex; gap: 8px;">
                                <button class="btn btn-outline btn-sm" onclick="mensagemUsuario(<?php echo $usuario['id']; ?>)">
                                    <i class="fas fa-comment"></i> Mensagem
                                </button>
                                <button class="btn btn-outline btn-sm" onclick="notificarUsuario(<?php echo $usuario['id']; ?>)">
                                    <i class="fas fa-bell"></i> Notificar
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h4>Nenhum usuário disponível</h4>
                    <p>Adicione usuários para começar a usar o sistema</p>
                    <button class="btn btn-primary" onclick="adicionarUsuario()">
                        <i class="fas fa-plus"></i> Adicionar Usuário
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Mensagens Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-envelope"></i> Mensagens Recentes</h3>
                <button class="btn btn-success" onclick="verTodasMensagens()">
                    <i class="fas fa-eye"></i> Ver Todas
                </button>
            </div>
            
            <div class="empty-state">
                <i class="fas fa-envelope"></i>
                <h4>Sistema de Mensagens</h4>
                <p>Funcionalidade será implementada em breve</p>
                <button class="btn btn-success" onclick="verTodasMensagens()">
                    <i class="fas fa-eye"></i> Ver Mensagens
                </button>
            </div>
        </div>
    </div>

    <script>
        function enviarMensagem() {
            alert(`💬 Enviando nova mensagem\n\nFuncionalidade será implementada em breve!`);
        }

        function mensagemUsuario(usuarioId) {
            alert(`💬 Enviando mensagem para usuário ID: ${usuarioId}\n\nFuncionalidade será implementada em breve!`);
        }

        function notificarUsuario(usuarioId) {
            alert(`🔔 Notificando usuário ID: ${usuarioId}\n\nFuncionalidade será implementada em breve!`);
        }

        function adicionarUsuario() {
            alert(`👤 Adicionando novo usuário\n\nFuncionalidade será implementada em breve!`);
        }

        function verTodasMensagens() {
            alert(`📧 Visualizando todas as mensagens\n\nFuncionalidade será implementada em breve!`);
        }
    </script>
</body>
</html>



































