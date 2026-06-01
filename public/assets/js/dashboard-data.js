/**
 * Dashboard Data Manager - EduConnect
 * Gerencia dados reais do dashboard consumindo APIs PHP
 */

class DashboardDataManager {
    constructor() {
        this.baseURL = window.location.origin + window.location.pathname.replace('dashboard.html', '');
        this.cache = new Map();
        this.cacheTimeout = 5 * 60 * 1000; // 5 minutos
    }

    // Função para fazer requisições HTTP
    async makeRequest(url, options = {}) {
        try {
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    ...options.headers
                },
                ...options
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Erro na requisição:', error);
            throw error;
        }
    }

    // Função para verificar cache
    getCachedData(key) {
        const cached = this.cache.get(key);
        if (cached && Date.now() - cached.timestamp < this.cacheTimeout) {
            return cached.data;
        }
        return null;
    }

    // Função para salvar no cache
    setCachedData(key, data) {
        this.cache.set(key, {
            data: data,
            timestamp: Date.now()
        });
    }

    // Buscar estatísticas gerais
    async getStats() {
        const cacheKey = 'dashboard_stats';
        const cached = this.getCachedData(cacheKey);
        
        if (cached) {
            return cached;
        }

        try {
            const data = await this.makeRequest(`${this.baseURL}api/dashboard_api.php?action=stats`);
            
            if (data.success) {
                this.setCachedData(cacheKey, data.data);
                return data.data;
            } else {
                throw new Error(data.error || 'Erro ao buscar estatísticas');
            }
        } catch (error) {
            console.error('Erro ao buscar estatísticas:', error);
            throw error;
        }
    }

    // Buscar agendamentos
    async getAgendamentos(filters = {}) {
        const cacheKey = `agendamentos_${JSON.stringify(filters)}`;
        const cached = this.getCachedData(cacheKey);
        
        if (cached) {
            return cached;
        }

        try {
            const params = new URLSearchParams({ action: 'list', ...filters });
            const data = await this.makeRequest(`${this.baseURL}api/dashboard_api.php?action=agendamentos&${params}`);
            
            if (data.success) {
                this.setCachedData(cacheKey, data.data);
                return data.data;
            } else {
                throw new Error(data.error || 'Erro ao buscar agendamentos');
            }
        } catch (error) {
            console.error('Erro ao buscar agendamentos:', error);
            throw error;
        }
    }

    // Buscar professores
    async getProfessores(filters = {}) {
        const cacheKey = `professores_${JSON.stringify(filters)}`;
        const cached = this.getCachedData(cacheKey);
        
        if (cached) {
            return cached;
        }

        try {
            const params = new URLSearchParams({ action: 'professores', ...filters });
            const data = await this.makeRequest(`${this.baseURL}api/dashboard_api.php?action=professores&${params}`);
            
            if (data.success) {
                this.setCachedData(cacheKey, data.data);
                return data.data;
            } else {
                throw new Error(data.error || 'Erro ao buscar professores');
            }
        } catch (error) {
            console.error('Erro ao buscar professores:', error);
            throw error;
        }
    }

    // Buscar alunos
    async getAlunos(filters = {}) {
        const cacheKey = `alunos_${JSON.stringify(filters)}`;
        const cached = this.getCachedData(cacheKey);
        
        if (cached) {
            return cached;
        }

        try {
            const params = new URLSearchParams({ action: 'alunos', ...filters });
            const data = await this.makeRequest(`${this.baseURL}api/dashboard_api.php?action=alunos&${params}`);
            
            if (data.success) {
                this.setCachedData(cacheKey, data.data);
                return data.data;
            } else {
                throw new Error(data.error || 'Erro ao buscar alunos');
            }
        } catch (error) {
            console.error('Erro ao buscar alunos:', error);
            throw error;
        }
    }

    // Buscar cursos
    async getCursos(filters = {}) {
        const cacheKey = `cursos_${JSON.stringify(filters)}`;
        const cached = this.getCachedData(cacheKey);
        
        if (cached) {
            return cached;
        }

        try {
            const params = new URLSearchParams({ action: 'cursos', ...filters });
            const data = await this.makeRequest(`${this.baseURL}api/dashboard_api.php?action=cursos&${params}`);
            
            if (data.success) {
                this.setCachedData(cacheKey, data.data);
                return data.data;
            } else {
                throw new Error(data.error || 'Erro ao buscar cursos');
            }
        } catch (error) {
            console.error('Erro ao buscar cursos:', error);
            throw error;
        }
    }

    // Buscar certificados
    async getCertificados(filters = {}) {
        const cacheKey = `certificados_${JSON.stringify(filters)}`;
        const cached = this.getCachedData(cacheKey);
        
        if (cached) {
            return cached;
        }

        try {
            const params = new URLSearchParams({ action: 'certificados', ...filters });
            const data = await this.makeRequest(`${this.baseURL}api/dashboard_api.php?action=certificados&${params}`);
            
            if (data.success) {
                this.setCachedData(cacheKey, data.data);
                return data.data;
            } else {
                throw new Error(data.error || 'Erro ao buscar certificados');
            }
        } catch (error) {
            console.error('Erro ao buscar certificados:', error);
            throw error;
        }
    }

    // Buscar relatórios
    async getRelatorios(tipo) {
        const cacheKey = `relatorios_${tipo}`;
        const cached = this.getCachedData(cacheKey);
        
        if (cached) {
            return cached;
        }

        try {
            const data = await this.makeRequest(`${this.baseURL}api/dashboard_api.php?action=relatorios&tipo=${tipo}`);
            
            if (data.success) {
                this.setCachedData(cacheKey, data.data);
                return data.data;
            } else {
                throw new Error(data.error || 'Erro ao buscar relatórios');
            }
        } catch (error) {
            console.error('Erro ao buscar relatórios:', error);
            throw error;
        }
    }

    // Atualizar estatísticas do dashboard
    async updateDashboardStats() {
        try {
            const stats = await this.getStats();
            
            // Atualizar contadores
            document.getElementById('total-professores').textContent = stats.estatisticas.total_professores || 0;
            document.getElementById('total-alunos').textContent = stats.estatisticas.total_alunos || 0;
            document.getElementById('total-cursos').textContent = stats.estatisticas.total_cursos || 0;
            document.getElementById('total-turmas').textContent = stats.estatisticas.total_turmas || 0;
            document.getElementById('aulas-hoje').textContent = stats.estatisticas.aulas_hoje || 0;
            document.getElementById('aulas-semana').textContent = stats.estatisticas.aulas_semana || 0;

            // Atualizar gráfico de cursos populares
            this.updateCursosPopularesChart(stats.cursos_populares);
            
            // Atualizar lista de professores em destaque
            this.updateProfessoresDestaque(stats.professores_destaque);

        } catch (error) {
            console.error('Erro ao atualizar estatísticas:', error);
            this.showError('Erro ao carregar estatísticas do dashboard');
        }
    }

    // Atualizar gráfico de cursos populares
    updateCursosPopularesChart(cursos) {
        const ctx = document.getElementById('cursosChart');
        if (!ctx || !cursos || cursos.length === 0) return;

        const labels = cursos.map(curso => curso.nome);
        const data = cursos.map(curso => curso.total_matriculas);

        if (window.cursosChart) {
            window.cursosChart.destroy();
        }

        window.cursosChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#8b5cf6',
                        '#ec4899'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Cursos Mais Populares'
                    }
                }
            }
        });
    }

    // Atualizar lista de professores em destaque
    updateProfessoresDestaque(professores) {
        const container = document.querySelector('.professores-destaque');
        if (!container || !professores || professores.length === 0) return;

        const html = professores.map(prof => `
            <div class="professor-destaque">
                <div class="professor-avatar">
                    <img src="${prof.avatar || 'assets/img/default-avatar.png'}" alt="${prof.nome}">
                </div>
                <div class="professor-info">
                    <h4>${prof.nome}</h4>
                    <p>${prof.total_turmas} turmas • ${prof.total_alunos} alunos</p>
                    <p class="media-notas">Média: ${(prof.media_notas || 0).toFixed(1)}</p>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    // Atualizar lista de agendamentos
    async updateAgendamentosList() {
        try {
            const agendamentos = await this.getAgendamentos();
            const container = document.querySelector('.agendamentos-lista');
            
            if (!container) return;

            if (agendamentos.length === 0) {
                container.innerHTML = '<p class="no-data">Nenhum agendamento encontrado</p>';
                return;
            }

            const html = agendamentos.map(agend => `
                <div class="agendamento-item">
                    <div class="agendamento-data">
                        <div class="data">
                            <span class="dia">${new Date(agend.data_aula).getDate()}</span>
                            <span class="mes">${new Date(agend.data_aula).toLocaleDateString('pt-BR', { month: 'short' })}</span>
                        </div>
                        <div class="horario">
                            <span>${agend.horario_inicio.substring(0, 5)} - ${agend.horario_fim.substring(0, 5)}</span>
                        </div>
                    </div>
                    <div class="agendamento-info">
                        <h4>${agend.tema || 'Aula Regular'}</h4>
                        <p><strong>Curso:</strong> ${agend.curso_nome}</p>
                        <p><strong>Turma:</strong> ${agend.turma_nome}</p>
                        <p><strong>Professor:</strong> ${agend.professor_nome}</p>
                        <p><strong>Sala:</strong> ${agend.sala || 'Online'}</p>
                    </div>
                    <div class="agendamento-acoes">
                        <span class="status-badge status-${agend.status}">${agend.status}</span>
                        <button class="btn btn-sm btn-primary" onclick="viewAgendamento(${agend.id})">Ver</button>
                    </div>
                </div>
            `).join('');

            container.innerHTML = html;

        } catch (error) {
            console.error('Erro ao atualizar agendamentos:', error);
            this.showError('Erro ao carregar agendamentos');
        }
    }

    // Atualizar lista de professores
    async updateProfessoresList() {
        try {
            const professores = await this.getProfessores();
            const container = document.querySelector('.professores-grid');
            
            if (!container) return;

            if (professores.length === 0) {
                container.innerHTML = '<p class="no-data">Nenhum professor encontrado</p>';
                return;
            }

            const html = professores.map(prof => `
                <div class="professor-card">
                    <div class="professor-avatar">
                        <img src="${prof.avatar || 'assets/img/default-avatar.png'}" alt="${prof.nome}">
                        <span class="status-${prof.status === 'ativo' ? 'online' : 'offline'}"></span>
                    </div>
                    <div class="professor-info">
                        <h4>${prof.nome}</h4>
                        <p class="email">${prof.email}</p>
                        <p class="bio">${prof.bio || 'Sem descrição disponível'}</p>
                    </div>
                    <div class="professor-stats">
                        <div class="stat">
                            <span class="number">${prof.total_turmas}</span>
                            <span class="label">Turmas</span>
                        </div>
                        <div class="stat">
                            <span class="number">${prof.total_alunos}</span>
                            <span class="label">Alunos</span>
                        </div>
                        <div class="stat">
                            <span class="number">${(prof.media_notas || 0).toFixed(1)}</span>
                            <span class="label">Média</span>
                        </div>
                    </div>
                    <div class="professor-acoes">
                        <button class="btn btn-sm btn-primary" onclick="viewProfessor(${prof.id})">Ver</button>
                        <button class="btn btn-sm btn-secondary" onclick="editProfessor(${prof.id})">Editar</button>
                    </div>
                </div>
            `).join('');

            container.innerHTML = html;

        } catch (error) {
            console.error('Erro ao atualizar professores:', error);
            this.showError('Erro ao carregar professores');
        }
    }

    // Atualizar lista de alunos
    async updateAlunosList() {
        try {
            const alunos = await this.getAlunos();
            const container = document.querySelector('.alunos-tabela');
            
            if (!container) return;

            if (alunos.length === 0) {
                container.innerHTML = '<p class="no-data">Nenhum aluno encontrado</p>';
                return;
            }

            const html = alunos.map(aluno => `
                <tr class="aluno-row">
                    <td>
                        <div class="aluno-info">
                            <div class="aluno-avatar">
                                <img src="assets/img/default-avatar.png" alt="${aluno.nome}">
                            </div>
                            <div>
                                <h5>${aluno.nome}</h5>
                                <p>${aluno.email}</p>
                            </div>
                        </div>
                    </td>
                    <td>${aluno.telefone || 'N/A'}</td>
                    <td>${aluno.total_matriculas}</td>
                    <td>${aluno.matriculas_ativas}</td>
                    <td>
                        <div class="progresso-bar">
                            <div class="progresso-fill" style="width: ${aluno.media_frequencia || 0}%"></div>
                        </div>
                        <span>${(aluno.media_frequencia || 0).toFixed(1)}%</span>
                    </td>
                    <td>${(aluno.media_notas || 0).toFixed(1)}</td>
                    <td>
                        <span class="status-badge status-${aluno.status}">${aluno.status}</span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewAluno(${aluno.id})">Ver</button>
                        <button class="btn btn-sm btn-secondary" onclick="editAluno(${aluno.id})">Editar</button>
                    </td>
                </tr>
            `).join('');

            container.innerHTML = html;

        } catch (error) {
            console.error('Erro ao atualizar alunos:', error);
            this.showError('Erro ao carregar alunos');
        }
    }

    // Atualizar lista de certificados
    async updateCertificadosList() {
        try {
            const certificados = await this.getCertificados();
            const container = document.querySelector('.certificados-grid');
            
            if (!container) return;

            if (certificados.length === 0) {
                container.innerHTML = '<p class="no-data">Nenhum certificado encontrado</p>';
                return;
            }

            const html = certificados.map(cert => `
                <div class="certificado-card">
                    <div class="certificado-header">
                        <div class="certificado-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="certificado-info">
                            <h4>${cert.aluno_nome}</h4>
                            <p><strong>Curso:</strong> ${cert.curso_nome}</p>
                            <p><strong>Turma:</strong> ${cert.turma_nome}</p>
                        </div>
                    </div>
                    <div class="certificado-status">
                        <span class="status-badge status-${cert.status}">${cert.status}</span>
                        <span class="data-emissao">Emitido em: ${new Date(cert.data_emissao).toLocaleDateString('pt-BR')}</span>
                    </div>
                    <div class="certificado-detalhes">
                        <p><strong>Código:</strong> ${cert.codigo_verificacao}</p>
                        <p><strong>Nota Final:</strong> ${cert.nota_final}</p>
                        <p><strong>Carga Horária:</strong> ${cert.carga_horaria}h</p>
                    </div>
                    <div class="certificado-acoes">
                        <button class="btn btn-sm btn-primary" onclick="viewCertificado('${cert.codigo_verificacao}')">Ver</button>
                        <button class="btn btn-sm btn-success" onclick="downloadCertificado('${cert.codigo_verificacao}')">Download</button>
                    </div>
                </div>
            `).join('');

            container.innerHTML = html;

        } catch (error) {
            console.error('Erro ao atualizar certificados:', error);
            this.showError('Erro ao carregar certificados');
        }
    }

    // Atualizar relatórios
    async updateRelatorios() {
        try {
            const tipos = ['desempenho_cursos', 'frequencia_alunos'];
            
            for (const tipo of tipos) {
                const relatorio = await this.getRelatorios(tipo);
                this.updateRelatorioChart(tipo, relatorio);
            }

        } catch (error) {
            console.error('Erro ao atualizar relatórios:', error);
            this.showError('Erro ao carregar relatórios');
        }
    }

    // Atualizar gráfico de relatório
    updateRelatorioChart(tipo, data) {
        if (tipo === 'desempenho_cursos') {
            this.updateDesempenhoChart(data);
        } else if (tipo === 'frequencia_alunos') {
            this.updateFrequenciaChart(data);
        }
    }

    // Atualizar gráfico de desempenho
    updateDesempenhoChart(data) {
        const ctx = document.getElementById('desempenhoChart');
        if (!ctx || !data || data.length === 0) return;

        const labels = data.map(item => item.curso);
        const notas = data.map(item => item.media_notas || 0);
        const frequencias = data.map(item => item.media_frequencia || 0);

        if (window.desempenhoChart) {
            window.desempenhoChart.destroy();
        }

        window.desempenhoChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Média de Notas',
                        data: notas,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Frequência (%)',
                        data: frequencias,
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 2,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Desempenho dos Cursos'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        max: 10
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    }

    // Atualizar gráfico de frequência
    updateFrequenciaChart(data) {
        const ctx = document.getElementById('distribuicaoChart');
        if (!ctx || !data || data.length === 0) return;

        const labels = data.map(item => item.aluno);
        const frequencias = data.map(item => item.frequencia || 0);

        if (window.distribuicaoChart) {
            window.distribuicaoChart.destroy();
        }

        window.distribuicaoChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: frequencias,
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#8b5cf6',
                        '#ec4899',
                        '#06b6d4',
                        '#84cc16',
                        '#f97316'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Distribuição de Frequência dos Alunos'
                    }
                }
            }
        });
    }

    // Limpar cache
    clearCache() {
        this.cache.clear();
        console.log('Cache limpo');
    }

    // Mostrar erro
    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-error';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" class="close-btn">&times;</button>
        `;
        
        document.body.appendChild(errorDiv);
        
        setTimeout(() => {
            if (errorDiv.parentElement) {
                errorDiv.remove();
            }
        }, 5000);
    }

    // Mostrar sucesso
    showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success';
        successDiv.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" class="close-btn">&times;</button>
        `;
        
        document.body.appendChild(successDiv);
        
        setTimeout(() => {
            if (successDiv.parentElement) {
                successDiv.remove();
            }
        }, 3000);
    }

    // Inicializar dashboard
    async init() {
        try {
            await this.updateDashboardStats();
            await this.updateAgendamentosList();
            await this.updateProfessoresList();
            await this.updateAlunosList();
            await this.updateCertificadosList();
            await this.updateRelatorios();
            
            this.showSuccess('Dashboard carregado com sucesso!');
        } catch (error) {
            console.error('Erro ao inicializar dashboard:', error);
            this.showError('Erro ao inicializar dashboard');
        }
    }
}

// Instanciar gerenciador de dados
const dataManager = new DashboardDataManager();

// Funções globais para interação
function viewAgendamento(id) {
    console.log('Visualizando agendamento:', id);
    // Implementar modal de visualização
}

function viewProfessor(id) {
    console.log('Visualizando professor:', id);
    // Implementar modal de visualização
}

function editProfessor(id) {
    console.log('Editando professor:', id);
    // Implementar modal de edição
}

function viewAluno(id) {
    console.log('Visualizando aluno:', id);
    // Implementar modal de visualização
}

function editAluno(id) {
    console.log('Editando aluno:', id);
    // Implementar modal de edição
}

function viewCertificado(codigo) {
    console.log('Visualizando certificado:', codigo);
    // Implementar modal de visualização
}

function downloadCertificado(codigo) {
    console.log('Baixando certificado:', codigo);
    // Implementar download
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    dataManager.init();
});

// Atualizar dados a cada 5 minutos
setInterval(() => {
    dataManager.updateDashboardStats();
}, 5 * 60 * 1000);

// Exportar para uso global
window.DashboardDataManager = DashboardDataManager;
window.dataManager = dataManager;





































