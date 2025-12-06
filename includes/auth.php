<?php
require_once 'config.php';

// Processar login
if (isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    $user = getUserByEmail($email);
    
    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_type'] = $user['tipo'];
        
        header('Location: ../index.php');
        exit();
    } else {
        $login_error = "Email ou senha incorretos!";
    }
}

// Processar registro
if (isset($_POST['register'])) {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $register_error = "Todos os campos são obrigatórios!";
    } elseif ($senha !== $confirmar_senha) {
        $register_error = "As senhas não coincidem!";
    } elseif (strlen($senha) < 6) {
        $register_error = "A senha deve ter pelo menos 6 caracteres!";
    } else {
        $existingUser = getUserByEmail($email);
        if ($existingUser) {
            $register_error = "Este email já está cadastrado!";
        } else {
            if (registerUser($nome, $email, $senha)) {
                $user = getUserByEmail($email);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nome'] = $user['nome'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['tipo'];
                
                header('Location: ../index.php');
                exit();
            } else {
                $register_error = "Erro ao criar conta. Tente novamente!";
            }
        }
    }
}
?>