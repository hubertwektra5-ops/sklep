<?php 
require_once 'config/db.php'; 

// Logika logowania
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password_input = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password_input, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        if (isset($user['first_name'])) {
            $_SESSION['first_name'] = $user['first_name'];
        }

        $redirect = (isset($_GET['redirect']) && $_GET['redirect'] == 'checkout') ? 'checkout.php' : 'index.php';
        header("Location: " . $redirect);
        exit;
    } else { 
        $error = "Błędny login lub hasło."; 
    }
}

require_once 'views/header.php'; 
?>

<div class="container">
    <form method="post" class="form-box">
        <h2>Zaloguj się</h2>
        
        <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
        
        <label for="login-email">Adres e-mail</label>
        <input type="email" id="login-email" name="email" required>
        
        <label for="login-pass">Hasło</label>
        <input type="password" id="login-pass" name="password" required>
        
        <button type="submit" name="login" class="btn">Zaloguj</button>
        
        <div class="mt-10 text-right">
            <a href="forgot_password.php" class="lost-password-link">Nie pamiętasz hasła?</a>
        </div>

        <p class="mt-20 text-center text-muted text-small">
            Nie masz jeszcze konta? <a href="register.php" class="link-green">Zarejestruj się</a>
        </p>
    </form>
</div>

<?php require_once 'views/footer.php'; ?>