<?php
session_start();
require_once '../config/db.php';

// Verifica se o formulário enviou dados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Captura a ação (login ou register) enviada pelo formulário
    $action = $_POST['action'];
    
    // Limpeza básica de segurança nos campos
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // =========================================================
    // AÇÃO 1: CADASTRAR NOVO PACIENTE (REGISTER)
    // =========================================================
    if ($action === 'register') {
        $name = htmlspecialchars($_POST['name']); 
        
        // 1. Verifica se o e-mail já existe no banco
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->rowCount() > 0) {
            // Se já existe, interrompe e avisa
            echo "<script>
                    alert('Erro: Este e-mail já está cadastrado!');
                    window.history.back();
                  </script>";
            exit;
        }

        // 2. Criptografa a senha (Segurança AppSec)
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // 3. Insere no banco (Sempre como 'patient' por segurança)
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'patient')");
            
            if ($stmt->execute([$name, $email, $hash])) {
                // Cadastro sucesso: Já loga o usuário automaticamente
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['role'] = 'patient';
                $_SESSION['name'] = $name;
                
                // Manda para a área do paciente
                header("Location: ../views/dashboard_patient.php");
                exit;
            }
        } catch (PDOException $e) {
            die("Erro no banco de dados: " . $e->getMessage());
        }
    }

    // =========================================================
    // AÇÃO 2: LOGIN (ENTRAR)
    // =========================================================
    if ($action === 'login') {
        // 1. Busca o usuário pelo e-mail
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // 2. Verifica se usuário existe E se a senha bate com o hash
        if ($user && password_verify($password, $user['password_hash'])) {
            
            // Login Sucesso: Salva dados na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirecionamento baseado no cargo (Role)
            if ($user['role'] === 'admin') {
                header("Location: ../views/dashboard_nutri.php");
            } else {
                header("Location: ../views/dashboard_patient.php");
            }
            exit;

        } else {
            // Login Falhou
            echo "<script>
                    alert('E-mail ou senha incorretos!');
                    window.location.href='../views/login.php';
                  </script>";
            exit;
        }
    }
} else {
    // Se tentar abrir o arquivo direto sem ser POST, manda voltar
    header("Location: ../views/login.php");
    exit;
}
?>