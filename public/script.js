document.addEventListener('DOMContentLoaded', () => {
    // Event listener para o formulário de agendamento
    const scheduleForm = document.getElementById('scheduleForm');
    if (scheduleForm) {
        scheduleForm.addEventListener('submit', scheduleClass);
    }

    // Avatar do usuário personalizado (upload)
    const avatarInput = document.getElementById('avatar-upload');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    document.getElementById('user-avatar').src = ev.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Alternância de tema
    document.getElementById('themeToggle').onclick = function() {
        document.body.classList.toggle('dark-mode');
        showNotification(
            document.body.classList.contains('dark-mode') ? 'Modo escuro ativado!' : 'Modo claro ativado!',
            'success'
        );
    };

    // Modal de perfil
    function abrirModalPerfil() {
        document.getElementById('modalPerfil').style.display = 'flex';
    }
    function fecharModalPerfil() {
        document.getElementById('modalPerfil').style.display = 'none';
    }
    const perfilBtn = document.querySelector('.fa-user-edit');
    if (perfilBtn) perfilBtn.addEventListener('click', abrirModalPerfil);
    const formPerfil = document.getElementById('formPerfil');
    if (formPerfil) {
        formPerfil.onsubmit = function(e) {
            e.preventDefault();
            showNotification('Perfil atualizado!', 'success');
            fecharModalPerfil();
        };
    }

    // Botão de ajuda/tour
    document.getElementById('ajudaBtn').onclick = function() {
        showNotification('Bem-vindo ao EduConnect! Navegue pelo menu lateral para acessar todas as funções.', 'success');
    };

    // ...outros listeners que quiser adicionar...
});

// ...restante do seu código (NÃO coloque o código do avatar fora desse bloco!)...
    }

    // Avatar do usuário personalizado (upload)
   
// ...restante do seu código...
    // Event listener para o formulário de agendamento
    

// Sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobile-overlay');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('visible');
}

// Tema escuro
function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    const toggleBtn = document.querySelector('.theme-toggle');
    toggleBtn.textContent = document.body.classList.contains('dark-mode') ? '☀️' : '🌙';
}

// Notificações
function showNotification(message, type) {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    notification.classList.add('notification', type);
    notification.textContent = message;
    container.appendChild(notification);
    void notification.offsetWidth;
    notification.classList.add('show');
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 500);
    }, 5000);
}

// Agendamento
async function scheduleClass(event) {
    event.preventDefault();
    const form = document.getElementById('scheduleForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    if (!data.teacherName || !data.subject || !data.student || !data.date || !data.time || !data.duration) {
        showNotification('Por favor, preencha todos os campos do formulário.', 'error');
        return;
    }
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = document.getElementById('button-text').textContent;
    submitBtn.classList.add('loading-state');
    document.getElementById('button-text').textContent = '';
    submitBtn.disabled = true;
    try {
        const response = await fetch('schedule.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            showNotification(result.message, 'success');
            form.reset();
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Erro na comunicação com o servidor:', error);
        showNotification('Ocorreu um erro ao agendar. Tente novamente.', 'error');
    } finally {
        submitBtn.classList.remove('loading-state');
        document.getElementById('button-text').textContent = originalText;
        submitBtn.disabled = false;
    }
}

// --- CHAT BOT PROFISSIONAL ---
document.getElementById('chatForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const box = document.getElementById('chatMessages');
    const message = document.getElementById('chatInput').value;
    if (!message) return;

    // Balão do usuário com avatar
    const userDiv = document.createElement('div');
    userDiv.innerHTML = `
        <div style="display:flex; align-items:center; justify-content:flex-end; gap:8px;">
            <span style="background:#eaf1fb; color:#3a7bd5; border-radius:16px 16px 0 16px; padding:8px 14px; max-width:70%; font-size:0.98rem;">${message}</span>
            <img src="https://i.pravatar.cc/32?img=3" style="width:32px; height:32px; border-radius:50%; border:2px solid #3a7bd5;">
        </div>
    `;
    box.appendChild(userDiv);

    // Balão do bot com avatar
    setTimeout(() => {
        const botDiv = document.createElement('div');
        botDiv.innerHTML = `
            <div style="display:flex; align-items:center; gap:8px;">
                <img src="https://i.pravatar.cc/32?img=12" style="width:32px; height:32px; border-radius:50%; border:2px solid #e91e63;">
                <span style="background:#ffe5ef; color:#e91e63; border-radius:16px 16px 16px 0; padding:8px 14px; max-width:70%; font-size:0.98rem;">
                    <i class="fa fa-robot"></i> Olá! Você disse: ${message}
                </span>
            </div>
        `;
        box.appendChild(botDiv);
        box.scrollTop = box.scrollHeight;
    }, 500);

    document.getElementById('chatInput').value = '';
    box.scrollTop = box.scrollHeight;
});

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
   // ...código do Chart.js...
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

// --- FUNÇÃO DE AGENDAMENTO ---
    function salvarAgendamento() {
    const curso_id = document.querySelector('#agendar-screen select[name="curso_id"]').value;
   const professor_id = document.querySelector('#agendar-screen select[name="professor_id"]').value;
    const data = document.querySelector('#agendar-screen input[name="data"]').value;
    const horario = document.querySelector('#agendar-screen input[name="horario"]').value;
    const formData = new FormData();
    formData.append('curso_id', curso_id);
    formData.append('professor_id', professor_id);
    formData.append('data', data);
    formData.append('horario', horario);

    fetch('inserir_agendamento.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.text())
    .then(msg => showNotification(msg, 'success'));
}

// ...restante do seu código...