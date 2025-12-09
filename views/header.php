<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Sprawdzamy czy użytkownik jest w panelu admina (dla ścieżek)
$is_admin_path = (strpos($_SERVER['REQUEST_URI'], 'admin') !== false);
$path_prefix = $is_admin_path ? '../' : '';

// Pobieramy rolę użytkownika (jeśli zalogowany)
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WinZone - Analizy</title>
    
    <link rel="stylesheet" href="<?= $path_prefix ?>assets/css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="icon" type="image/png" href="<?= $path_prefix ?>assets/img/favicon.png">
</head>
<body>

<header class="main-header">
    <div class="container header-flex">
        
        <div class="brand-logo">
            <a href="<?= $path_prefix ?>index.php" style="display: flex; align-items: center; gap: 15px; text-decoration: none;">
                <img src="<?= $path_prefix ?>assets/img/favicon.png" alt="WinZone Logo" style="height: 50px; width: auto;">
                
                <h1 style="font-size: 24px; color: #fff; margin: 0; letter-spacing: 1px; font-weight: 700;">WinZone</h1>
            </a>
        </div>

        <nav class="nav-center">
            <a href="<?= $path_prefix ?>offer.php">Oferta</a>
            <a href="<?= $path_prefix ?>about.php">O Nas</a>
            <a href="<?= $path_prefix ?>contact.php">Kontakt</a>
        </nav>

        <div class="nav-right">
            
            <?php if($user_role === 'admin'): ?>
                <a href="<?= $path_prefix ?>admin/index.php" class="admin-badge">
                    <i class="fa-solid fa-gear"></i> PANEL ADMINA
                </a>
            <?php elseif($user_role === 'employee'): ?>
                <a href="<?= $path_prefix ?>admin/index.php" class="employee-badge">
                    <i class="fa-solid fa-briefcase"></i> PRACOWNIK
                </a>
            <?php endif; ?>

            <a href="<?= $path_prefix ?>cart.php" class="icon-link" title="Koszyk">
                <i class="fa-solid fa-bag-shopping"></i>
                <span class="cart-badge"><?= $cart_count ?></span>
            </a>

            <?php if(isset($_SESSION['user_id'])): ?>
                
                <a href="<?= $path_prefix ?>account.php" class="icon-link" title="Moje Konto">
                    <i class="fa-regular fa-user"></i>
                </a>

                <a href="<?= $path_prefix ?>logout.php" class="icon-link logout-icon" title="Wyloguj się">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>

            <?php else: ?>
                <a href="<?= $path_prefix ?>login.php" class="icon-link" title="Zaloguj">
                    <i class="fa-regular fa-user"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>