<?php
// Conexão com banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "educonnect"; // ou "agendamento" se esse for o nome do seu banco
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Falha na conexão: " . $conn->connect_error); }

// Processa agendamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agendar'])) {
    $curso_id = $_POST['curso_id'] ?? '';
    $professor_id = $_POST['professor_id'] ?? '';
    $materia = $_POST['materia'] ?? '';
    $data = $_POST['data'] ?? '';
    $horario = $_POST['horario'] ?? '';
    if ($curso_id && $professor_id && $materia && $data && $horario) {
        $stmt = $conn->prepare("INSERT INTO agendamentos (curso_id, professor_id, materia, data, horario) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Erro na preparação do SQL: " . $conn->error);
        }
        $stmt->bind_param("iisss", $curso_id, $professor_id, $materia, $data, $horario);
        $stmt->execute();
        $stmt->close();
        header("Location: edu certo oficial.php");
        exit;
    } else {
        $msg = "Preencha todos os campos!";
    }
}

// Busca professores
$professores = [];
$result = $conn->query("SELECT id, nome FROM professores");
while ($row = $result->fetch_assoc()) { $professores[] = $row; }

// Busca cursos
$cursos = [];
$result = $conn->query("SELECT id, nome FROM cursos");
while ($row = $result->fetch_assoc()) { $cursos[] = $row; }

// Busca agendamentos
$aulas = [];
$result = $conn->query("SELECT a.*, p.nome as professor_nome, c.nome as curso_nome FROM agendamentos a JOIN professores p ON a.professor_id=p.id JOIN cursos c ON a.curso_id=c.id ORDER BY a.data DESC");
while ($row = $result->fetch_assoc()) { $aulas[] = $row; }
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>EduConnect - Sistema Educacional</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>   
       
    
</head>
    <button class="menu-toggle" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
    <div class="main-layout" style="margin-left:250px;">
        <aside class="sidebar open">
    <div class="sidebar-header">
        <div style="background:linear-gradient(135deg,#3a7bd5,#6a89cc); border-radius:50%; padding:6px; box-shadow:0 4px 16px rgba(58,123,213,0.18);">
            <img src="https://i.pravatar.cc/56?img=3" alt="Avatar" style="width:56px; height:56px; border-radius:50%; border:3px solid #fff;">
        </div>
        <a href="#" class="logo">EduConnect</a>
        <span style="color:#eaf1fb; font-size:1rem; font-weight:500; margin-top:4px;">Seu portal educacional</span>
    </div>
    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <li><span class="nav-item active" onclick="showScreen('dashboard')"><i class="fas fa-home"></i> Dashboard</span></li>
            <li><span class="nav-item" onclick="showScreen('agendar')"><i class="fas fa-calendar-alt"></i> Agendar Aulas</span></li>
            <li><span class="nav-item" onclick="showScreen('notificacoes')"><i class="fas fa-bell"></i> Notificações</span></li>
            <li><span class="nav-item" onclick="showScreen('chat')"><i class="fas fa-comments"></i> Chat</span></li>
            <li><span class="nav-item" onclick="showScreen('notas')"><i class="fas fa-graduation-cap"></i> Notas</span></li>
            <li><span class="nav-item" onclick="showScreen('relatorios')"><i class="fas fa-chart-line"></i> Relatórios</span></li>
            <li><span class="nav-item" onclick="showScreen('config')"><i class="fas fa-cog"></i> Configurações</span></li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <span style="color:#fff; font-size:0.95rem;">Versão 3.3.1<br>© 2025 EduConnect</span>
    </div>
</aside>
        <div class="topbar">
            <button class="btn" style="background:#6a89cc; margin-right:16px;"><i class="fa fa-bell"></i> Notificações</button>
            <span class="user-info">
                <img src="https://i.pravatar.cc/40?img=3" style="width:32px; height:32px; border-radius:50%; border:2px solid #6a89cc;">
                Usuário Teste
            </span>
        </div>
        <main class="content-area">
    <section id="dashboard-screen" class="screen active">
    <div class="dashboard-header">
        <h2><i class="fa fa-chart-bar"></i> Dashboard</h2>
        <div class="dashboard-actions">
            <button class="btn btn-primary" onclick="showScreen('agendar')"><i class="fa fa-plus-circle fa-lg"></i> Agendar Nova Aula</button>
            <button class="btn btn-success" onclick="showScreen('horarios')"><i class="fa fa-clock fa-lg"></i> Meus Horários</button>
            <button class="btn btn-warning" onclick="showScreen('historico')"><i class="fa fa-history fa-lg"></i> Histórico de Aulas</button>
        </div>
    </div>
    <div class="dashboard-cards">
        <div class="dashboard-card" title="Total de aulas agendadas">
            <div class="card-title"><i class="fa fa-calendar-alt fa-lg"></i> Total Aulas</div>
            <div id="card-total" class="card-value">156</div>
        </div>
        <div class="dashboard-card" title="Aulas concluídas">
            <div class="card-title"><i class="fa fa-check fa-lg"></i> Concluídas</div>
            <div id="card-concluidas" class="card-value">134</div>
        </div>
        <div class="dashboard-card" title="Próximas aulas">
            <div class="card-title"><i class="fa fa-clock fa-lg"></i> Próximas</div>
            <div id="card-proximas" class="card-value">8</div>
        </div>
        <div class="dashboard-card" title="Avaliação média">
            <div class="card-title"><i class="fa fa-star fa-lg"></i> Avaliação</div>
            <div id="card-avaliacao" class="card-value">4.7 <i class="fa fa-star" style="color:gold"></i></div>
        </div>
    </div> <!-- FECHAMENTO CORRETO DA DIV dashboard-cards -->
    <div class="card">
        <div style="font-weight:600; color:#3a7bd5; margin-bottom:8px;">Progresso Mensal</div>
        <canvas id="dashboardChart" height="80"></canvas>
    </div>
</section>
    <section id="historico-screen" class="screen">
        <div class="card">
            <h2><i class="fa fa-history"></i> Histórico de Aulas</h2>
            <ul>
                <?php foreach($aulas as $aula): ?>
                    <li>
                        <strong><?= $aula['data'] ?> <?= $aula['horario'] ?></strong> - 
                        <?= $aula['materia'] ?> (<?= $aula['curso_nome'] ?>) com <?= $aula['professor_nome'] ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <section id="notas-screen" class="screen">
        <div class="card">
            <h2><i class="fa fa-graduation-cap"></i> Minhas Notas</h2>
            <table class="table" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Matéria</th>
                        <th>Professor</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($aulas as $aula): ?>
                        <tr>
                            <td><?= $aula['curso_nome'] ?></td>
                            <td><?= $aula['materia'] ?></td>
                            <td><?= $aula['professor_nome'] ?></td>
                            <td><?= $aula['nota'] ?? '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <section id="relatorios-screen" class="screen">
        <div class="card">
            <h2><i class="fa fa-chart-line"></i> Relatórios e Analytics</h2>
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class="card-title"><i class="fa fa-users"></i> Total de Alunos</div>
                    <div class="card-value">1.247</div>
                    <div class="card-info">+12% este mês</div>
                </div>
                <div class="dashboard-card">
                    <div class="card-title"><i class="fa fa-book"></i> Aulas Realizadas</div>
                    <div class="card-value">3.456</div>
                    <div class="card-info">+8% este mês</div>
                </div>
                <div class="dashboard-card">
                    <div class="card-title"><i class="fa fa-star"></i> Avaliação Média</div>
                    <div class="card-value">4.8</div>
                    <div class="card-info">+0.2 pontos</div>
                </div>
                <div class="dashboard-card">
                    <div class="card-title"><i class="fa fa-bullseye"></i> Taxa de Conclusão</div>
                    <div class="card-value">89%</div>
                    <div class="card-info">+3% este mês</div>
                </div>
            </div>
            <div class="card" style="margin-top:24px;">
                <div style="font-weight:600; color:#3a7bd5; margin-bottom:8px;">Engajamento por Mês</div>
                <canvas id="relatorioChart" height="80"></canvas>
            </div>
            <div class="card" style="margin-top:24px;">
                <div style="font-weight:600; color:#3a7bd5; margin-bottom:8px;">Matérias Mais Populares</div>
                <ul style="list-style:none; padding:0;">
                    <li>Matemática <span style="float:right; color:#3a7bd5;">85%</span></li>
                    <li>História <span style="float:right; color:#f39c12;">72%</span></li>
                    <li>Física <span style="float:right; color:#27ae60;">68%</span></li>
                    <li>Português <span style="float:right; color:#e74c3c;">64%</span></li>
                </ul>
            </div>
        </div>
    </section>
    <section id="config-screen" class="screen">
        <div class="card">
            <h2><i class="fa fa-cog"></i> Configurações</h2>
            <p>Altere seu perfil, senha ou preferências do sistema.</p>
        </div>
    </section>
    <section id="chat-screen" class="screen">
        <div class="card" id="chat-card" style="max-width:500px; margin:auto;">
            <h2><i class="fa fa-comments"></i> Chat Bot</h2>
            <div id="chatMessages" style="background:#f5f7fa; border-radius:8px; min-height:180px; max-height:260px; overflow-y:auto; margin-bottom:12px; padding:12px; font-size:1rem; display:flex; flex-direction:column;">
                <!-- Mensagens aparecem aqui -->
            </div>
            <form id="chatForm" style="display:flex; gap:10px;">
                <input type="text" id="chatInput" class="form-control" placeholder="Digite sua mensagem..." required style="flex:1; padding:12px; border-radius:8px; border:1px solid #dee2e6; font-size:1rem;">
                <button type="submit" class="btn btn-primary" style="background:#e91e63; font-size:1.1rem; border-radius:8px;">
                    <i class="fa fa-paper-plane" style="font-size:1.2rem;"></i>
                </button>
            </form>
        </div>
    </section>
    <section id="agendar-screen" class="screen">
        <div class="card">
            <h2><i class="fa fa-calendar-alt"></i> Agendar Nova Aula</h2>
            <form method="post" class="form-agendar">
    <div class="form-group">
        <label class="form-label"><i class="fa fa-book"></i> Curso:</label>
        <select name="curso_id" class="form-control" required>
            <option value="">Selecione</option>
            <?php foreach($cursos as $curso): ?>
                <option value="<?= $curso['id'] ?>"><?= $curso['nome'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label"><i class="fa fa-chalkboard-teacher"></i> Professor:</label>
        <select name="professor_id" class="form-control" required>
            <option value="">Selecione</option>
            <?php foreach($professores as $prof): ?>
                <option value="<?= $prof['id'] ?>"><?= $prof['nome'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label"><i class="fa fa-file-alt"></i> Matéria:</label>
        <input type="text" name="materia" class="form-control" placeholder="Digite a matéria" required>
    </div>
    <div class="form-group">
        <label class="form-label"><i class="fa fa-calendar-day"></i> Data:</label>
        <input type="date" name="data" class="form-control" required>
    </div>
    <div class="form-group">
        <label class="form-label"><i class="fa fa-clock"></i> Horário:</label>
        <input type="time" name="horario" class="form-control" required>
    </div>
    <button type="submit" name="agendar" class="btn btn-primary" style="width:100%; font-size:1.1rem;">
        <i class="fa fa-calendar-plus"></i> Agendar Aula
    </button>
</form>
        </div>
        <div class="card">
            <h3><i class="fa fa-list"></i> Próximas Aulas Agendadas</h3>
            <ul>
                <?php foreach($aulas as $aula): ?>
                    <li>
                        <strong><?= $aula['data'] ?> <?= $aula['horario'] ?></strong> - 
                        <?= $aula['materia'] ?> (<?= $aula['curso_nome'] ?>) com <?= $aula['professor_nome'] ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <section id="notificacoes-screen" class="screen">
        <div class="card">
            <h2><i class="fa fa-bell"></i> Notificações</h2>
            <ul>
                <li>Nova aula agendada para amanhã às 10h.</li>
                <li>Professor João atualizou as notas do curso de Informática.</li>
                <li>Seu certificado está disponível para download.</li>
            </ul>
        </div>
    </section>
    <section id="horarios-screen" class="screen">
        <div class="card">
            <h2><i class="fa fa-clock"></i> Meus Horários</h2>
            <p>Em breve você verá seus horários aqui!</p>
        </div>
    </section>
</main>
    <!-- Toast, Modal, Botões, Scripts -->
    <!-- ...mantenha igual ao seu arquivo anterior... -->
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
        }
        function showToast(msg) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 2500);
        }
        function showScreen(screen) {
            document.querySelectorAll('.nav-item').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.screen').forEach(sec => sec.classList.remove('active'));
            document.getElementById(screen + '-screen').classList.add('active');
            document.querySelectorAll('.nav-item').forEach(btn => {
                if (btn.getAttribute('onclick') === `showScreen('${screen}')`) {
                    btn.classList.add('active');
                }
            });
        }
        // Ativa o dashboard ao carregar
        window.onload = function() {
            showScreen('dashboard');
        };

        // Gráfico do dashboard
      // Gráfico do dashboard
const ctx = document.getElementById('dashboardChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Progresso Mensal',
            data: [12, 19, 15, 22, 28, 35],
            borderColor: '#3a7bd5',
            backgroundColor: 'rgba(58,123,213,0.1)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});
        // Chat Bot Profissional
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const box = document.getElementById('chatMessages');
            const message = document.getElementById('chatInput').value;
            if (!message) return;
            const userDiv = document.createElement('div');
            userDiv.textContent = 'Você: ' + message;
            userDiv.classList.add('user');
            box.appendChild(userDiv);
            setTimeout(() => {
                const botDiv = document.createElement('div');
                botDiv.textContent = 'Bot: Olá! Você disse: ' + message;
                botDiv.classList.add('bot');
                box.appendChild(botDiv);
                box.scrollTop = box.scrollHeight;
            }, 500);
            document.getElementById('chatInput').value = '';
            box.scrollTop = box.scrollHeight;
        });
        if(document.getElementById('relatorioChart')) {
            const relatorioCtx = document.getElementById('relatorioChart').getContext('2d');
            new Chart(relatorioCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Engajamento',
                        data: [120, 140, 110, 150, 170, 200],
                        backgroundColor: '#3a7bd5',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    </script>
</body>
</html>

