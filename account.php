<?php
require_once 'config/db.php';

// Zabezpieczenie - tylko dla zalogowanych
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

$uid = $_SESSION['user_id'];

// --- LOGIKA POWITANIA ---
// Sprawdzamy czy w sesji jest zapisane imię.
// Jeśli tak -> używamy imienia.
// Jeśli nie (np. stare konto bez imienia) -> używamy emaila.
$welcome_name = !empty($_SESSION['first_name']) ? $_SESSION['first_name'] : $_SESSION['email'];

// Pobieramy zamówienia tego konkretnego użytkownika
$orders = $pdo->query("SELECT * FROM orders WHERE user_id = $uid ORDER BY order_date DESC")->fetchAll();

require_once 'views/header.php';
?>

<div class="container content-padding">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid var(--primary); padding-bottom: 15px;">
        <h2 style="margin: 0; color: var(--text-main);">Moje Konto</h2>
        
        <span style="color: var(--text-muted); font-size: 16px;">
            Witaj, <strong style="color: var(--primary);"><?= htmlspecialchars($welcome_name) ?></strong>!
        </span>
    </div>

    <h3 style="color: var(--text-main); margin-bottom: 20px;">Twoja historia zamówień</h3>
    
    <?php if(count($orders) == 0): ?>
        <div class="alert alert-info">
            Nie masz jeszcze żadnych zamówień. <a href="offer.php" style="color: #fff; font-weight: bold; text-decoration: underline;">Przejdź do oferty</a>.
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Kwota</th>
                    <th>Status</th>
                    <th>Numer Zamówienia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td><?= $o['order_date'] ?></td>
                    <td><strong><?= $o['total_price'] ?> PLN</strong></td>
                    <td>
                        <span class="status-badge status-<?= $o['status'] ?>">
                            <?= strtoupper($o['status']) ?>
                        </span>
                    </td>
                    <td style="color: var(--text-muted);">#<?= $o['id'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'views/footer.php'; ?>