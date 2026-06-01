/**
 * EduConnect - Sistema de Validação de Formulários
 * Versão: 3.0
 * 
 * Validações client-side para melhor UX
 */

class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.errors = [];
        this.rules = {};
    }

    /**
     * Adicionar regra de validação
     */
    addRule(fieldName, rule, message) {
        if (!this.rules[fieldName]) {
            this.rules[fieldName] = [];
        }
        this.rules[fieldName].push({ rule, message });
        return this;
    }

    /**
     * Validar campo individual
     */
    validateField(fieldName, value) {
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        const fieldRules = this.rules[fieldName] || [];
        let isValid = true;
        let errorMessage = '';

        for (const { rule, message } of fieldRules) {
            if (!rule(value)) {
                isValid = false;
                errorMessage = message;
                this.showFieldError(field, message);
                break;
            } else {
                this.clearFieldError(field);
            }
        }

        return { isValid, errorMessage };
    }

    /**
     * Validar formulário completo
     */
    validate() {
        this.errors = [];
        let isValid = true;

        for (const fieldName in this.rules) {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (!field) continue;

            const value = field.value.trim();
            const result = this.validateField(fieldName, value);

            if (!result.isValid) {
                isValid = false;
                this.errors.push({
                    field: fieldName,
                    message: result.errorMessage
                });
            }
        }

        return isValid;
    }

    /**
     * Mostrar erro no campo
     */
    showFieldError(field, message) {
        if (!field) return;

        // Remover erro anterior
        this.clearFieldError(field);

        // Adicionar classe de erro
        field.classList.add('error');
        
        // Criar elemento de erro
        const errorElement = document.createElement('div');
        errorElement.className = 'field-error';
        errorElement.textContent = message;
        errorElement.style.cssText = 'color: #dc2626; font-size: 0.875rem; margin-top: 4px; display: flex; align-items: center; gap: 4px;';
        errorElement.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

        // Inserir após o campo
        field.parentNode.insertBefore(errorElement, field.nextSibling);
    }

    /**
     * Limpar erro do campo
     */
    clearFieldError(field) {
        if (!field) return;

        field.classList.remove('error');
        const errorElement = field.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    /**
     * Obter erros
     */
    getErrors() {
        return this.errors;
    }
}

// Regras de validação comuns
const ValidationRules = {
    required: (value) => value !== null && value !== undefined && value.trim() !== '',
    
    email: (value) => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(value);
    },
    
    minLength: (min) => (value) => value.length >= min,
    
    maxLength: (max) => (value) => value.length <= max,
    
    numeric: (value) => /^\d+$/.test(value),
    
    phone: (value) => {
        const phoneRegex = /^[\d\s\(\)\-\+]+$/;
        return phoneRegex.test(value) && value.replace(/\D/g, '').length >= 10;
    },
    
    password: (value) => value.length >= 6,
    
    passwordStrong: (value) => {
        return value.length >= 8 && 
               /[A-Z]/.test(value) && 
               /[a-z]/.test(value) && 
               /\d/.test(value);
    },
    
    match: (fieldName) => (value) => {
        const otherField = document.querySelector(`[name="${fieldName}"]`);
        return otherField && value === otherField.value;
    },
    
    url: (value) => {
        try {
            new URL(value);
            return true;
        } catch {
            return false;
        }
    },
    
    date: (value) => {
        const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
        if (!dateRegex.test(value)) return false;
        const date = new Date(value);
        return date instanceof Date && !isNaN(date);
    },
    
    time: (value) => {
        const timeRegex = /^([01]\d|2[0-3]):([0-5]\d)$/;
        return timeRegex.test(value);
    }
};

// Função helper para validar formulário rapidamente
function validateForm(formId, rules) {
    const validator = new FormValidator(formId);
    
    for (const fieldName in rules) {
        for (const rule of rules[fieldName]) {
            validator.addRule(fieldName, rule.rule, rule.message);
        }
    }
    
    return validator.validate();
}

// Exportar para uso global
if (typeof window !== 'undefined') {
    window.FormValidator = FormValidator;
    window.ValidationRules = ValidationRules;
    window.validateForm = validateForm;
}


