import {
  ArrowRight,
  BookOpen,
  CalendarDays,
  CheckCircle2,
  Clock3,
  GraduationCap,
  LayoutDashboard,
  MessageCircle,
  ShieldCheck,
  Star,
  Users,
} from 'lucide-react';

const stats = [
  { value: '1.250+', label: 'aulas agendadas' },
  { value: '98%', label: 'satisfação dos alunos' },
  { value: '35+', label: 'professores ativos' },
];

const subjects = ['Matemática', 'Português', 'Inglês', 'Física', 'Química', 'Redação'];

const appointments = [
  { student: 'Marina Lopes', subject: 'Matemática', teacher: 'Prof. Rafael', time: 'Hoje, 14:00', status: 'Confirmado' },
  { student: 'Lucas Andrade', subject: 'Redação', teacher: 'Profa. Camila', time: 'Amanhã, 09:30', status: 'Pendente' },
  { student: 'Bianca Souza', subject: 'Inglês', teacher: 'Prof. Daniel', time: 'Sex, 16:00', status: 'Confirmado' },
];

const features = [
  {
    icon: CalendarDays,
    title: 'Agendamento inteligente',
    description: 'Organize aulas por data, professor, matéria e disponibilidade com uma experiência simples e fluida.',
  },
  {
    icon: Users,
    title: 'Perfis para aluno e professor',
    description: 'Ambientes separados para acompanhar aulas, histórico, horários e informações importantes.',
  },
  {
    icon: LayoutDashboard,
    title: 'Dashboard profissional',
    description: 'Indicadores, próximos agendamentos e visão geral do sistema em uma interface clara.',
  },
  {
    icon: MessageCircle,
    title: 'Comunicação centralizada',
    description: 'Estrutura preparada para mensagens, notificações e acompanhamento do processo educacional.',
  },
];

function App() {
  return (
    <main className="app-shell">
      <nav className="navbar">
        <a className="nav-logo" href="#inicio" aria-label="EduConnect início">
          <span className="logo-icon"><GraduationCap size={22} /></span>
          EduConnect
        </a>

        <div className="nav-links" aria-label="Navegação principal">
          <a href="#recursos">Recursos</a>
          <a href="#agenda">Agenda</a>
          <a href="#professores">Professores</a>
          <a className="nav-cta" href="#agendar">Agendar aula</a>
        </div>
      </nav>

      <section className="hero-section" id="inicio">
        <div className="hero-content">
          <div className="hero-badge">
            <ShieldCheck size={17} />
            Sistema educacional moderno e seguro
          </div>

          <h1>Agende aulas particulares com praticidade e confiança.</h1>
          <p>
            Uma plataforma profissional para conectar alunos e professores, organizar horários e acompanhar aulas em um só lugar.
          </p>

          <div className="hero-actions">
            <a className="btn btn-primary" href="#agendar">
              Começar agendamento
              <ArrowRight size={18} />
            </a>
            <a className="btn btn-secondary" href="#recursos">Ver recursos</a>
          </div>

          <div className="stats-grid">
            {stats.map((item) => (
              <div className="stat-card" key={item.label}>
                <strong>{item.value}</strong>
                <span>{item.label}</span>
              </div>
            ))}
          </div>
        </div>

        <aside className="hero-panel" aria-label="Painel de agendamentos">
          <div className="panel-header">
            <div>
              <span>Dashboard</span>
              <h2>Próximas aulas</h2>
            </div>
            <div className="panel-icon"><CalendarDays size={22} /></div>
          </div>

          <div className="schedule-list">
            {appointments.map((appointment) => (
              <article className="schedule-card" key={`${appointment.student}-${appointment.time}`}>
                <div className="schedule-avatar">{appointment.student.charAt(0)}</div>
                <div className="schedule-info">
                  <h3>{appointment.student}</h3>
                  <p>{appointment.subject} com {appointment.teacher}</p>
                  <span><Clock3 size={14} /> {appointment.time}</span>
                </div>
                <strong className={appointment.status === 'Confirmado' ? 'status confirmed' : 'status pending'}>
                  {appointment.status}
                </strong>
              </article>
            ))}
          </div>
        </aside>
      </section>

      <section className="section" id="recursos">
        <div className="section-heading">
          <span>Recursos principais</span>
          <h2>Visual moderno, organização real e experiência de sistema profissional.</h2>
        </div>

        <div className="features-grid">
          {features.map((feature) => {
            const Icon = feature.icon;
            return (
              <article className="feature-card" key={feature.title}>
                <div className="feature-icon"><Icon size={25} /></div>
                <h3>{feature.title}</h3>
                <p>{feature.description}</p>
              </article>
            );
          })}
        </div>
      </section>

      <section className="section split-section" id="agenda">
        <div className="glass-card">
          <span className="eyebrow">Agenda semanal</span>
          <h2>Controle de horários com foco em usabilidade.</h2>
          <p>
            A interface mantém a identidade azul e roxa do projeto original, com cards mais limpos, espaçamento melhor e hierarquia visual de nível profissional.
          </p>

          <div className="subject-tags">
            {subjects.map((subject) => <span key={subject}>{subject}</span>)}
          </div>
        </div>

        <div className="availability-card" id="agendar">
          <div className="availability-header">
            <BookOpen size={24} />
            <div>
              <span>Nova aula</span>
              <h3>Solicitar agendamento</h3>
            </div>
          </div>

          <form className="booking-form">
            <label>
              Nome do aluno
              <input type="text" placeholder="Ex: Ana Clara" />
            </label>
            <label>
              Matéria
              <select defaultValue="">
                <option value="" disabled>Selecione uma matéria</option>
                {subjects.map((subject) => <option key={subject}>{subject}</option>)}
              </select>
            </label>
            <div className="form-row">
              <label>
                Data
                <input type="date" />
              </label>
              <label>
                Horário
                <input type="time" />
              </label>
            </div>
            <button className="btn btn-primary" type="button">
              Enviar solicitação
              <CheckCircle2 size={18} />
            </button>
          </form>
        </div>
      </section>

      <section className="section teachers-section" id="professores">
        <div className="section-heading compact">
          <span>Equipe educacional</span>
          <h2>Professores em destaque</h2>
        </div>

        <div className="teachers-grid">
          {['Rafael Martins', 'Camila Rocha', 'Daniel Ferreira'].map((teacher, index) => (
            <article className="teacher-card" key={teacher}>
              <div className="teacher-photo">{teacher.charAt(0)}</div>
              <h3>{teacher}</h3>
              <p>{['Matemática e Física', 'Português e Redação', 'Inglês e Conversação'][index]}</p>
              <div className="rating"><Star size={16} fill="currentColor" /> 4.9</div>
            </article>
          ))}
        </div>
      </section>
    </main>
  );
}

export default App;
