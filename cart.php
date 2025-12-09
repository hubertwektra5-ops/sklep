<?php
require_once 'config/db.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] == 'add') {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    } elseif ($_GET['action'] == 'remove') {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php");
    exit;
}

$products_in_cart = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products_in_cart = $stmt->fetchAll();
}

require_once 'views/header.php';
?>
<div class="container">
    <h2>Twój Koszyk</h2>
    
    <?php if(empty($products_in_cart)): ?>
        <div class="alert alert-error">Koszyk jest pusty.</div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr><th>Produkt</th><th>Ilość</th><th>Cena</th><th>Suma</th><th>Akcja</th></tr>
            </thead>
            <tbody>
            <?php foreach($products_in_cart as $p): 
                $qty = $_SESSION['cart'][$p['id']];
                $subtotal = $p['price'] * $qty;
                $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= $qty ?></td>
                <td><?= $p['price'] ?> zł</td>
                <td><?= number_format($subtotal, 2) ?> zł</td>
                <td>
                    <a href="cart.php?action=remove&id=<?= $p['id'] ?>" class="btn-danger">Usuń</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="cart-summary">
            <h3>Razem: <?= number_format($total, 2) ?> PLN</h3>
            <div class="cart-actions">
                <a href="checkout.php" class="btn">Przejdź do kasy</a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php require_once 'views/footer.php'; ?>