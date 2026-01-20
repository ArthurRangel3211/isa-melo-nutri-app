<?php
session_start();
require_once '../config/db.php'; // Garante que a variável $pdo existe

class PaymentController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Ação 1: Enviar para o Pagamento (Checkout)
    public function checkout($appointmentId) {
        // Segurança: Verifica se o usuário está logado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/login.php');
            exit;
        }


        $stmt = $this->pdo->prepare("SELECT * FROM appointments WHERE id = ? AND patient_id = ?");
        $stmt->execute([$appointmentId, $_SESSION['user_id']]);
        $appointment = $stmt->fetch();

        if (!$appointment) {
            die("Erro: Agendamento não encontrado ou você não tem permissão.");
        }

        // --- LÓGICA DE INTEGRAÇÃO ---
        
        // Vamos redirecionar para o próprio controller confirmar o pagamento
        $linkSimulado = "PaymentController.php?action=confirm&id=" . $appointmentId;
        
        header("Location: $linkSimulado");
        exit;
    }

    // Ação 2: Confirmar o Pagamento (Callback)
    public function confirmPayment($appointmentId) {
        try {
            // Atualiza o banco de dados
            // Muda status para 'confirmed' e payment_status para 'paid'
            $sql = "UPDATE appointments 
                    SET payment_status = 'paid', status = 'confirmed' 
                    WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute([$appointmentId])) {
                // Redireciona de volta para a dashboard do paciente com mensagem de sucesso
                header("Location: ../views/dashboard_patient.php?msg=pagamento_sucesso");
                exit;
            } else {
                die("Erro ao atualizar o pagamento no banco de dados.");
            }

        } catch (Exception $e) {
            die("Erro crítico: " . $e->getMessage());
        }
    }
}

// --- ROTEADOR SIMPLES ---

if (isset($_GET['action']) && isset($_GET['id'])) {
    $controller = new PaymentController($pdo); // $pdo vem do db.php
    
    $action = $_GET['action'];
    $id = (int)$_GET['id']; // Cast para int por segurança

    if ($action === 'checkout') {
        $controller->checkout($id);
    } elseif ($action === 'confirm') {
        $controller->confirmPayment($id);
    }
} else {
    // Se acessar o arquivo direto sem parâmetros
    header("Location: ../index.php");
    exit;
}
?>