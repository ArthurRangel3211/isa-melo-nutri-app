<?php
session_start();
require_once '../config/db.php';

// VERIFICAÇÃO DE SEGURANÇA
// Se o usuário não estiver logado, manda pro login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// BUSCAR AGENDAMENTOS DO PACIENTE LOGADO
// Ordenado pela data mais recente primeiro
$stmt = $pdo->prepare("
    SELECT * FROM appointments 
    WHERE patient_id = ? 
    ORDER BY appointment_date DESC
");
$stmt->execute([$user_id]);
$agendamentos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Agendamentos - Isa Melo Nutri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-isa-main py-3">
        <div class="container">
            <a class="navbar-brand text-white fw-bold fst-italic" href="#">
                <i class="bi bi-flower1"></i> Isa Melo Nutri
            </a>
            
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Olá, <?= htmlspecialchars($_SESSION['name']) ?></span>
                
                <a href="logout.php" class="btn btn-sm btn-outline-light text-white-50 border-0">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'pagamento_sucesso'): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill"></i> Pagamento confirmado com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['msg'] == 'cancel_success'): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="bi bi-info-circle-fill"></i> Sua consulta foi cancelada.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['msg'] == 'new_success'): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-calendar-check-fill"></i> Consulta agendada! Realize o pagamento para confirmar.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color: var(--isa-green-dark)">Meus Agendamentos</h2>
            <a href="agendamento.php" class="btn btn-isa">
                <i class="bi bi-plus-circle"></i> Nova Consulta
            </a>
        </div>

        <div class="card card-dashboard">
            <div class="card-body p-0">
                
                <div style="min-height: 400px;"> 
                    
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Data e Hora</th>
                                <th>Status</th>
                                <th>Pagamento</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agendamentos as $agenda): ?>
                                <?php 
                                    // Formata data (d/m/Y H:i)
                                    $dataFormatada = date('d/m/Y \à\s H:i', strtotime($agenda['appointment_date']));
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-secondary">
                                        <i class="bi bi-calendar-event me-2"></i> 
                                        <?= $dataFormatada ?>
                                    </td>
                                    
                                    <td>
                                        <?php if ($agenda['status'] == 'confirmed'): ?>
                                            <span class="badge bg-success rounded-pill">Confirmado</span>
                                        <?php elseif ($agenda['status'] == 'cancelled'): ?>
                                            <span class="badge bg-danger rounded-pill">Cancelado</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark rounded-pill">Pendente</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($agenda['status'] == 'cancelled'): ?>
                                            <span class="text-muted text-decoration-line-through small">Cancelado</span>
                                        <?php elseif ($agenda['payment_status'] == 'paid'): ?>
                                            <div class="text-success fw-bold">
                                                <i class="bi bi-check-circle-fill"></i> Pago
                                            </div>
                                        <?php else: ?>
                                            <a href="../controllers/PaymentController.php?action=checkout&id=<?= $agenda['id'] ?>" 
                                               class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-credit-card"></i> Pagar R$ 150,00
                                            </a>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-light text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow border-0">
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> Ver Detalhes</a></li>
                                                
                                                <li><hr class="dropdown-divider"></li>
                                                
                                                <?php if ($agenda['status'] != 'cancelled'): ?>
                                                    <li>
                                                        <a class="dropdown-item text-danger fw-bold" 
                                                           href="../controllers/BookingController.php?action=cancel&id=<?= $agenda['id'] ?>"
                                                           onclick="return confirm('Tem certeza que deseja cancelar esta consulta?');">
                                                            <i class="bi bi-x-circle"></i> Cancelar
                                                        </a>
                                                    </li>
                                                <?php else: ?>
                                                    <li><span class="dropdown-item text-muted disabled">Cancelado</span></li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (count($agendamentos) == 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        Você ainda não tem consultas agendadas.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>