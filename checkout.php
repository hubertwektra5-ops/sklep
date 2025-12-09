<?php
require_once 'config/db.php';

if (empty($_SESSION['cart'])) { header("Location: offer.php"); exit; }

// Logika wyboru trybu gościa
if (isset($_POST['guest_checkout'])) {
    $_SESSION['guest_mode'] = true;
}

// ETAP 1: WYBÓR (Jeśli nie zalogowany i nie gość)
if (!isset($_SESSION['user_id']) && !isset($_SESSION['guest_mode'])) {
    require_once 'views/header.php';
    ?>
    <div class="container">
        <div class="alert alert-info" style="margin-top:30px; text-align:center;">
            <i class="fa-solid fa-circle-info"></i> Nie jesteś zalogowany.
        </div>
        
        <div class="checkout-choice-wrapper">
            <div class="checkout-option">
                <i class="fa-solid fa-user-check checkout-icon" style="color:var(--primary)"></i>
                <h3 class="checkout-title">Mam konto</h3>
                <p class="checkout-desc">Zaloguj się, aby mieć historię zamówień.</p>
                <a href="login.php?redirect=checkout" class="btn">Zaloguj się</a>
            </div>
            
            <div class="checkout-option">
                <i class="fa-solid fa-user-slash checkout-icon" style="color:#aaa"></i>
                <h3 class="checkout-title">Kup bez rejestracji</h3>
                <p class="checkout-desc">Szybki zakup jako gość.</p>
                <form method="post">
                    <button type="submit" name="guest_checkout" class="btn btn-outline">Kup jako Gość</button>
                </form>
            </div>
        </div>
    </div>
    <?php
    require_once 'views/footer.php';
    exit;
}

// ETAP 2: FINALIZACJA
$success = false;
$user_email = isset($_SESSION['user_id']) ? $_SESSION['email'] : '';

if (isset($_POST['pay'])) {
    $email_final = isset($_SESSION['user_id']) ? $_SESSION['email'] : $_POST['email'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $total = $_POST['total'];

    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, guest_email, total_price) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $email_final, $total]);
        $order_id = $pdo->lastInsertId();

        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        foreach($_SESSION['cart'] as $pid => $qty) {
            $price = $pdo->query("SELECT price FROM products WHERE id = $pid")->fetchColumn();
            $stmt_item->execute([$order_id, $pid, $qty, $price]);
        }

        $pdo->commit();
        $_SESSION['cart'] = [];
        unset($_SESSION['guest_mode']);
        $success = true;
    } catch(Exception $e) {
        $pdo->rollBack();
        die("Błąd transakcji: " . $e->getMessage());
    }
}

// Przeliczanie sumy
$total_calc = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $rows = $pdo->query("SELECT id, price FROM products WHERE id IN ($ids)")->fetchAll();
    foreach ($rows as $r) { $total_calc += $r['price'] * $_SESSION['cart'][$r['id']]; }
}

require_once 'views/header.php';
?>

<div class="container">
    <?php if($success): ?>
        <div class="alert alert-success">
            <h2>Dziękujemy! ✅</h2>
            <p>Analizy zostały wysłane na e-mail.</p>
            <a href="index.php" class="btn" style="width:auto; display:inline-block; margin-top:10px;">Strona Główna</a>
        </div>
    <?php else: ?>
        <form method="post" class="form-box">
            <h2 style="color:var(--primary);">Finalizacja Zamówienia</h2>
            
            <?php if(!isset($_SESSION['user_id'])): ?>
                <div class="alert alert-info">Kupujesz jako <strong>Gość</strong></div>
                <label>E-mail do wysyłki</label>
                <input type="email" name="email" required placeholder="np. jan@kowalski.pl">
            <?php else: ?>
                <div class="alert alert-success">Zalogowany: <strong><?= $user_email ?></strong></div>
            <?php endif; ?>
            
            <h3 style="margin:20px 0; border-top:1px solid #333; padding-top:20px;">
                Razem: <span style="color:var(--danger);"><?= number_format($total_calc, 2) ?> PLN</span>
            </h3>
            <input type="hidden" name="total" value="<?= $total_calc ?>">
            
            <div class="checkbox-wrapper">
                <input type="checkbox" required id="reg">
                <label for="reg">Akceptuję Regulamin.</label>
            </div>
            
            <button type="submit" name="pay" class="btn">Płacę i Zamawiam</button>
        </form>
    <?php endif; ?>
</div>
<?php require_once 'views/footer.php'; ?>