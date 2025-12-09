<?php
require_once 'config/db.php';

// --- 1. POBIERANIE PARAMETRÓW URL ---
$cat_id = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
$odds_filter = isset($_GET['odds']) ? $_GET['odds'] : null;
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Sprawdzamy, czy jakiekolwiek filtry są aktywne (żeby pokazać przycisk "Wyczyść")
$filters_active = ($cat_id || $odds_filter || $min_price || $max_price || $sort !== 'newest');

// --- 2. BUDOWANIE ZAPYTANIA SQL ---
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$params = [];

// Filtr Kategorii
if ($cat_id) {
    $sql .= " AND p.category_id = ?";
    $params[] = $cat_id;
}

// Filtr Kursów
if ($odds_filter) {
    if ($odds_filter == '2') $sql .= " AND p.odds BETWEEN 1.50 AND 2.50";
    elseif ($odds_filter == '3.5') $sql .= " AND p.odds BETWEEN 2.51 AND 4.00";
    elseif ($odds_filter == '5') $sql .= " AND p.odds BETWEEN 4.01 AND 8.00";
    elseif ($odds_filter == 'tasma') $sql .= " AND p.odds > 8.00";
}

// Filtr Ceny (Zakres)
if ($min_price !== null) {
    $sql .= " AND p.price >= ?";
    $params[] = $min_price;
}
if ($max_price !== null) {
    $sql .= " AND p.price <= ?";
    $params[] = $max_price;
}

// Sortowanie (ORDER BY)
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    default: // 'newest'
        $sql .= " ORDER BY p.id DESC";
        break;
}

// Wykonanie zapytania
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Pobieranie kategorii do sidebara
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Funkcja pomocnicza do budowania URL (zachowuje inne parametry)
function buildUrl($newParams) {
    $currentParams = $_GET;
    $merged = array_merge($currentParams, $newParams);
    return 'offer.php?' . http_build_query($merged);
}

require_once 'views/header.php';
?>

<div class="container shop-layout">
    
    <aside class="sidebar">
        
        <?php if ($filters_active): ?>
            <a href="offer.php" class="btn-clear-filters">
                <i class="fa-solid fa-xmark"></i> Wyczyść filtry
            </a>
        <?php endif; ?>
        
        <div class="filter-group">
            <h3>Sortuj według</h3>
            <ul>
                <li>
                    <a href="<?= buildUrl(['sort' => 'newest']) ?>" class="sidebar-link <?= ($sort == 'newest') ? 'active' : '' ?>">Najnowsze</a>
                </li>
                <li>
                    <a href="<?= buildUrl(['sort' => 'price_asc']) ?>" class="sidebar-link <?= ($sort == 'price_asc') ? 'active' : '' ?>">Cena: Rosnąco</a>
                </li>
                <li>
                    <a href="<?= buildUrl(['sort' => 'price_desc']) ?>" class="sidebar-link <?= ($sort == 'price_desc') ? 'active' : '' ?>">Cena: Malejąco</a>
                </li>
            </ul>
        </div>

        <div class="filter-group">
            <h3>Cena (PLN)</h3>
            <form action="offer.php" method="GET" class="price-filter-form">
                <?php if($cat_id): ?><input type="hidden" name="cat" value="<?= $cat_id ?>"><?php endif; ?>
                <?php if($odds_filter): ?><input type="hidden" name="odds" value="<?= $odds_filter ?>"><?php endif; ?>
                <?php if($sort): ?><input type="hidden" name="sort" value="<?= $sort ?>"><?php endif; ?>

                <div class="price-inputs-row">
                    <input type="number" name="min_price" class="input-mini" placeholder="Od" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                    <span class="separator">-</span>
                    <input type="number" name="max_price" class="input-mini" placeholder="Do" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                </div>
                <button type="submit" class="btn-filter-submit">Filtruj</button>
            </form>
        </div>

        <div class="filter-group">
            <h3>Dyscyplina</h3>
            <ul>
                <li><a href="<?= buildUrl(['cat' => null]) ?>" class="sidebar-link <?= ($cat_id === null) ? 'active' : '' ?>">Wszystkie</a></li>
                <?php foreach($categories as $c): ?>
                    <li>
                        <a href="<?= buildUrl(['cat' => $c['id']]) ?>" 
                           class="sidebar-link <?= ($cat_id == $c['id']) ? 'active' : '' ?>">
                           <?= htmlspecialchars($c['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="filter-group">
            <h3>Kursy</h3>
            <ul>
                <li><a href="<?= buildUrl(['odds' => null]) ?>" class="sidebar-link <?= ($odds_filter === null) ? 'active' : '' ?>">Każdy</a></li>
                <li><a href="<?= buildUrl(['odds' => '2']) ?>" class="sidebar-link <?= ($odds_filter == '2') ? 'active' : '' ?>">Około 2.00</a></li>
                <li><a href="<?= buildUrl(['odds' => '3.5']) ?>" class="sidebar-link <?= ($odds_filter == '3.5') ? 'active' : '' ?>">Około 3.50</a></li>
                <li><a href="<?= buildUrl(['odds' => '5']) ?>" class="sidebar-link <?= ($odds_filter == '5') ? 'active' : '' ?>">Około 5.00</a></li>
                <li><a href="<?= buildUrl(['odds' => 'tasma']) ?>" class="sidebar-link <?= ($odds_filter == 'tasma') ? 'active' : '' ?>">Tasiemki (8.00+)</a></li>
            </ul>
        </div>
    </aside>

    <main>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
            <h2 style="margin:0;">Dostępne Analizy</h2>
            <span style="color: #666; font-size: 14px;">Znaleziono: <strong><?= count($products) ?></strong></span>
        </div>
        
        <?php if(empty($products)): ?>
            <div class="alert alert-info">
                Brak analiz spełniających wybrane kryteria.<br>
                <a href="offer.php" style="color:#fff; font-weight:bold; text-decoration:underline; margin-top:10px; display:inline-block;">Wyczyść filtry</a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <div class="card-img-wrapper">
                            <?php 
                                $imgName = !empty($product['image']) ? $product['image'] : 'default.png';
                                $imgSrc = 'assets/img/' . $imgName;
                            ?>
                            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.style.display='none'">
                        </div>
                        <div class="card-body">
                            <div class="card-meta">
                                <span class="meta-cat"><?= htmlspecialchars($product['category_name']) ?></span>
                                <span class="meta-odds">Kurs: <?= isset($product['odds']) ? $product['odds'] : '2.00' ?></span>
                            </div>
                            
                            <h3 class="card-title"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="card-price"><?= number_format($product['price'], 2) ?> PLN</div>
                            
                            <div class="card-actions">
                                <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-secondary">Szczegóły</a>
                                <a href="cart.php?action=add&id=<?= $product['id'] ?>" class="btn">Do koszyka</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once 'views/footer.php'; ?>