<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isa Melo Nutri - Saúde e Bem-estar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-isa-main py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fst-italic" href="#"><i class="bi bi-flower1"></i> Isa Melo Nutri</a>
            <div class="ms-auto">
                <a href="views/login.php" class="btn btn-outline-light me-2">Entrar</a>
                <a href="views/login.php?action=register" class="btn btn-light text-success fw-bold">Agendar Consulta</a>
            </div>
        </div>
    </nav>

    <header class="py-5" style="background: linear-gradient(rgba(62,74,61,0.9), rgba(62,74,61,0.8)), url('assets/img/background-nutri.jpg'); background-size: cover; background-position: center; color: white;">
        <div class="container text-center py-5">
            <h1 class="display-3 fw-bold mb-3">Nutrição que transforma vidas</h1>
            <p class="lead mb-4">Planos alimentares personalizados para sua rotina, sem radicalismos.</p>
            <a href="views/login.php?action=register" class="btn btn-light btn-lg px-5 py-3 fw-bold text-success rounded-pill shadow">
                Começar Minha Mudança Agora
            </a>
        </div>
    </header>

    <section class="py-5">
        <div class="container text-center">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <div class="text-success mb-3"><i class="bi bi-camera-video fs-1"></i></div>
                        <h4>100% Online</h4>
                        <p class="text-muted">Atendimento por vídeo chamada no conforto da sua casa.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <div class="text-success mb-3"><i class="bi bi-calendar-check fs-1"></i></div>
                        <h4>Agenda Flexível</h4>
                        <p class="text-muted">Horários noturnos e aos sábados para quem tem rotina corrida.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <div class="text-success mb-3"><i class="bi bi-chat-heart fs-1"></i></div>
                        <h4>Suporte Diário</h4>
                        <p class="text-muted">Tire dúvidas sobre suas refeições diretamente pelo sistema.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-isa-main text-white text-center py-4 mt-5">
        <p class="mb-0">&copy; 2026 Isa Melo Nutri. Desenvolvido por Arthur Rangel.</p>
    </footer>

</body>
</html>