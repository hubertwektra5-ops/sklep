<?php
session_start();

// Usuwamy wszystkie zmienne sesyjne
session_unset();

// Niszczymy sesję
session_destroy();

// Przekierowanie na stronę główną
header("Location: index.php");
exit;
?>