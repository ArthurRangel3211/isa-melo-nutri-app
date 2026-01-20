<?php
session_start();
require_once '../config/db.php';

// SEGURANÇA: Apenas Admin pode acessar
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../views/login.php");
    exit;
}

// =======================================================
// AÇÃO 1: BLOQUEAR DATA (POST)
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $reason = htmlspecialchars($_POST['reason']);
    
    try {
        // Verifica se já existe para não duplicar
        $check = $pdo->prepare("SELECT id FROM availability WHERE unavailable_date = ?");
        $check->execute([$date]);
        
        if ($check->rowCount() > 0) {
            // Atualiza motivo
            $stmt = $pdo->prepare("UPDATE availability SET reason = ? WHERE unavailable_date = ?");
            $stmt->execute([$reason, $date]);
        } else {
            // Cria novo bloqueio
            $stmt = $pdo->prepare("INSERT INTO availability (unavailable_date, unavailable_time, reason) VALUES (?, NULL, ?)");
            $stmt->execute([$date, $reason]);
        }
        header("Location: ../views/dashboard_nutri.php?msg=block_success");

    } catch (Exception $e) {
        die("Erro ao bloquear: " . $e->getMessage());
    }
}

// =======================================================
// AÇÃO 2: DESBLOQUEAR DATA (GET - Excluir)
// =======================================================
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM availability WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: ../views/dashboard_nutri.php?msg=unblock_success");
    } catch (Exception $e) {
        die("Erro ao desbloquear: " . $e->getMessage());
    }
}
?>