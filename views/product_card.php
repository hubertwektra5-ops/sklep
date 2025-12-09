<div class="product-card">
    <div class="card-img-wrapper">
        <?php 
            $imgName = !empty($product['image']) ? $product['image'] : 'default.png';
            $prefix = (strpos($_SERVER['REQUEST_URI'], 'admin') !== false) ? '../' : '';
            $imgSrc = $prefix . 'assets/img/' . $imgName;
        ?>
        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" onerror="this.style.display='none'">
    </div>
    
    <div class="card-body">
        <span class="card-category"><?= htmlspecialchars($product['category_name'] ?? 'Analiza') ?></span>
        <h3 class="card-title"><?= htmlspecialchars($product['product_name'] ?? $product['name']) ?></h3>
        <div class="card-price"><?= number_format($product['price'], 2) ?> PLN</div>
        
        <div class="card-actions">
            <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-secondary">Szczegóły</a>
            <a href="cart.php?action=add&id=<?= $product['id'] ?>" class="btn">Do koszyka</a>
        </div>
    </div>
</div>