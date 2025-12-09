<?php
require_once '../config/db.php';

// 1. ZABEZPIECZENIE DOSTĘPU
// Wpuszczamy Admina ORAZ Pracownika
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'employee'])) { 
    header("Location: ../login.php"); 
    exit; 
}

$role = $_SESSION['role']; // 'admin' lub 'employee'
$msg = '';

// --- LOGIKA: USUWANIE (TYLKO ADMIN) ---
if (isset($_GET['delete'])) {
    if ($role !== 'admin') {
        die("<div class='container'><div class='alert alert-error'>Brak uprawnień do usuwania.</div></div>");
    }
    
    try {
        $id = (int)$_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: products.php?status=deleted");
        exit;
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-error'>Błąd usuwania: " . $e->getMessage() . "</div>";
    }
}

if (isset($_GET['status']) && $_GET['status'] == 'deleted') {
    $msg = "<div class='alert alert-success'>Produkt został usunięty.</div>";
}

// --- LOGIKA: EDYCJA (TYLKO ADMIN) ---
$edit_mode = false;
$product_data = [
    'id' => '', 'name' => '', 'description' => '', 'price' => '', 
    'stock_quantity' => '', 'image' => '', 'category_id' => '', 'odds' => ''
];

if (isset($_GET['edit'])) {
    if ($role !== 'admin') {
        die("<div class='container'><div class='alert alert-error'>Brak uprawnień do edycji.</div></div>");
    }

    $edit_mode = true;
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $fetched = $stmt->fetch();
    if ($fetched) $product_data = $fetched;
}

// --- LOGIKA: ZAPISYWANIE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $qty = $_POST['stock_quantity'];
    $img = $_POST['image'];
    $cat = $_POST['category_id'];
    $odds = $_POST['odds'];

    try {
        if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
            // UPDATE (Tylko Admin)
            if ($role !== 'admin') die("Brak uprawnień.");
            
            $id = (int)$_POST['update_id'];
            $sql = "UPDATE products SET category_id=?, name=?, description=?, price=?, stock_quantity=?, image=?, odds=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$cat, $name, $desc, $price, $qty, $img, $odds, $id]);
            $msg = "<div class='alert alert-success'>Zaktualizowano produkt!</div>";
            
            $product_data = $_POST;
            $product_data['id'] = $id;
            $edit_mode = true; 

        } else {
            // INSERT (Admin i Pracownik)
            $sql = "INSERT INTO products (category_id, name, description, price, stock_quantity, image, odds) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$cat, $name, $desc, $price, $qty, $img, $odds]);
            $msg = "<div class='alert alert-success'>Dodano nową analizę!</div>";
            
            // Czyścimy formularz
            $product_data = array_fill_keys(array_keys($product_data), '');
        }
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-error'>Błąd bazy: " . $e->getMessage() . "</div>";
    }
}

// Pobieranie danych
$products_list = $pdo->query("SELECT * FROM v_products_list ORDER BY id DESC")->fetchAll();
$categories_list = $pdo->query("SELECT * FROM categories")->fetchAll();

require_once '../views/header.php';
?>

<div class="container content-padding">
    
    <div class="admin-page-header">
        <h2>Zarządzanie Ofertą</h2>
        <a href="index.php" class="btn btn-secondary" style="width:auto; font-size:12px;">&larr; Panel Główny</a>
    </div>

    <?= $msg ?>

    <form method="post" class="form-box admin-mode">
        <h3 class="form-title">
            <?= $edit_mode ? 'Edytuj Analizę' : 'Dodaj Nową Analizę' ?>
        </h3>

        <?php if($edit_mode): ?>
            <input type="hidden" name="update_id" value="<?= $product_data['id'] ?>">
        <?php endif; ?>

        <div class="form-grid-2">
            <div>
                <label>Tytuł Analizy (Mecz)</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($product_data['name']) ?>" placeholder="np. Lakers vs Bulls">
            </div>
            <div>
                <label>Kategoria</label>
                <select name="category_id" required>
                    <option value="" disabled <?= empty($product_data['category_id']) ? 'selected' : '' ?>>Wybierz...</option>
                    <?php foreach($categories_list as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($product_data['category_id'] == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-grid-3">
            <div>
                <label>Cena (PLN)</label>
                <input type="number" step="0.01" name="price" required value="<?= $product_data['price'] ?>" placeholder="0.00">
            </div>
            <div>
                <label>Kurs (Odds)</label>
                <input type="number" step="0.01" name="odds" required value="<?= $product_data['odds'] ?>" placeholder="np. 2.50">
            </div>
            <div>
                <label>Ilość</label>
                <input type="number" name="stock_quantity" required value="<?= $product_data['stock_quantity'] ?>" placeholder="100">
            </div>
        </div>

        <label>Opis</label>
        <textarea name="description" rows="4"><?= htmlspecialchars($product_data['description']) ?></textarea>

        <label>Plik graficzny (nazwa z assets/img/)</label>
        <input type="text" name="image" value="<?= htmlspecialchars($product_data['image']) ?>" placeholder="default.png">

        <div class="form-actions-row">
            <?php if($edit_mode): ?>
                <button type="submit" class="btn" style="width: auto;">Zapisz Zmiany</button>
                <a href="products.php" class="btn btn-secondary" style="width: auto;">Anuluj</a>
            <?php else: ?>
                <button type="submit" class="btn" style="width: auto;">Dodaj Produkt</button>
            <?php endif; ?>
        </div>
    </form>
    
    <h3 style="color: var(--text-main); margin-bottom: 15px;">Lista Analiz</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nazwa</th>
                <th>Kurs</th>
                <th>Cena</th>
                <th>Ilość</th>
                <?php if($role === 'admin'): ?> 
                    <th>Akcje</th> 
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach($products_list as $p): ?>
        <tr>
            <td>
                <?php $img = !empty($p['image']) ? $p['image'] : 'default.png'; ?>
                <img src="../assets/img/<?= $img ?>" class="table-thumb" alt="img" onerror="this.src='https://via.placeholder.com/40'">
            </td>
            <td>
                <span class="product-name"><?= htmlspecialchars($p['product_name']) ?></span><br>
                <span class="product-cat"><?= htmlspecialchars($p['category_name']) ?></span>
            </td>
            <td><span class="text-odds"><?= isset($p['odds']) ? $p['odds'] : '-' ?></span></td>
            <td><?= $p['price'] ?> zł</td>
            <td>
                <span class="<?= ($p['stock_quantity'] < 5) ? 'stock-low' : 'stock-ok' ?>">
                    <?= $p['stock_quantity'] ?>
                </span>
            </td>
            
            <?php if($role === 'admin'): ?>
            <td>
                <div class="action-buttons">
                    <a href="products.php?edit=<?= $p['id'] ?>" class="btn-edit">Edytuj</a>
                    <a href="products.php?delete=<?= $p['id'] ?>" class="btn-link-danger" onclick="return confirm('Usunąć?')">Usuń</a>
                </div>
            </td>
            <?php endif; ?>
            
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../views/footer.php'; ?>