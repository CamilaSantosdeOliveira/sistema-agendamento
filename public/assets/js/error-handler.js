/**
 * EduConnect - Sistema de Tratamento de Erros
 * Versão: 3.0
 * 
 * Tratamento centralizado de erros para melhor UX
 */

class ErrorHandler {
    constructor() {
        this.errors = [];
        this.setupGlobalErrorHandling();
    }

    /**
     * Configurar tratamento global de erros
     */
    setupGlobalErrorHandling() {
        // Capturar erros JavaScript não tratados
        window.addEventListener('error', (event) => {
            this.logError('JavaScript Error', {
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                error: event.error
            });
        });

        // Capturar promessas rejeitadas
        window.addEventListener('unhandledrejection', (event) => {
            this.logError('Unhandled Promise Rejection', {
                reason: event.reason,
                promise: event.promise
            });
        });
    }

    /**
     * Tratar erro de API
     */
    handleAPIError(error, context = '') {
        console.error('API Error:', error);
        
        let message = 'Erro ao processar solicitação.';
        
        if (error.response) {
            // Erro de resposta HTTP
            const status = error.response.status;
            const data = error.response.data || {};
            
            switch (status) {
                case 400:
                    message = data.message || 'Dados inválidos. Verifique os campos preenchidos.';
                    break;
                case 401:
                    message = 'Sessão expirada. Faça login novamente.';
                    this.redirectToLogin();
                    return;
                case 403:
                    message = 'Você não tem permissão para realizar esta ação.';
                    break;
                case 404:
                    message = 'Recurso não encontrado.';
                    break;
                case 500:
                    message = 'Erro interno do servidor. Tente novamente mais tarde.';
                    break;
                default:
                    message = data.message || `Erro ${status}: ${error.response.statusText}`;
            }
        } else if (error.request) {
            // Erro de rede
            message = 'Erro de conexão. Verifique sua internet e tente novamente.';
        } else {
            // Outro erro
            message = error.message || 'Erro desconhecido.';
        }
        
        this.showError(message, context);
        this.logError('API Error', { error, context });
    }

    /**
     * Tratar erro de validação
     */
    handleValidationError(errors) {
        if (typeof errors === 'string') {
            this.showError(errors);
            return;
        }
        
        if (Array.isArray(errors)) {
            errors.forEach(error => {
                if (error.field) {
                    this.showFieldError(error.field, error.message);
                } else {
                    this.showError(error.message || error);
                }
            });
        } else if (typeof errors === 'object') {
            for (const field in errors) {
                this.showFieldError(field, errors[field]);
            }
        }
    }

    /**
     * Mostrar erro geral
     */
    showError(message, context = '') {
        // Usar toast se disponível
        if (typeof showToast === 'function') {
            showToast('Erro', message, 'error');
        } else {
            // Fallback para alert
            alert('Erro: ' + message);
        }
        
        this.logError('User Error', { message, context });
    }

    /**
     * Mostrar erro em campo específico
     */
    showFieldError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field) return;
        
        // Adicionar classe de erro
        field.classList.add('error');
        
        // Criar ou atualizar mensagem de erro
        let errorElement = field.parentNode.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'field-error';
            field.parentNode.insertBefore(errorElement, field.nextSibling);
        }
        
        errorElement.textContent = message;
        errorElement.style.cssText = 'color: #dc2626; font-size: 0.875rem; margin-top: 4px;';
    }

    /**
     * Limpar erro de campo
     */
    clearFieldError(fieldName) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field) return;
        
        field.classList.remove('error');
        const errorElement = field.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    /**
     * Log de erro
     */
    logError(type, details) {
        const error = {
            type,
            details,
            timestamp: new Date().toISOString(),
            url: window.location.href,
            userAgent: navigator.userAgent
        };
        
        this.errors.push(error);
        
        // Em produção, enviar para servidor de logs
        if (window.location.hostname !== 'localhost') {
            this.sendErrorToServer(error);
        }
        
        // Log no console em desenvolvimento
        if (window.location.hostname === 'localhost') {
            console.error('Error logged:', error);
        }
    }

    /**
     * Enviar erro para servidor
     */
    async sendErrorToServer(error) {
        try {
            await fetch('api/log_error.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(error)
            });
        } catch (e) {
            // Falha silenciosa - não quebrar o fluxo
            console.error('Failed to send error to server:', e);
        }
    }

    /**
     * Redirecionar para login
     */
    redirectToLogin() {
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 2000);
    }

    /**
     * Obter todos os erros
     */
    getErrors() {
        return this.errors;
    }

    /**
     * Limpar erros
     */
    clearErrors() {
        this.errors = [];
    }
}

// Instância global
const errorHandler = new ErrorHandler();

// Exportar para uso global
if (typeof window !== 'undefined') {
    window.ErrorHandler = ErrorHandler;
    window.errorHandler = errorHandler;
}


