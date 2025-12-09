<?php
require_once '../config/db.php';

// Zabezpieczenie: Tylko admin (Pracownik wylatuje)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    // Jeśli to pracownik, odeślij go do produktów
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'employee') {
        header("Location: products.php");
    } else {
        header("Location: ../login.php"); 
    }
    exit; 
}

// --- LOGIKA: ZMIANA STATUSU ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    // Odświeżenie, żeby zobaczyć zmianę
    header("Location: orders.php"); 
    exit;
}

// --- POBIERANIE ZAMÓWIEŃ ---
// Pobieramy zamówienia + email użytkownika (jeśli zarejestrowany) lub guest_email
$sql = "SELECT o.*, u.email as registered_email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.order_date DESC";
$orders = $pdo->query($sql)->fetchAll();

require_once '../views/header.php';
?>

<div class="container content-padding">
    
    <div class="admin-page-header">
        <h2>Zarządzanie Zamówieniami</h2>
        <a href="index.php" class="btn btn-secondary" style="width:auto; font-size:12px;">&larr; Wróć</a>
    </div>

    <?php if(empty($orders)): ?>
        <div class="alert alert-info">Brak zamówień w bazie.</div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Klient</th>
                    <th>Kwota</th>
                    <th>Zakupione Typy</th>
                    <th>Data</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($orders as $o): ?>
                <?php 
                    // Ustalenie adresu email klienta (gość czy user?)
                    $client_email = $o['guest_email'] ? $o['guest_email'] : $o['registered_email'];
                    
                    // Pobranie produktów dla TEGO zamówienia (małe zapytanie w pętli)
                    $stmt_items = $pdo->prepare("
                        SELECT p.name, oi.quantity 
                        FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = ?
                    ");
                    $stmt_items->execute([$o['id']]);
                    $items = $stmt_items->fetchAll();
                ?>
                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($client_email) ?></strong><br>
                        <span style="font-size:11px; color:#666;">
                            <?= $o['user_id'] ? 'Zarejestrowany' : 'Gość' ?>
                        </span>
                    </td>
                    <td><strong><?= $o['total_price'] ?> PLN</strong></td>
                    <td>
                        <ul class="order-items-list">
                            <?php foreach($items as $item): ?>
                                <li><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td><?= $o['order_date'] ?></td>
                    <td>
                        <form method="post" class="status-form">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <select name="status" class="status-select">
                                <option value="new" <?= $o['status']=='new'?'selected':'' ?>>Nowe</option>
                                <option value="paid" <?= $o['status']=='paid'?'selected':'' ?>>Opłacone</option>
                                <option value="completed" <?= $o['status']=='completed'?'selected':'' ?>>Zakończone</option>
                                <option value="cancelled" <?= $o['status']=='cancelled'?'selected':'' ?>>Anulowane</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-small">OK</button>
                        </form>
                        
                        <div style="margin-top: 5px;">
                            <span class="status-badge status-<?= $o['status'] ?>">
                                <?= strtoupper($o['status']) ?>
                            </span>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once '../views/footer.php'; ?>