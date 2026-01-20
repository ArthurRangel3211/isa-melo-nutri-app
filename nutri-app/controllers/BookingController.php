<?php
session_start();
require_once '../config/db.php';

// Verificação de Segurança Global
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit;
}

// ==================================================================
// AÇÃO 1: CANCELAR AGENDAMENTO (Via Link/GET)
// ==================================================================
if (isset($_GET['action']) && $_GET['action'] == 'cancel') {
    $appointment_id = $_GET['id'];
    $patient_id = $_SESSION['user_id'];

    try {
        // Atualiza para 'cancelled' SOMENTE se o agendamento pertencer ao usuário logado
        // Isso impede que um usuário cancele a consulta de outro (Segurança!)
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND patient_id = ?");
        $stmt->execute([$appointment_id, $patient_id]);

        if ($stmt->rowCount() > 0) {
            header("Location: ../views/dashboard_patient.php?msg=cancel_success");
        } else {
            // Se não cancelou, ou o ID não existe ou não pertence a esse paciente
            header("Location: ../views/dashboard_patient.php?msg=error");
        }
    } catch (Exception $e) {
        die("Erro ao cancelar: " . $e->getMessage());
    }
    exit;
}

// ==================================================================
// AÇÃO 2: NOVO AGENDAMENTO (Via Formulário/POST)
// ==================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $patient_id = $_SESSION['user_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $datetime = "$date $time";
    
    // 1. Valida disponibilidade novamente (Segurança extra)
    $stmt = $pdo->prepare("SELECT id FROM appointments WHERE appointment_date = ? AND status != 'cancelled'");
    $stmt->execute([$datetime]);
    
    if ($stmt->rowCount() > 0) {
        die("Erro: Desculpe, alguém reservou este horário segundos antes de você.");
    }

    // 2. Insere o agendamento
    try {
        $sql = "INSERT INTO appointments (patient_id, appointment_date, status, payment_status) VALUES (?, ?, 'pending', 'unpaid')";
        $insert = $pdo->prepare($sql);
        
        if ($insert->execute([$patient_id, $datetime])) {
            header("Location: ../views/dashboard_patient.php?msg=new_success");
        } else {
            echo "Erro ao salvar no banco.";
        }
    } catch (Exception $e) {
        echo "Erro crítico: " . $e->getMessage();
    }
}
?>