<footer class="main-footer">
    <div class="container">
        
        <div style="margin-bottom: 20px; opacity: 0.5;">
            <img src="<?= (strpos($_SERVER['REQUEST_URI'], 'admin') !== false) ? '../' : '' ?>assets/img/favicon.png" alt="WinZone" style="height: 30px; filter: grayscale(100%);">
        </div>

        <div class="footer-links">
            <a href="<?= (strpos($_SERVER['REQUEST_URI'], 'admin') !== false) ? '../' : '' ?>offer.php">Oferta</a>
            <a href="<?= (strpos($_SERVER['REQUEST_URI'], 'admin') !== false) ? '../' : '' ?>privacy.php">Polityka Prywatności</a>
            <a href="<?= (strpos($_SERVER['REQUEST_URI'], 'admin') !== false) ? '../' : '' ?>terms.php">Regulamin</a>
            <a href="<?= (strpos($_SERVER['REQUEST_URI'], 'admin') !== false) ? '../' : '' ?>contact.php">Kontakt</a>
        </div>
        
        <p class="legal-note">
            &copy; 2025 WinZone. Wszelkie prawa zastrzeżone.<br>
            Serwis przeznaczony dla osób pełnoletnich (18+). Hazard wiąże się z ryzykiem.
        </p>
    </div>
</footer>
</body>
</html>