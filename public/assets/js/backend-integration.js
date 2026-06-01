/**
 * EduCerto - Backend Integration
 * Funções para integração com PHP backend
 */

// Configuração da API
const API_BASE_URL = '/api/educerto-api.php';

// Classe para gerenciar requisições à API
class EduCertoAPI {
    constructor() {
        this.baseURL = API_BASE_URL;
    }

    // Método genérico para requisições
    async request(action, data = {}, method = 'POST') {
        try {
            const url = method === 'GET' ? 
                `${this.baseURL}?action=${action}&${new URLSearchParams(data).toString()}` : 
                `${this.baseURL}?action=${action}`;

            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
            };

            if (method !== 'GET') {
                options.body = JSON.stringify({ action, ...data });
            }

            const response = await fetch(url, options);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Erro na requisição');
            }

            return result;
        } catch (error) {
            console.error('Erro na API:', error);
            return { success: false, message: error.message };
        }
    }

    // Login via API
    async login(username, password) {
        return await this.request('login', { username, password });
    }

    // Salvar agenda
    async saveSchedule(username, schedule) {
        return await this.request('save_schedule', { username, schedule });
    }

    // Carregar agenda
    async loadSchedule(username) {
        return await this.request('load_schedule', { username }, 'GET');
    }

    // Salvar perfil
    async saveProfile(username, profile) {
        return await this.request('save_profile', { username, profile });
    }

    // Carregar perfil
    async loadProfile(username) {
        return await this.request('load_profile', { username }, 'GET');
    }

    // Obter analytics
    async getAnalytics() {
        return await this.request('get_analytics', {}, 'GET');
    }

    // Agendar uma única aula
    async scheduleClass(username, classData, email = '') {
        return await this.request('schedule_single_class', { username, ...classData, email });
    }

    // Enviar mensagem
    async sendMessage(from, to, message) {
        return await this.request('send_message', { from, to, message });
    }

    // Obter mensagens
    async getMessages(username) {
        return await this.request('get_messages', { username }, 'GET');
    }

    // Exportar dados
    async exportData(username) {
        return await this.request('export_data', { username }, 'GET');
    }
}

// Instância global da API
const apiClient = new EduCertoAPI();

// Funções aprimoradas com integração PHP

// Login aprimorado com backend
async function loginWithBackend() {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorDiv = document.getElementById('errorMessage');
    const submitBtn = document.querySelector('.btn-login');

    if (!username || !password) {
        errorDiv.textContent = '❌ Por favor, preencha todos os campos';
        errorDiv.style.display = 'block';
        return;
    }

    // Mostrar loading
    if (submitBtn) {
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '🔄 Entrando...';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';
    }

    try {
        const result = await apiClient.login(username, password);
        
        if (result.success) {
            currentUser = result.user;
            localStorage.setItem('educonnect_user', JSON.stringify(currentUser));
            errorDiv.style.display = 'none';
            
            // Feedback de sucesso
            if (submitBtn) {
                submitBtn.innerHTML = '✅ Sucesso!';
                submitBtn.style.background = 'var(--success-color)';
            }
            
            showNotification('✅ Login realizado com sucesso!', 'success');
            
            setTimeout(() => {
                showMainApp();
                loadUserDataFromBackend();
            }, 1500);
        } else {
            throw new Error(result.message || 'Erro no login');
        }
    } catch (error) {
        errorDiv.textContent = '❌ ' + error.message;
        errorDiv.style.display = 'block';
        
        // Feedback de erro
        if (submitBtn) {
            submitBtn.style.background = 'var(--danger-color)';
            submitBtn.innerHTML = '❌ Erro!';
            
            setTimeout(() => {
                submitBtn.style.background = '';
                submitBtn.innerHTML = '🚀 Entrar no Sistema';
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            }, 2000);
        }
        
        showNotification('❌ ' + error.message, 'error');
        
        // Limpar campos
        document.getElementById('password').value = '';
        document.getElementById('username').focus();
    }
}

// Carregar dados do usuário do backend
async function loadUserDataFromBackend() {
    if (!currentUser) return;

    try {
        // Carregar perfil
        const profileResult = await apiClient.loadProfile(currentUser.username);
        if (profileResult.success && profileResult.profile) {
            // Aplicar dados do perfil se necessário
            console.log('📄 Perfil carregado:', profileResult.profile);
        }

        // Carregar agenda
        const scheduleResult = await apiClient.loadSchedule(currentUser.username);
        if (scheduleResult.success && scheduleResult.schedule) {
            // Aplicar dados da agenda se necessário
            console.log('📅 Agenda carregada:', scheduleResult.schedule);
        }

        // Carregar analytics se for admin ou professor
        if (currentUser.role === 'admin' || currentUser.role === 'professor') {
            const analyticsResult = await apiClient.getAnalytics();
            if (analyticsResult.success) {
                updateChartsWithData(analyticsResult.analytics);
            }
        }

    } catch (error) {
        console.error('Erro ao carregar dados do backend:', error);
        showNotification('⚠️ Alguns dados não puderam ser carregados', 'warning');
    }
}

// Atualizar gráficos com dados do backend
function updateChartsWithData(analyticsData) {
    // Esta função será chamada quando os dados de analytics estiverem disponíveis
    console.log('📊 Dados de analytics recebidos:', analyticsData);
    
    // Aqui você pode usar os dados para atualizar os gráficos Chart.js
    // Exemplo: updateDashboardCharts(analyticsData);
}

// Salvar agenda no backend
async function saveScheduleToBackend(scheduleData) {
    if (!currentUser) return;

    try {
        const result = await apiClient.saveSchedule(currentUser.username, scheduleData);
        if (result.success) {
            showNotification('✅ Agenda salva no servidor!', 'success');
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Erro ao salvar agenda:', error);
        showNotification('❌ Erro ao salvar agenda: ' + error.message, 'error');
    }
}

// Função de agendamento aprimorada com backend e email
async function scheduleClassWithBackend() {
    if (!currentUser) {
        showNotification('❌ Usuário não encontrado', 'error');
        return;
    }

    const subject = prompt('📚 Digite a matéria da aula:');
    if (!subject) return;

    const date = prompt('📅 Digite a data (dd/mm/yyyy):');
    if (!date) return;

    const time = prompt('⏰ Digite o horário (hh:mm):');
    if (!time) return;

    // Perguntar pelo email para confirmação
    const userEmail = prompt('📧 Digite seu email para receber confirmação (opcional):');

    const classData = {
        subject: subject.trim(),
        date: date.trim(),
        time: time.trim(),
        teacher: currentUser?.name || 'Professor',
        description: '',
        duration: 60
    };

    try {
        showNotification('🔄 Agendando aula...', 'info');
        
        const result = await apiClient.scheduleClass(currentUser.username, classData, userEmail || '');
        
        if (result.success) {
            // Salvar no localStorage também (backup local)
            let localSchedule = JSON.parse(localStorage.getItem('schedule_' + currentUser.username) || '[]');
            localSchedule.push(result.classData);
            localStorage.setItem('schedule_' + currentUser.username, JSON.stringify(localSchedule));
            
            let message = '✅ Aula agendada: ' + subject + ' em ' + date + ' às ' + time;
            if (result.emailSent) {
                message += '\n📧 Email de confirmação enviado!';
            } else if (userEmail) {
                message += '\n⚠️ Email não pôde ser enviado, mas aula foi salva.';
            }
            
            showNotification(message, 'success');
            
            // Atualizar interface se estiver na tela de calendário
            if (typeof loadCalendarModule === 'function') {
                setTimeout(() => {
                    const currentContent = document.getElementById('contentArea')?.innerHTML;
                    if (currentContent && currentContent.includes('Calendário de Aulas')) {
                        loadCalendarModule();
                    }
                }, 1000);
            }
            
        } else {
            throw new Error(result.message || 'Erro ao agendar aula');
        }
    } catch (error) {
        console.error('Erro no agendamento:', error);
        
        // Fallback: salvar apenas localmente
        const fallbackData = {
            ...classData,
            id: 'local_' + Date.now(),
            created_at: new Date().toISOString(),
            status: 'scheduled'
        };
        
        let localSchedule = JSON.parse(localStorage.getItem('schedule_' + currentUser.username) || '[]');
        localSchedule.push(fallbackData);
        localStorage.setItem('schedule_' + currentUser.username, JSON.stringify(localSchedule));
        
        showNotification('⚠️ Aula salva localmente. Servidor indisponível: ' + error.message, 'warning');
    }
}

// Função para carregar e exibir aulas agendadas
async function loadScheduledClasses() {
    if (!currentUser) return [];
    
    try {
        // Tentar carregar do backend primeiro
        const result = await apiClient.loadSchedule(currentUser.username);
        if (result.success && result.schedule) {
            return result.schedule;
        }
    } catch (error) {
        console.log('Backend indisponível, carregando dados locais');
    }
    
    // Fallback: carregar do localStorage
    const localSchedule = localStorage.getItem('schedule_' + currentUser.username);
    return localSchedule ? JSON.parse(localSchedule) : [];
}

// Função para exibir aulas no calendário
function displayScheduledClasses(classes) {
    if (!classes || classes.length === 0) {
        return '<p style="text-align: center; color: #666; padding: 20px;">📅 Nenhuma aula agendada ainda.</p>';
    }
    
    return classes.map(cls => `
        <div class="class-item" style="display: flex; align-items: center; justify-content: space-between; background: white; padding: 15px; margin: 10px 0; border-radius: 12px; box-shadow: var(--shadow-sm); border-left: 4px solid var(--accent-color);">
            <div class="class-time" style="font-weight: 600; color: var(--accent-color); min-width: 80px;">${cls.time}</div>
            <div class="class-info" style="flex: 1; margin: 0 15px;">
                <h4 style="margin: 0 0 5px 0; color: var(--text-primary);">${cls.subject}</h4>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">
                    📅 ${cls.date} • 👨‍🏫 ${cls.teacher} 
                    ${cls.status ? '• ' + (cls.status === 'scheduled' ? '🟢 Agendada' : cls.status === 'completed' ? '✅ Concluída' : '❌ Cancelada') : ''}
                </p>
            </div>
            <div class="class-actions">
                <button onclick="editClass('${cls.id || cls.created_at}')" class="btn-small" style="background: var(--warning-color); color: white; margin-right: 5px;">✏️</button>
                <button onclick="cancelClass('${cls.id || cls.created_at}')" class="btn-small" style="background: var(--danger-color); color: white;">❌</button>
            </div>
        </div>
    `).join('');
}

// Funções para gerenciar aulas
window.editClass = function(classId) {
    showNotification('✏️ Funcionalidade de edição em desenvolvimento!', 'info');
};

window.cancelClass = async function(classId) {
    if (!confirm('❌ Tem certeza que deseja cancelar esta aula?')) return;
    
    try {
        // Carregar aulas atuais
        let classes = await loadScheduledClasses();
        
        // Marcar como cancelada
        classes = classes.map(cls => {
            if ((cls.id || cls.created_at) === classId) {
                cls.status = 'cancelled';
            }
            return cls;
        });
        
        // Salvar atualizações
        localStorage.setItem('schedule_' + currentUser.username, JSON.stringify(classes));
        
        // Tentar salvar no backend também
        try {
            await apiClient.saveSchedule(currentUser.username, classes);
        } catch (e) {
            console.log('Não foi possível sincronizar com o servidor');
        }
        
        showNotification('❌ Aula cancelada com sucesso!', 'success');
        
        // Recarregar calendário se estiver aberto
        if (typeof loadCalendarModule === 'function') {
            setTimeout(loadCalendarModule, 500);
        }
        
    } catch (error) {
        console.error('Erro ao cancelar aula:', error);
        showNotification('❌ Erro ao cancelar aula: ' + error.message, 'error');
    }
};

// Exportar dados aprimorado
async function exportDataWithBackend() {
    if (!currentUser) {
        showNotification('❌ Usuário não encontrado', 'error');
        return;
    }

    try {
        showNotification('📊 Preparando exportação...', 'info');
        
        const result = await apiClient.exportData(currentUser.username);
        
        if (result.success) {
            // Criar arquivo para download
            const dataStr = JSON.stringify(result.data, null, 2);
            const dataBlob = new Blob([dataStr], { type: 'application/json' });
            
            const link = document.createElement('a');
            link.href = URL.createObjectURL(dataBlob);
            link.download = result.filename || 'educerto_export.json';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showNotification('✅ Dados exportados com sucesso!', 'success');
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Erro na exportação:', error);
        showNotification('❌ Erro ao exportar dados: ' + error.message, 'error');
    }
}
    if (!currentUser) {
        showNotification('❌ Usuário não encontrado', 'error');
        return;
    }

    try {
        showNotification('📊 Preparando exportação...', 'info');
        
        const result = await apiClient.exportData(currentUser.username);
        
        if (result.success) {
            // Criar arquivo para download
            const dataStr = JSON.stringify(result.data, null, 2);
            const dataBlob = new Blob([dataStr], { type: 'application/json' });
            
            const link = document.createElement('a');
            link.href = URL.createObjectURL(dataBlob);
            link.download = result.filename || 'educerto_export.json';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showNotification('✅ Dados exportados com sucesso!', 'success');
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Erro na exportação:', error);
        showNotification('❌ Erro ao exportar dados: ' + error.message, 'error');
    }
}

// Substituir funções originais se o backend estiver disponível
function initializeBackendIntegration() {
    // Verificar se o backend está disponível
    fetch(API_BASE_URL + '?action=test')
        .then(response => {
            if (response.ok) {
                console.log('🔗 Backend PHP disponível - Integrando funcionalidades...');
                
                // Substituir função de login original
                window.login = loginWithBackend;
                window.scheduleClass = scheduleClassWithBackend;
                window.exportData = exportDataWithBackend;
                
                showNotification('🔗 Conectado ao servidor!', 'success');
            }
        })
        .catch(error => {
            console.log('⚠️ Backend PHP não disponível - Usando funcionalidades offline');
            showNotification('⚠️ Modo offline - algumas funcionalidades limitadas', 'warning');
        });
}

// Inicializar integração quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeBackendIntegration, 1000);
});

console.log('🔗 EduCerto Backend Integration carregado!');
