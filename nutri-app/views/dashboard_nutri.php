<?php
session_start();
require_once '../config/db.php';

// SEGURANÇA
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// 1. BUSCAR AGENDAMENTOS (Para a tabela principal)
$sql = "SELECT a.*, u.name as patient_name, u.email as patient_email 
        FROM appointments a 
        JOIN users u ON a.patient_id = u.id 
        ORDER BY a.appointment_date ASC";
$stmt = $pdo->query($sql);
$agendamentos = $stmt->fetchAll();

// 2. BUSCAR DIAS BLOQUEADOS (Para a lista lateral)
$sqlBlock = "SELECT * FROM availability ORDER BY unavailable_date ASC";
$stmtBlock = $pdo->query($sqlBlock);
$bloqueios = $stmtBlock->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Nutri - Gestão Completa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg bg-isa-main py-3 shadow-sm">
        <div class="container">
            <a class="navbar-brand text-white fw-bold fst-italic" href="#">
                <i class="bi bi-flower1"></i> Isa Melo Nutri
            </a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Olá, <?= htmlspecialchars($_SESSION['name']) ?></span>
                <a href="logout.php" class="btn btn-sm btn-outline-light border-0">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'block_success'): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-calendar-x"></i> Data bloqueada com sucesso! Ninguém poderá agendar neste dia.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['msg'] == 'unblock_success'): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> Data liberada! Agenda aberta novamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="row g-4">
            
            <div class="col-lg-8">
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card card-dashboard h-100 border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center">
                                <div class="bg-success p-3 rounded-circle text-white me-3 opacity-75">
                                    <i class="bi bi-calendar-check fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="card-title text-muted mb-0">Total Agendados</h5>
                                    <h2 class="fw-bold text-dark mb-0"><?= count($agendamentos) ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-dashboard border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-success">Próximas Consultas</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Paciente</th>
                                        <th>Data/Hora</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($agendamentos as $agenda): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold"><?= $agenda['patient_name'] ?></div>
                                                <small class="text-muted"><?= $agenda['patient_email'] ?></small>
                                            </td>
                                            <td>
                                                <i class="bi bi-clock"></i> <?= date('d/m/Y H:i', strtotime($agenda['appointment_date'])) ?>
                                            </td>
                                            <td>
                                                <?php if ($agenda['payment_status'] == 'paid'): ?>
                                                    <span class="badge bg-success">Pago</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Aguardando Pgto</span>
                                                <?php endif; ?>
                                                
                                                <?php if ($agenda['status'] == 'cancelled'): ?>
                                                    <span class="badge bg-danger">Cancelado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end pe-3">
                                                <button class="btn btn-sm btn-light"><i class="bi bi-three-dots"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (count($agendamentos) == 0): ?>
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Agenda livre!</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-danger text-white fw-bold">
                        <i class="bi bi-slash-circle"></i> Bloquear Data
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">Selecione um dia (feriado ou folga) para impedir novos agendamentos.</p>
                        
                        <form action="../controllers/AvailabilityController.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Data</label>
                                <input type="date" name="date" class="form-control" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Motivo</label>
                                <input type="text" name="reason" class="form-control" placeholder="Ex: Feriado, Folga...">
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-danger">Bloquear Agenda</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold text-secondary">
                        Dias Indisponíveis
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($bloqueios as $block): ?>
                            <?php $dateBlock = date('d/m/Y', strtotime($block['unavailable_date'])); ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-danger"><?= $dateBlock ?></span>
                                    <div class="small text-muted"><?= $block['reason'] ?></div>
                                </div>
                                <a href="../controllers/AvailabilityController.php?action=delete&id=<?= $block['id'] ?>" 
                                   class="btn btn-sm btn-light text-danger"
                                   onclick="return confirm('Liberar esta data novamente?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>

                        <?php if (count($bloqueios) == 0): ?>
                            <li class="list-group-item text-center text-muted py-3">
                                <small>Nenhum bloqueio ativo.</small>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>