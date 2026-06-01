# EduConnect — Sistema de Agendamento de Aulas

Plataforma web completa para agendamento de aulas particulares com três perfis de acesso: **Aluno**, **Professor** e **Administrador**.

---

## Funcionalidades

### Aluno
- Cadastro e login com autenticação por sessão PHP
- Dashboard com estatísticas: cursos inscritos, aulas assistidas, progresso geral
- Agendamento de aulas com professores disponíveis
- Histórico de aulas e próximas aulas
- Avaliação de professores após aulas
- Catálogo de cursos disponíveis
- Modo escuro

### Professor
- Dashboard com visão geral de alunos e cursos atribuídos
- Gerenciamento de agenda e próximas aulas agendadas
- Visualização de avaliações recebidas pelos alunos
- Modo escuro

### Administrador
- Painel com estatísticas gerais da plataforma
- Gerenciamento completo de usuários (aprovar, banir, editar)
- Gerenciamento de todos os agendamentos
- Moderação de avaliações
- Relatórios por período
- Atribuição de cursos a professores

---

## Tecnologias

| Camada | Tecnologia |
|--------|-----------|
| Back-end | PHP 8 + MySQLi |
| Banco de dados | MySQL |
| Front-end | HTML5, CSS3, JavaScript |
| Ícones | Font Awesome 6 |
| Fonte | Plus Jakarta Sans |

---

## Como Rodar Localmente

**Requisitos:** XAMPP (Apache + MySQL) com PHP 8+

```bash
# 1. Clone o repositório na pasta do servidor
git clone https://github.com/seu-usuario/educonnect.git "c:/xampp/htdocs/Sistema De Agendamento"

# 2. Inicie Apache e MySQL no XAMPP

# 3. Crie o banco de dados
# Acesse http://localhost/phpmyadmin
# Crie um banco chamado: educonnect
# Importe: public/database/educerto_setup.sql

# 4. Configure a conexão em public/db.php
#    Ajuste host, usuário e senha se necessário

# 5. Acesse no browser
http://localhost/Sistema%20De%20Agendamento/public/login.php
```

---

## Estrutura Principal

```
public/
├── login.php                # Autenticação
├── cadastro.php             # Registro de novos usuários
├── dashboard_aluno.php      # Painel do aluno
├── dashboard_professor.php  # Painel do professor
├── admin.html               # Painel administrativo
├── agendamento.php          # Agendamento de aulas
├── notas.php                # Notas e avaliações
├── relatorios.php           # Relatórios
├── chat.php                 # Chat
├── db.php                   # Conexão com banco
├── admin_api.php            # API do painel admin
└── avaliacoes_api.php       # API de avaliações
```

---

## Usuários de Teste

Acesse `public/criar_usuarios_demo.php` uma vez para criar os usuários de demonstração:

| Perfil | Email | Senha |
|--------|-------|-------|
| Administrador | admin@educonnect.com | admin123 |
| Professor | professor@educonnect.com | prof123 |
| Aluno | aluno@educonnect.com | aluno123 |

---

## Autor

**Camila Santos de Oliveira**
- GitHub: [github.com/CamilaSantosdeOliveira](https://github.com/CamilaSantosdeOliveira)
- LinkedIn: [linkedin.com/in/seu-perfil](https://linkedin.com/in/seu-perfil)
