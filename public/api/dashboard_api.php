<?php
/**
 * API do Dashboard - EduConnect
 * Fornece dados estatísticos e informações gerais do sistema
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Função para retornar resposta JSON
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Função para validar token de autenticação (simplificado)
function validateAuth() {
    // Em produção, implementar validação JWT ou sessão
    return true;
}

try {
    // Validar autenticação
    if (!validateAuth()) {
        jsonResponse(['error' => 'Não autorizado'], 401);
    }
    
    $action = $_GET['action'] ?? 'stats';
    $db = getDB();
    
    switch ($action) {
        case 'stats':
            // Estatísticas gerais do sistema
            $stats = dbFetch("
                SELECT 
                    (SELECT COUNT(*) FROM usuarios WHERE tipo = 'professor' AND status = 'ativo') as total_professores,
                    (SELECT COUNT(*) FROM usuarios WHERE tipo = 'aluno' AND status = 'ativo') as total_alunos,
                    (SELECT COUNT(*) FROM cursos WHERE status = 'ativo') as total_cursos,
                    (SELECT COUNT(*) FROM turmas WHERE status = 'ativa') as total_turmas,
                    (SELECT COUNT(*) FROM matriculas WHERE status = 'ativa') as total_matriculas,
                    (SELECT COUNT(*) FROM agendamentos WHERE status = 'agendada' AND data_aula >= CURDATE()) as aulas_hoje,
                    (SELECT COUNT(*) FROM agendamentos WHERE status = 'agendada' AND data_aula BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)) as aulas_semana
            ");
            
            // Calcular estatísticas adicionais
            $cursos_populares = dbFetchAll("
                SELECT 
                    c.nome,
                    c.categoria,
                    COUNT(m.id) as total_matriculas,
                    AVG(m.frequencia) as media_frequencia
                FROM cursos c
                LEFT JOIN turmas t ON c.id = t.curso_id
                LEFT JOIN matriculas m ON t.id = m.turma_id
                WHERE c.status = 'ativo'
                GROUP BY c.id
                ORDER BY total_matriculas DESC
                LIMIT 5
            ");
            
            $professores_destaque = dbFetchAll("
                SELECT 
                    u.nome,
                    u.email,
                    COUNT(DISTINCT t.id) as total_turmas,
                    COUNT(DISTINCT m.id) as total_alunos,
                    AVG(av.nota) as media_notas
                FROM usuarios u
                LEFT JOIN turmas t ON u.id = t.professor_id
                LEFT JOIN matriculas m ON t.id = m.turma_id
                LEFT JOIN avaliacoes av ON m.id = av.matricula_id
                WHERE u.tipo = 'professor' AND u.status = 'ativo'
                GROUP BY u.id
                ORDER BY total_alunos DESC
                LIMIT 5
            ");
            
            jsonResponse([
                'success' => true,
                'data' => [
                    'estatisticas' => $stats,
                    'cursos_populares' => $cursos_populares,
                    'professores_destaque' => $professores_destaque,
                    'ultima_atualizacao' => date('Y-m-d H:i:s')
                ]
            ]);
            break;
            
        case 'agendamentos':
            // Agendamentos recentes
            $filtro_curso = $_GET['curso'] ?? '';
            $filtro_data = $_GET['data'] ?? '';
            
            $where = "WHERE a.status != 'cancelada'";
            $params = [];
            
            if ($filtro_curso) {
                $where .= " AND c.nome LIKE :curso";
                $params[':curso'] = "%{$filtro_curso}%";
            }
            
            if ($filtro_data) {
                $where .= " AND a.data_aula = :data";
                $params[':data'] = $filtro_data;
            }
            
            $agendamentos = dbFetchAll("
                SELECT 
                    a.id,
                    a.data_aula,
                    a.horario_inicio,
                    a.horario_fim,
                    a.tema,
                    a.status,
                    c.nome as curso_nome,
                    t.nome as turma_nome,
                    CONCAT(u.nome, ' (', u.email, ')') as professor,
                    t.sala,
                    t.modalidade
                FROM agendamentos a
                JOIN turmas t ON a.turma_id = t.id
                JOIN cursos c ON t.curso_id = c.id
                JOIN usuarios u ON t.professor_id = u.id
                {$where}
                ORDER BY a.data_aula DESC, a.horario_inicio
                LIMIT 50
            ", $params);
            
            jsonResponse([
                'success' => true,
                'data' => $agendamentos,
                'total' => count($agendamentos)
            ]);
            break;
            
        case 'professores':
            // Lista de professores
            $filtro_status = $_GET['status'] ?? '';
            
            $where = "WHERE u.tipo = 'professor'";
            $params = [];
            
            if ($filtro_status) {
                $where .= " AND u.status = :status";
                $params[':status'] = $filtro_status;
            }
            
            $professores = dbFetchAll("
                SELECT 
                    u.id,
                    u.nome,
                    u.email,
                    u.bio,
                    u.avatar,
                    u.status,
                    u.ultimo_login,
                    COUNT(DISTINCT t.id) as total_turmas,
                    COUNT(DISTINCT m.id) as total_alunos,
                    AVG(av.nota) as media_notas
                FROM usuarios u
                LEFT JOIN turmas t ON u.id = t.professor_id
                LEFT JOIN matriculas m ON t.id = m.turma_id
                LEFT JOIN avaliacoes av ON m.id = av.matricula_id
                {$where}
                GROUP BY u.id
                ORDER BY u.nome
            ", $params);
            
            jsonResponse([
                'success' => true,
                'data' => $professores,
                'total' => count($professores)
            ]);
            break;
            
        case 'alunos':
            // Lista de alunos
            $filtro_curso = $_GET['curso'] ?? '';
            $filtro_status = $_GET['status'] ?? '';
            $busca = $_GET['busca'] ?? '';
            
            $where = "WHERE u.tipo = 'aluno'";
            $params = [];
            
            if ($filtro_curso) {
                $where .= " AND c.nome LIKE :curso";
                $params[':curso'] = "%{$filtro_curso}%";
            }
            
            if ($filtro_status) {
                $where .= " AND m.status = :status";
                $params[':status'] = $filtro_status;
            }
            
            if ($busca) {
                $where .= " AND (u.nome LIKE :busca OR u.email LIKE :busca)";
                $params[':busca'] = "%{$busca}%";
            }
            
            $alunos = dbFetchAll("
                SELECT 
                    u.id,
                    u.nome,
                    u.email,
                    u.telefone,
                    u.status,
                    u.data_criacao,
                    COUNT(DISTINCT m.id) as total_matriculas,
                    COUNT(DISTINCT CASE WHEN m.status = 'ativa' THEN m.id END) as matriculas_ativas,
                    AVG(m.frequencia) as media_frequencia,
                    AVG(av.nota) as media_notas,
                    GROUP_CONCAT(DISTINCT c.nome SEPARATOR ', ') as cursos
                FROM usuarios u
                LEFT JOIN matriculas m ON u.id = m.aluno_id
                LEFT JOIN turmas t ON m.turma_id = t.id
                LEFT JOIN cursos c ON t.curso_id = c.id
                LEFT JOIN avaliacoes av ON m.id = av.matricula_id
                {$where}
                GROUP BY u.id
                ORDER BY u.nome
                LIMIT 100
            ", $params);
            
            jsonResponse([
                'success' => true,
                'data' => $alunos,
                'total' => count($alunos)
            ]);
            break;
            
        case 'cursos':
            // Lista de cursos
            $filtro_categoria = $_GET['categoria'] ?? '';
            $filtro_nivel = $_GET['nivel'] ?? '';
            
            $where = "WHERE c.status != 'inativo'";
            $params = [];
            
            if ($filtro_categoria) {
                $where .= " AND c.categoria = :categoria";
                $params[':categoria'] = $filtro_categoria;
            }
            
            if ($filtro_nivel) {
                $where .= " AND c.nivel = :nivel";
                $params[':nivel'] = $filtro_nivel;
            }
            
            $cursos = dbFetchAll("
                SELECT 
                    c.id,
                    c.nome,
                    c.descricao,
                    c.categoria,
                    c.nivel,
                    c.duracao_horas,
                    c.preco,
                    c.vagas_maximas,
                    c.vagas_disponiveis,
                    c.imagem,
                    c.status,
                    c.data_criacao,
                    COUNT(DISTINCT t.id) as total_turmas,
                    COUNT(DISTINCT m.id) as total_matriculas,
                    AVG(m.frequencia) as media_frequencia
                FROM cursos c
                LEFT JOIN turmas t ON c.id = t.curso_id
                LEFT JOIN matriculas m ON t.id = m.turma_id
                {$where}
                GROUP BY c.id
                ORDER BY c.nome
            ", $params);
            
            jsonResponse([
                'success' => true,
                'data' => $cursos,
                'total' => count($cursos)
            ]);
            break;
            
        case 'certificados':
            // Lista de certificados
            $filtro_status = $_GET['status'] ?? '';
            $filtro_curso = $_GET['curso'] ?? '';
            
            $where = "WHERE 1=1";
            $params = [];
            
            if ($filtro_status) {
                $where .= " AND cert.status = :status";
                $params[':status'] = $filtro_status;
            }
            
            if ($filtro_curso) {
                $where .= " AND c.nome LIKE :curso";
                $params[':curso'] = "%{$filtro_curso}%";
            }
            
            $certificados = dbFetchAll("
                SELECT 
                    cert.id,
                    cert.codigo_verificacao,
                    cert.data_emissao,
                    cert.data_conclusao_curso,
                    cert.nota_final,
                    cert.carga_horaria,
                    cert.status,
                    cert.url_download,
                    u.nome as aluno_nome,
                    u.email as aluno_email,
                    c.nome as curso_nome,
                    t.nome as turma_nome
                FROM certificados cert
                JOIN matriculas m ON cert.matricula_id = m.id
                JOIN usuarios u ON m.aluno_id = u.id
                JOIN turmas t ON m.turma_id = t.id
                JOIN cursos c ON t.curso_id = c.id
                {$where}
                ORDER BY cert.data_emissao DESC
                LIMIT 100
            ", $params);
            
            jsonResponse([
                'success' => true,
                'data' => $certificados,
                'total' => count($certificados)
            ]);
            break;
            
        case 'relatorios':
            // Relatórios disponíveis
            $tipo = $_GET['tipo'] ?? '';
            
            if ($tipo === 'desempenho_cursos') {
                $relatorio = dbFetchAll("
                    SELECT 
                        c.nome as curso,
                        c.categoria,
                        c.nivel,
                        COUNT(DISTINCT t.id) as total_turmas,
                        COUNT(DISTINCT m.id) as total_matriculas,
                        AVG(m.frequencia) as media_frequencia,
                        AVG(av.nota) as media_notas,
                        COUNT(DISTINCT CASE WHEN m.status = 'concluida' THEN m.id END) as total_concluidos,
                        ROUND((COUNT(DISTINCT CASE WHEN m.status = 'concluida' THEN m.id END) / COUNT(DISTINCT m.id)) * 100, 2) as taxa_conclusao
                    FROM cursos c
                    LEFT JOIN turmas t ON c.id = t.curso_id
                    LEFT JOIN matriculas m ON t.id = m.turma_id
                    LEFT JOIN avaliacoes av ON m.id = av.matricula_id
                    WHERE c.status = 'ativo'
                    GROUP BY c.id
                    ORDER BY media_notas DESC
                ");
                
                jsonResponse([
                    'success' => true,
                    'data' => $relatorio,
                    'tipo' => 'desempenho_cursos'
                ]);
            } elseif ($tipo === 'frequencia_alunos') {
                $relatorio = dbFetchAll("
                    SELECT 
                        u.nome as aluno,
                        c.nome as curso,
                        t.nome as turma,
                        m.frequencia,
                        COUNT(DISTINCT av.id) as total_avaliacoes,
                        AVG(av.nota) as media_notas,
                        m.status as status_matricula
                    FROM usuarios u
                    JOIN matriculas m ON u.id = m.aluno_id
                    JOIN turmas t ON m.turma_id = t.id
                    JOIN cursos c ON t.curso_id = c.id
                    LEFT JOIN avaliacoes av ON m.id = av.matricula_id
                    WHERE u.tipo = 'aluno' AND m.status = 'ativa'
                    GROUP BY m.id
                    ORDER BY m.frequencia DESC
                ");
                
                jsonResponse([
                    'success' => true,
                    'data' => $relatorio,
                    'tipo' => 'frequencia_alunos'
                ]);
            } else {
                // Lista de tipos de relatório disponíveis
                jsonResponse([
                    'success' => true,
                    'data' => [
                        'tipos_disponiveis' => [
                            'desempenho_cursos' => 'Desempenho dos Cursos',
                            'frequencia_alunos' => 'Frequência dos Alunos',
                            'professores_ranking' => 'Ranking de Professores',
                            'cursos_populares' => 'Cursos Mais Populares',
                            'evolucao_matriculas' => 'Evolução das Matrículas'
                        ]
                    ]
                ]);
            }
            break;
            
        default:
            jsonResponse(['error' => 'Ação não reconhecida'], 400);
    }
    
} catch (Exception $e) {
    error_log("Erro na API do dashboard: " . $e->getMessage());
    jsonResponse(['error' => 'Erro interno do servidor'], 500);
}
?>

