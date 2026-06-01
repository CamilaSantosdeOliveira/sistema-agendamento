<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar Certificado - EduConnect</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 30px;
            text-align: center;
        }
        .card-body {
            padding: 40px;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 15px;
            font-size: 16px;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
        }
        .result-card {
            margin-top: 30px;
            border-radius: 15px;
            overflow: hidden;
        }
        .valid-certificate {
            border-left: 5px solid #10b981;
        }
        .invalid-certificate {
            border-left: 5px solid #ef4444;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1><i class="fas fa-shield-alt"></i> Validar Certificado</h1>
                <p class="mb-0">Sistema de Validação EduConnect</p>
            </div>
            <div class="card-body">
                <form id="validationForm">
                    <div class="mb-4">
                        <label for="codigo" class="form-label">
                            <i class="fas fa-key"></i> Código de Validação
                        </label>
                        <input type="text" class="form-control" id="codigo" name="codigo" 
                               placeholder="Digite o código do certificado (ex: CERT-B34BD621-2025)" required>
                        <div class="form-text">
                            Digite o código de validação que está no certificado
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Validar Certificado
                        </button>
                    </div>
                </form>
                
                <div id="result" style="display: none;"></div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('validationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const codigo = document.getElementById('codigo').value.trim();
            const resultDiv = document.getElementById('result');
            
            if (!codigo) {
                alert('Por favor, digite o código de validação');
                return;
            }
            
            // Mostrar loading
            resultDiv.innerHTML = `
                <div class="text-center mt-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Validando...</span>
                    </div>
                    <p class="mt-2">Validando certificado...</p>
                </div>
            `;
            resultDiv.style.display = 'block';
            
            try {
                // Simular validação (em produção, isso seria uma chamada para a API)
                const response = await fetch(`validar_certificado_api.php?codigo=${encodeURIComponent(codigo)}`);
                const data = await response.json();
                
                if (data.success) {
                    // Certificado válido
                    resultDiv.innerHTML = `
                        <div class="card result-card valid-certificate">
                            <div class="card-header bg-success text-white">
                                <h4><i class="fas fa-check-circle"></i> Certificado Válido</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-user"></i> Aluno</h5>
                                        <p class="fw-bold">${data.certificado.aluno_nome}</p>
                                        
                                        <h5><i class="fas fa-graduation-cap"></i> Curso</h5>
                                        <p class="fw-bold">${data.certificado.curso_nome}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-clock"></i> Carga Horária</h5>
                                        <p class="fw-bold">${data.certificado.carga_horaria} horas</p>
                                        
                                        <h5><i class="fas fa-calendar"></i> Data de Conclusão</h5>
                                        <p class="fw-bold">${data.certificado.data_conclusao}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <h5><i class="fas fa-shield-alt"></i> Código de Validação</h5>
                                    <code class="fs-5 bg-light p-2 rounded">${data.certificado.codigo_verificacao}</code>
                                    <p class="text-muted mt-2">Status: <span class="badge bg-success">${data.certificado.status}</span></p>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // Certificado inválido
                    resultDiv.innerHTML = `
                        <div class="card result-card invalid-certificate">
                            <div class="card-header bg-danger text-white">
                                <h4><i class="fas fa-times-circle"></i> Certificado Inválido</h4>
                            </div>
                            <div class="card-body text-center">
                                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Código não encontrado</h5>
                                <p class="text-muted">O código de validação informado não foi encontrado em nossa base de dados.</p>
                                <p><strong>Código informado:</strong> <code>${codigo}</code></p>
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                // Erro na validação
                resultDiv.innerHTML = `
                    <div class="card result-card invalid-certificate">
                        <div class="card-header bg-warning text-dark">
                            <h4><i class="fas fa-exclamation-triangle"></i> Erro na Validação</h4>
                        </div>
                        <div class="card-body text-center">
                            <p>Ocorreu um erro ao validar o certificado. Tente novamente.</p>
                            <button class="btn btn-primary" onclick="location.reload()">
                                <i class="fas fa-redo"></i> Tentar Novamente
                            </button>
                        </div>
                    </div>
                `;
            }
        });
    </script>
</body>
</html>
















