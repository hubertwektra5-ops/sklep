<?php
require_once 'config/db.php';

// Jeśli nie mamy maila w sesji (ktoś wszedł tu bezpośrednio), wracamy do początku
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['reset_email'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_reset'])) {
    $code_input = trim($_POST['code']);
    $pass = $_POST['new_password'];
    $pass_confirm = $_POST['confirm_password'];
    $current_time = date("Y-m-d H:i:s");

    // 1. Sprawdzamy czy kod pasuje i nie wygasł
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_expires > ?");
    $stmt->execute([$email, $code_input, $current_time]);
    $user = $stmt->fetch();

    if (!$user) {
        $msg = "<div class='alert alert-error'>Kod jest nieprawidłowy lub wygasł.</div>";
    } elseif ($pass !== $pass_confirm) {
        $msg = "<div class='alert alert-error'>Hasła nie są identyczne.</div>";
    } else {
        // 2. Zmieniamy hasło
        $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?");
        $update->execute([$pass_hash, $email]);

        // Czyścimy sesję resetowania
        unset($_SESSION['reset_email']);

        $msg = "<div class='alert alert-success'>
                    Hasło zmienione pomyślnie! <br>
                    <a href='login.php' class='btn' style='display:inline-block; margin-top:10px; width:auto;'>Zaloguj się</a>
                </div>";
    }
}

require_once 'views/header.php';
?>

<div class="container">
    <form method="post" class="form-box">
        <h2>Zmień hasło</h2>
        
        <div class="alert alert-info">
            Kod wysłano na: <strong><?= htmlspecialchars($email) ?></strong>
        </div>

        <?= $msg ?>

        <?php if (strpos($msg, 'alert-success') === false): ?>
            
            <label for="code">Kod weryfikacyjny (5 cyfr)</label>
            <input type="text" id="code" name="code" required placeholder="np. 12345" style="letter-spacing: 5px; font-weight: bold; text-align: center; font-size: 20px;">
            
            <label for="np">Nowe hasło</label>
            <input type="password" id="np" name="new_password" required minlength="5">

            <label for="cp">Powtórz hasło</label>
            <input type="password" id="cp" name="confirm_password" required minlength="5">
            
            <button type="submit" name="verify_reset" class="btn">Zatwierdź zmianę</button>
            
        <?php endif; ?>
    </form>
</div>

<?php require_once 'views/footer.php'; ?>