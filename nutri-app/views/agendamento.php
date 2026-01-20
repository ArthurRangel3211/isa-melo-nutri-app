<?php
session_start();
require_once '../config/db.php';

// Se não estiver logado, manda pro login
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// ====================================================================
// 1. CONFIGURAÇÃO DOS HORÁRIOS (AQUI VOCÊ DEFINE A REGRA)
// ====================================================================
$horarioInicio = '08:00';
$horarioFim    = '19:00'; // Último horário possível para começar
$tempoConsulta = '75 minutes'; // 1 hora e 15 minutos

// Função para gerar os slots de tempo
function gerarHorarios($inicio, $fim, $intervalo) {
    $horarios = [];
    $atual = strtotime($inicio);
    $final = strtotime($fim);

    while ($atual <= $final) {
        $horarios[] = date('H:i:s', $atual);
        $atual = strtotime("+$intervalo", $atual);
    }
    return $horarios;
}

$listaHorarios = gerarHorarios($horarioInicio, $horarioFim, $tempoConsulta);

// ====================================================================
// 2. BUSCAR HORÁRIOS JÁ OCUPADOS NO BANCO
// ====================================================================

$ocupados = [];

// A) Buscar Agendamentos de Pacientes (status != cancelado)
$sqlApp = "SELECT appointment_date FROM appointments WHERE status != 'cancelled'";
$stmtApp = $pdo->query($sqlApp);
while ($row = $stmtApp->fetch()) {
    // Separa data e hora (Ex: 2026-10-20 14:00:00)
    $data = date('Y-m-d', strtotime($row['appointment_date']));
    $hora = date('H:i:s', strtotime($row['appointment_date']));
    
    $ocupados[$data][] = $hora;
}

// B) Buscar Bloqueios da Nutricionista
$sqlBlock = "SELECT unavailable_date, unavailable_time FROM availability";
$stmtBlock = $pdo->query($sqlBlock);
while ($row = $stmtBlock->fetch()) {
    $data = $row['unavailable_date'];
    
    if ($row['unavailable_time'] == NULL) {
        $ocupados[$data] = 'DIA_CHEIO'; 
    } else {
        $ocupados[$data][] = $row['unavailable_time'];
    }
}

$jsonOcupados = json_encode($ocupados);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendar Consulta - Isa Melo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-isa-main mb-4">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Agendamento</span>
            <a href="dashboard_patient.php" class="btn btn-sm btn-outline-light">Voltar</a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0 text-success fw-bold">Nova Consulta</h4>
                        <small class="text-muted">Duração de 1h 15min</small>
                    </div>
                    <div class="card-body p-4">
                        <form action="../controllers/BookingController.php" method="POST">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Selecione a Data</label>
                                <input type="date" name="date" id="dateInput" class="form-control" required min="<?= date('Y-m-d') ?>">
                                <div id="msgData" class="form-text text-danger fw-bold" style="display:none;">
                                    <i class="bi bi-x-circle"></i> A Dra. Isa não atenderá neste dia.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Horário Disponível</label>
                                <select name="time" id="timeSelect" class="form-select" required>
                                    <option value="" selected disabled>Escolha uma data primeiro...</option>
                                    
                                    <?php foreach ($listaHorarios as $horaSlot): ?>
                                        <option value="<?= $horaSlot ?>">
                                            <?= date('H:i', strtotime($horaSlot)) ?>
                                        </option>
                                    <?php endforeach; ?>

                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" id="btnSubmit" class="btn btn-isa btn-lg">Confirmar Agendamento</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const agendaOcupada = <?= $jsonOcupados ?>;

        const dateInput = document.getElementById('dateInput');
        const timeSelect = document.getElementById('timeSelect');
        const msgData = document.getElementById('msgData');
        const btnSubmit = document.getElementById('btnSubmit');

        dateInput.addEventListener('change', function() {
            const dataSelecionada = this.value;
            const bloqueiosDoDia = agendaOcupada[dataSelecionada];

            // Reset visual
            msgData.style.display = 'none';
            btnSubmit.disabled = false;
            dateInput.classList.remove('is-invalid');
            
            // Limpa status das opções
            const options = timeSelect.options;
            for (let i = 0; i < options.length; i++) {
                options[i].disabled = false;
                options[i].text = options[i].text.replace(' (Ocupado)', '').replace(' (Indisponível)', '');
            }

            if (bloqueiosDoDia) {
                // Bloqueio Total
                if (bloqueiosDoDia === 'DIA_CHEIO') {
                    msgData.style.display = 'block';
                    dateInput.classList.add('is-invalid');
                    btnSubmit.disabled = true;
                    timeSelect.value = "";
                    for (let i = 0; i < options.length; i++) options[i].disabled = true;
                    return; 
                }

                // Bloqueio Parcial
                for (let i = 0; i < options.length; i++) {
           
                    const horaOpcao = options[i].value;
                    
                    if (bloqueiosDoDia.includes(horaOpcao)) {
                        options[i].disabled = true;
                        options[i].text += ' (Ocupado)';
                    }
                }
            }
        });
    </script>

</body>
</html>