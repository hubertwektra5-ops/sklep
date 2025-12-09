<?php
require_once 'config/db.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    // Sprawdzamy czy email istnieje
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // 1. Generujemy kod
        $code = rand(10000, 99999);
        $expires = date("Y-m-d H:i:s", time() + 900); // 15 minut
        
        // 2. Zapisujemy w bazie
        $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $update->execute([$code, $expires, $email]);

        // 3. Ustawiamy sesję (ważne!)
        $_SESSION['reset_email'] = $email;

        // 4. Próba wysyłki maila
        $to = $email;
        $subject = "Kod resetujący - WinZone";
        $message = "Twój kod: $code";
        $headers = "From: no-reply@winzone.pl\r\nContent-Type: text/plain; charset=UTF-8";

        if(@mail($to, $subject, $message, $headers)) {
            // Jeśli mail poszedł -> przekieruj od razu
            header("Location: reset_password.php");
            exit;
        } else {
            // --- TUTAJ BYŁ BŁĄD: BRAKOWAŁO PRZYCISKU ---
            // Jeśli mail nie poszedł (localhost), pokazujemy kod I PRZYCISK
            $msg = "<div class='alert alert-info'>
                        <strong>Tryb Awaryjny (Brak serwera poczty)</strong><br><br>
                        Twój kod resetujący to: <strong style='font-size: 24px; color: #fff;'>$code</strong>
                        <br><br>
                        Skopiuj go i kliknij przycisk poniżej:
                        <br><br>
                        <a href='reset_password.php' class='btn'>WPISZ KOD I ZMIEŃ HASŁO &rarr;</a>
                    </div>";
        }

    } else {
        $msg = "<div class='alert alert-error'>Nie znaleziono takiego adresu e-mail.</div>";
    }
}

require_once 'views/header.php';
?>

<div class="container">
    <form method="post" class="form-box">
        <h2>Odzyskiwanie hasła</h2>
        
        <?= $msg ?>
        
        <?php if(empty($msg) || strpos($msg, 'alert-error') !== false): ?>
            <p class="text-muted text-small mb-20">
                Wpisz swój e-mail. Wyślemy na niego 5-cyfrowy kod weryfikacyjny.
            </p>
            
            <label for="reset-email">Twój adres e-mail</label>
            <input type="email" id="reset-email" name="email" required placeholder="np. jan@kowalski.pl">
            
            <button type="submit" class="btn">Wyślij kod</button>

            <div class="mt-20 text-center">
                <a href="login.php" class="link-green">&larr; Wróć do logowania</a>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php require_once 'views/footer.php'; ?>