<?php
require_once 'config/db.php';

// 1. Walidacja ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Pobranie danych z bazy (używamy widoku v_products_list)
$stmt = $pdo->prepare("SELECT * FROM v_products_list WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

// 3. Jeśli produkt nie istnieje -> powrót do oferty
if (!$product) { 
    header("Location: offer.php"); 
    exit; 
}

require_once 'views/header.php';
?>

<div class="container content-padding">
    
    <div class="product-nav">
        <a href="offer.php" class="btn btn-secondary btn-back">&larr; Wróć do oferty</a>
    </div>

    <div class="product-detail-wrapper">
        
        <div class="product-image-col">
             <?php 
                $imgName = !empty($product['image']) ? $product['image'] : 'default.png';
                $imgSrc = 'assets/img/' . $imgName;
             ?>
             <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" onerror="this.style.display='none'">
        </div>

        <div class="product-info-col">
            
            <span class="product-cat">
                <?= htmlspecialchars($product['category_name']) ?>
            </span>
            
            <h1 class="product-title">
                <?= htmlspecialchars($product['product_name']) ?>
            </h1>
            
            <div class="product-meta-row">
                <span>
                    Kurs: <span class="meta-value"><?= isset($product['odds']) ? $product['odds'] : '-' ?></span>
                </span>
                <span>
                    Dostępność: 
                    <span class="<?= ($product['stock_quantity'] < 5) ? 'stock-low' : 'stock-ok' ?>">
                        <?= $product['stock_quantity'] ?> szt.
                    </span>
                </span>
            </div>
            
            <div class="big-price">
                <?= number_format($product['price'], 2) ?> PLN
            </div>
            
            <div class="product-description">
                <?= nl2br(htmlspecialchars($product['description'] ?? 'Brak szczegółowego opisu analizy.')) ?>
            </div>
            
            <?php if($product['stock_quantity'] > 0): ?>
                <a href="cart.php?action=add&id=<?= $product['id'] ?>" class="btn btn-buy-large">
                    Dodaj do koszyka <i class="fa-solid fa-cart-plus"></i>
                </a>
            <?php else: ?>
                <button class="btn btn-danger btn-disabled" disabled>
                    Produkt wyprzedany
                </button>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once 'views/footer.php'; ?>