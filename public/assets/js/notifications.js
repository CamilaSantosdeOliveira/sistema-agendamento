// Sistema de Notificações em Tempo Real - EduConnect
class NotificationSystem {
    constructor() {
        this.notifications = [];
        this.container = null;
        this.sound = null;
        this.init();
    }
    
    init() {
        this.createContainer();
        this.loadNotificationSound();
        this.checkPermissions();
        
        // Verificar novas notificações a cada 30 segundos
        setInterval(() => this.checkNewNotifications(), 30000);
        
        // Verificar imediatamente ao carregar
        this.checkNewNotifications();
    }
    
    createContainer() {
        // Container para notificações
        this.container = document.createElement('div');
        this.container.id = 'notification-container';
        this.container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 400px;
            pointer-events: none;
        `;
        document.body.appendChild(this.container);
        
        // Botão de notificações no navbar
        this.createNotificationButton();
    }
    
    createNotificationButton() {
        const navLinks = document.querySelector('.nav-links');
        if (navLinks) {
            const notificationBtn = document.createElement('div');
            notificationBtn.innerHTML = `
                <button id="notification-btn" style="
                    background: rgba(30, 64, 175, 0.1);
                    border: 2px solid #1e40af;
                    border-radius: 50%;
                    width: 45px;
                    height: 45px;
                    cursor: pointer;
                    position: relative;
                    transition: all 0.3s ease;
                ">
                    🔔
                    <span id="notification-count" style="
                        position: absolute;
                        top: -8px;
                        right: -8px;
                        background: #ef4444;
                        color: white;
                        border-radius: 50%;
                        width: 20px;
                        height: 20px;
                        display: none;
                        align-items: center;
                        justify-content: center;
                        font-size: 12px;
                        font-weight: bold;
                    ">0</span>
                </button>
            `;
            navLinks.appendChild(notificationBtn);
            
            document.getElementById('notification-btn').onclick = () => this.showNotificationPanel();
        }
    }
    
    loadNotificationSound() {
        this.sound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmEeATiS2fi4dikFLY/S9d2PQgwUXLjn66hWFAhGmdxUeFNaYGhwglB4cUpCfXGKd5V3mnmve6B9oH6iap8UeBR/YXlvfnGB') || null;
    }
    
    async checkPermissions() {
        if ('Notification' in window && Notification.permission === 'default') {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                this.showNotification('✅ Notificações Ativadas', 'Você receberá alertas sobre suas aulas!', 'success');
            }
        }
    }
    
    async checkNewNotifications() {
        try {
            const response = await fetch('api_melhorada.php?notificacoes=true');
            if (response.ok) {
                const data = await response.json();
                if (data.novas_notificacoes && data.novas_notificacoes.length > 0) {
                    data.novas_notificacoes.forEach(notif => {
                        this.addNotification(notif);
                    });
                }
            }
        } catch (error) {
            console.log('Erro ao verificar notificações:', error);
        }
    }
    
    addNotification(notification) {
        const notifElement = document.createElement('div');
        notifElement.className = 'notification-toast';
        notifElement.style.cssText = `
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            margin-bottom: 15px;
            padding: 20px;
            border-left: 5px solid ${this.getTypeColor(notification.type)};
            transform: translateX(450px);
            transition: all 0.4s ease;
            pointer-events: auto;
            cursor: pointer;
            position: relative;
            backdrop-filter: blur(20px);
        `;
        
        notifElement.innerHTML = `
            <div style="display: flex; align-items: flex-start; gap: 15px;">
                <div style="font-size: 24px;">${this.getTypeIcon(notification.type)}</div>
                <div style="flex: 1;">
                    <h4 style="margin: 0 0 8px 0; color: #1f2937; font-size: 16px;">
                        ${notification.title || notification.titulo || 'Nova Notificação'}
                    </h4>
                    <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.4;">
                        ${notification.message || notification.mensagem || ''}
                    </p>
                    <small style="color: #9ca3af; font-size: 12px; margin-top: 8px; display: block;">
                        ${this.formatTime(notification.criado_em || new Date())}
                    </small>
                </div>
                <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                        style="background: none; border: none; font-size: 18px; cursor: pointer; color: #9ca3af;">
                    ✕
                </button>
            </div>
        `;
        
        this.container.appendChild(notifElement);
        
        // Animar entrada
        setTimeout(() => {
            notifElement.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto-remove após 8 segundos
        setTimeout(() => {
            if (notifElement.parentElement) {
                notifElement.style.transform = 'translateX(450px)';
                setTimeout(() => {
                    if (notifElement.parentElement) {
                        notifElement.remove();
                    }
                }, 400);
            }
        }, 8000);
        
        // Tocar som
        this.playNotificationSound();
        
        // Notificação do navegador
        this.showBrowserNotification(notification);
        
        // Atualizar contador
        this.updateNotificationCount();
    }
    
    showNotification(title, message, type = 'info') {
        this.addNotification({
            title: title,
            message: message,
            type: type,
            criado_em: new Date()
        });
    }
    
    showBrowserNotification(notification) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const browserNotif = new Notification(
                notification.title || notification.titulo || 'EduConnect', 
                {
                    body: notification.message || notification.mensagem || '',
                    icon: '/favicon.ico',
                    badge: '/favicon.ico',
                    tag: 'educonnect-' + Date.now()
                }
            );
            
            browserNotif.onclick = () => {
                window.focus();
                browserNotif.close();
            };
            
            setTimeout(() => browserNotif.close(), 8000);
        }
    }
    
    playNotificationSound() {
        if (this.sound) {
            this.sound.currentTime = 0;
            this.sound.play().catch(e => console.log('Não foi possível tocar som de notificação'));
        }
    }
    
    getTypeColor(type) {
        const colors = {
            'success': '#10b981',
            'error': '#ef4444',
            'warning': '#f59e0b',
            'info': '#3b82f6',
            'agendamento': '#10b981',
            'confirmacao': '#3b82f6',
            'cancelamento': '#ef4444',
            'lembrete': '#f59e0b'
        };
        return colors[type] || colors['info'];
    }
    
    getTypeIcon(type) {
        const icons = {
            'success': '✅',
            'error': '❌',
            'warning': '⚠️',
            'info': 'ℹ️',
            'agendamento': '📅',
            'confirmacao': '✅',
            'cancelamento': '❌',
            'lembrete': '⏰'
        };
        return icons[type] || icons['info'];
    }
    
    formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) return 'Agora mesmo';
        if (diff < 3600000) return Math.floor(diff / 60000) + ' min atrás';
        if (diff < 86400000) return Math.floor(diff / 3600000) + 'h atrás';
        
        return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    updateNotificationCount() {
        const count = document.querySelectorAll('.notification-toast').length;
        const countElement = document.getElementById('notification-count');
        
        if (countElement) {
            if (count > 0) {
                countElement.textContent = count > 9 ? '9+' : count;
                countElement.style.display = 'flex';
            } else {
                countElement.style.display = 'none';
            }
        }
    }
    
    showNotificationPanel() {
        // Implementar painel completo de notificações se necessário
        alert('Painel de notificações - Em desenvolvimento!\n\nNotificações ativas: ' + 
              document.querySelectorAll('.notification-toast').length);
    }
}

// Inicializar sistema de notificações quando a página carregar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.notificationSystem = new NotificationSystem();
    });
} else {
    window.notificationSystem = new NotificationSystem();
}

// Funções globais para usar em outros scripts
window.showNotification = (title, message, type = 'info') => {
    if (window.notificationSystem) {
        window.notificationSystem.showNotification(title, message, type);
    }
};

window.showSuccess = (message) => window.showNotification('✅ Sucesso!', message, 'success');
window.showError = (message) => window.showNotification('❌ Erro!', message, 'error');
window.showWarning = (message) => window.showNotification('⚠️ Atenção!', message, 'warning');
window.showInfo = (message) => window.showNotification('ℹ️ Informação', message, 'info');
