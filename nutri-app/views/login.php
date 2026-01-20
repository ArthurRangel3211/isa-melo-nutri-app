<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso - Isa Melo Nutri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        body { background-color: var(--isa-green-dark); display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card-login { max-width: 400px; width: 100%; border-radius: 15px; overflow: hidden; }
    </style>
</head>
<body>

    <div class="card card-login shadow-lg">
        <div class="card-header bg-white text-center py-4 border-0">
            <h3 class="fw-bold" style="color: var(--isa-green-dark)">Isa Melo Nutri</h3>
            <p class="text-muted small mb-0">Área do Cliente</p>
        </div>
        <div class="card-body bg-light p-4">
            
            <ul class="nav nav-pills nav-fill mb-4" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active bg-success-subtle text-success" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button">Entrar</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link text-muted" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button">Criar Conta</button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="pills-login">
                    <form action="../controllers/AuthController.php" method="POST">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-isa w-100 py-2">Acessar Sistema</button>
                    </form>
                </div>

                <div class="tab-pane fade" id="pills-register">
                    <form action="../controllers/AuthController.php" method="POST">
                        <input type="hidden" name="action" value="register">
                        <div class="mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-outline-success w-100 py-2">Cadastrar e Agendar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>