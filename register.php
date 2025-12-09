<?php
require_once 'config/db.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Pobieramy dane
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];            // Hasło
    $pass_confirm = $_POST['confirm_password']; // Powtórzone hasło

    // 2. WALIDACJA: Sprawdzamy czy hasła są identyczne
    if ($pass !== $pass_confirm) {
        $msg = "<div class='alert alert-error'>Błąd: Podane hasła nie są identyczne!</div>";
    } else {
        // Jeśli hasła się zgadzają, próbujemy zarejestrować
        $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        try {
            // Zapytanie SQL
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'client')");
            $stmt->execute([$first_name, $last_name, $email, $pass_hash]);
            
            // Automatyczne logowanie po rejestracji
            $uid = $pdo->lastInsertId();
            $_SESSION['user_id'] = $uid;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'client';
            $_SESSION['first_name'] = $first_name;
            
            // Przekierowanie
            header("Location: index.php");
            exit;
            
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-error'>Błąd: Ten adres e-mail jest już zajęty.</div>";
        }
    }
}

require_once 'views/header.php';
?>

<div class="container">
    <form method="post" class="form-box">
        <h2 style="color: var(--primary);">Załóż konto</h2>
        
        <?= $msg ?>
        
        <div class="form-row-split">
            <div>
                <label for="fname">Imię <span class="required">*</span></label>
                <input type="text" id="fname" name="first_name" required placeholder="np. Jan" value="<?= isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '' ?>">
            </div>
            <div>
                <label for="lname">Nazwisko <span class="required">*</span></label>
                <input type="text" id="lname" name="last_name" required placeholder="np. Kowalski" value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>">
            </div>
        </div>

        <label for="reg-email">Adres e-mail <span class="required">*</span></label>
        <input type="email" id="reg-email" name="email" required placeholder="jan@przyklad.pl" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        
        <label for="reg-pass">Hasło <span class="required">*</span></label>
        <input type="password" id="reg-pass" name="password" required minlength="5">

        <label for="reg-pass-confirm">Powtórz hasło <span class="required">*</span></label>
        <input type="password" id="reg-pass-confirm" name="confirm_password" required minlength="5">
        
        <div class="reg-disclaimer">
            Rejestrując się, akceptujesz <a href="terms.php" class="link-green">Regulamin</a> oraz <a href="privacy.php" class="link-green">Politykę Prywatności</a>.
        </div>

        <button type="submit" class="btn">Zarejestruj się</button>
        
        <p class="form-footer-text">
            Masz już konto? <a href="login.php" class="link-green">Zaloguj się</a>
        </p>
    </form>
</div>

<?php require_once 'views/footer.php'; ?>