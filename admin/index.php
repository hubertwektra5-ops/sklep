<?php
require_once '../config/db.php';

// Zabezpieczenie: Admin LUB Pracownik
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'employee'])) { 
    header("Location: ../login.php"); 
    exit; 
}

$role = $_SESSION['role'];

require_once '../views/header.php';
?>

<div class="container content-padding">
    
    <div class="admin-page-header">
        <h2 style="margin: 0;">Pulpit Zarządzania</h2>
        <span style="color: var(--text-muted);">
            Zalogowany jako: <strong><?= ucfirst($role) ?></strong> (<?= $_SESSION['email'] ?>)
        </span>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
        
        <div class="form-box" style="margin: 0; text-align: center;">
            <i class="fa-solid fa-list" style="font-size: 40px; color: var(--primary); margin-bottom: 20px;"></i>
            <h3>Produkty i Analizy</h3>
            <p style="color: #aaa; margin-bottom: 20px;">
                <?= ($role === 'admin') ? 'Dodawaj, edytuj i usuwaj analizy.' : 'Dodawaj nowe analizy do oferty.' ?>
            </p>
            <a href="products.php" class="btn">Zarządzaj Ofertą</a>
        </div>

        <?php if($role === 'admin'): ?>
        <div class="form-box" style="margin: 0; text-align: center;">
            <i class="fa-solid fa-cart-shopping" style="font-size: 40px; color: var(--primary); margin-bottom: 20px;"></i>
            <h3>Zamówienia</h3>
            <p style="color: #aaa; margin-bottom: 20px;">Przeglądaj historię zakupów i zmieniaj statusy.</p>
            <a href="orders.php" class="btn">Zarządzaj Zamówieniami</a>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once '../views/footer.php'; ?>