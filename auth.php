<?php
require_once 'config/db.php';

// Logika przekierowania
$redirect_to = (isset($_GET['redirect']) && $_GET['redirect'] == 'checkout') ? 'checkout.php' : 'index.php';
$msg = '';

// --- REJESTRACJA ---
if (isset($_POST['register'])) {
    $first_name = trim($_POST['first_name']); // Nowe pole
    $last_name = trim($_POST['last_name']);   // Nowe pole
    $email = trim($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        // Zaktualizowane zapytanie SQL o imię i nazwisko
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'client')");
        $stmt->execute([$first_name, $last_name, $email, $pass]);
        
        // Auto-logowanie
        $uid = $pdo->lastInsertId();
        $_SESSION['user_id'] = $uid;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'client';
        // Opcjonalnie zapisz imię w sesji, żeby wyświetlać "Witaj Jan"
        $_SESSION['first_name'] = $first_name;
        
        header("Location: " . $redirect_to);
        exit;
        
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-error'>Błąd: Ten adres e-mail jest już zajęty.</div>";
    }
}

// --- LOGOWANIE ---
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name']; // Zapisujemy imię do sesji
        
        header("Location: " . $redirect_to);
        exit;
    } else {
        $msg = "<div class='alert alert-error'>Błędne dane logowania.</div>";
    }
}

require_once 'views/header.php';
?>

<div class="container">
    
    <?php if(isset($_GET['redirect']) && $_GET['redirect'] == 'checkout'): ?>
        <div class="alert" style="background: #222; color: #fff; border: 1px solid #444; display: flex; align-items: center; gap: 10px; margin-top: 20px;">
            <i class="fa-solid fa-lock" style="color: var(--primary);"></i> 
            <strong>Zaloguj się lub zarejestruj, aby dokończyć zamówienie.</strong>
        </div>
    <?php endif; ?>

    <?= $msg ?>

    <div class="auth-grid">
        
        <div class="auth-col" id="login-section">
            <h2 class="auth-title">Logowanie</h2>
            <form method="post">
                <div class="form-group">
                    <label for="login-email">Adres e-mail <span class="required">*</span></label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="login-pass">Hasło <span class="required">*</span></label>
                    <input type="password" id="login-pass" name="password" required>
                </div>
                
                <div class="form-actions">
                    <label class="form-check">
                        <input type="checkbox" name="remember">
                        <span>Zapamiętaj mnie</span>
                    </label>
                    <button type="submit" name="login" class="btn btn-full">Zaloguj się</button>
                </div>
                
                <a href="#" class="lost-password-link">Nie pamiętasz hasła?</a>
            </form>
        </div>

        <div class="auth-col">
            <h2 class="auth-title">Zarejestruj się</h2>
            <form method="post">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="reg-name">Imię <span class="required">*</span></label>
                        <input type="text" id="reg-name" name="first_name" required placeholder="np. Jan">
                    </div>
                    <div class="form-group">
                        <label for="reg-surname">Nazwisko <span class="required">*</span></label>
                        <input type="text" id="reg-surname" name="last_name" required placeholder="np. Kowalski">
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg-email">Adres e-mail <span class="required">*</span></label>
                    <input type="email" id="reg-email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="reg-pass">Hasło <span class="required">*</span></label>
                    <input type="password" id="reg-pass" name="password" required>
                </div>
                
                <div class="reg-disclaimer">
                    Twoje dane osobowe zostaną użyte do obsługi twojej wizyty na naszej stronie oraz do realizacji zamówień. Rejestrując się akceptujesz <a href="terms.php">Regulamin</a>.
                </div>

                <button type="submit" name="register" class="btn btn-full">Zarejestruj się</button>
                
                <p style="margin-top: 20px; text-align: center; font-size: 14px; color: #aaa;">
                    Masz już konto? 
                    <a href="#login-section" style="color: var(--primary); font-weight: bold; text-decoration: underline;">Zaloguj się</a>
                </p>
            </form>
        </div>
        
    </div>
</div>

<?php require_once 'views/footer.php'; ?>