========================================================================
DOKUMENTACJA PROJEKTU: SKLEP INTERNETOWY "WINZONE"
========================================================================

AUTORZY: Hubert Sobiczewski, Kacper Wierzejski
KLASA: 5 KT
TEMAT: Sklep internetowy z analizami sportowymi

------------------------------------------------------------------------
1. OPIS PROJEKTU I TECHNOLOGIE
------------------------------------------------------------------------
Aplikacja jest w pełni funkcjonalnym sklepem internetowym umożliwiającym 
zakup cyfrowych analiz sportowych. System obsługuje trzy poziomy dostępu:
Klient, Pracownik oraz Administrator.

Wykorzystane technologie:
- Backend: PHP 8.1 (Programowanie obiektowe PDO, architektura MVC)
- Baza danych: MySQL 8.0 (Relacyjna)
- Frontend: HTML5, CSS3 (Ciemny motyw, Flexbox, Responsywność)
- Inne: JavaScript (proste interakcje), FontAwesome (ikony)

------------------------------------------------------------------------
2. INSTALACJA I KONFIGURACJA
------------------------------------------------------------------------
1. Baza Danych:
   - Utwórz nową bazę danych (winzone_db).
   - Zaimportuj dostarczony plik winzone.sql (struktura tabel, widoki, wyzwalacze).
   
2. Połączenie (Plik config/db.php):
   - Uzupełnij zmienne: $host, $user, $pass, $name danymi:
$host = 'mysql1.small.pl';
$user = 'm2906_hubiess';
$pass = 'Sklep77';    
$name = 'm2906_winzone_db';  

------------------------------------------------------------------------
3. DANE DOSTĘPOWE (KONTA TESTOWE)
------------------------------------------------------------------------
W bazie danych utworzono 3 przykładowe konta o różnych uprawnieniach:

A) ADMINISTRATOR (Pełny dostęp: Zarządzanie produktami, zamówieniami, edycja, usuwanie)
   Login: admin@winzone.pl
   Hasło: admin123

B) PRACOWNIK (Ograniczony dostęp: Tylko dodawanie nowych analiz, brak dostępu do zamówień)
   Login: pracownik@winzone.pl
   Hasło: pracownik123

C) KLIENT (Dostęp podstawowy: Kupowanie, historia zamówień)
   Login: klient@winzone.pl
   Hasło: klient123

------------------------------------------------------------------------
4. ZAAWANSOWANE ROZWIĄZANIA SQL (WYMAGANIA PROJEKTOWE)
------------------------------------------------------------------------
W projekcie zastosowano zaawansowane mechanizmy bazodanowe usprawniające działanie sklepu:

A) WIDOKI (VIEWS)
   - Nazwa: `v_products_list`
   - Gdzie użyty: Pliki `offer.php`, `product.php`, `admin/products.php`.
   - Cel: Upraszcza pobieranie danych o produktach. Zamiast pisać skomplikowane 
     zapytania JOIN w każdym pliku PHP, widok automatycznie łączy tabelę 
     `products` z tabelą `categories`, dostarczając gotową nazwę kategorii 
     oraz sformatowane dane produktu.

B) WYZWALACZE (TRIGGERS)
   - Nazwa: `update_stock_after_order`
   - Gdzie użyty: Automatycznie w bazie danych MySQL.
   - Cel: Działa w momencie złożenia zamówienia (INSERT do tabeli `order_items`). 
     Automatycznie odejmuje zakupioną ilość sztuk z magazynu w tabeli `products`. 
     Zapobiega to błędom ręcznej aktualizacji stanów magazynowych przez PHP.

C) TRANSAKCJE (TRANSACTIONS)
   - Gdzie użyte: Plik `checkout.php`.
   - Cel: Proces składania zamówienia jest objęty transakcją (`$pdo->beginTransaction()`). 
     Gwarantuje to spójność danych: zamówienie zostanie zapisane tylko wtedy, 
     gdy poprawnie zapiszą się wszystkie jego pozycje. Jeśli wystąpi błąd w połowie 
     zapisu (np. brak prądu, błąd bazy), cała operacja jest wycofywana (`rollBack`), 
     dzięki czemu nie powstają "puste" zamówienia bez produktów.

------------------------------------------------------------------------
5. STRUKTURA KATALOGÓW
------------------------------------------------------------------------
/winzone/
├── admin/          (Panel zarządzania dla Admina/Pracownika)
├── assets/         (Pliki statyczne: CSS, Obrazki)
├── config/         (Pliki konfiguracyjne: db.php)
├── views/          (Elementy widoku: nagłówek, stopka)
├── *.php           (Logika aplikacji: sklep, koszyk, logowanie)
└── readme.txt      (Dokumentacja)