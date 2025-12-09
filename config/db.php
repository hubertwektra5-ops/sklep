<?php
// config/db.php

// Start sesji musi być na samym początku każdej podstrony
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'mysql1.small.pl';
$user = 'm2906_hubiess'; 
$pass = 'Sklep77';    
$name = 'm2906_winzone_db';  
try {
    $pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $pass);
    // Włączamy raportowanie błędów (ważne przy debugowaniu)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Domyślny tryb pobierania danych to tablica asocjacyjna
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // W razie błędu wyświetl komunikat i przerwij działanie
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}
?>