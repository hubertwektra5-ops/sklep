<?php
require_once 'config/db.php';

$msg = '';

// Symulacja wysyłania wiadomości
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    // Tutaj normalnie byłaby funkcja mail()
    $msg = "<div class='alert alert-success'>Wiadomość została wysłana! Odpowiemy wkrótce.</div>";
}

require_once 'views/header.php';
?>

<div class="container content-padding">
    
    <h1 class="text-center" style="margin-bottom: 40px; color: var(--text-main);">Skontaktuj się z nami</h1>

    <?= $msg ?>

    <div class="contact-wrapper">
        
        <div class="contact-info-box">
            <h3 style="color: var(--primary); margin-top: 0; margin-bottom: 25px;">Dane kontaktowe</h3>
            
            <div class="contact-item">
                <i class="fa-solid fa-envelope"></i>
                <div>
                    <strong>Email:</strong><br>
                    <a href="mailto:kontakt@winzone.pl" style="color: #ccc;">kontakt@winzone.pl</a>
                </div>
            </div>

            <div class="contact-item">
                <i class="fa-solid fa-clock"></i>
                <div>
                    <strong>Godziny pracy:</strong><br>
                    <span style="color: #ccc;">Codziennie: 9:00 - 22:00</span>
                </div>
            </div>

            <div class="contact-item">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                    <strong>Lokalizacja:</strong><br>
                    <span style="color: #ccc;">Warszawa, Polska (Online)</span>
                </div>
            </div>

            <h3 style="color: var(--text-main); margin-top: 40px; font-size: 18px;">Znajdź nas w aplikacjach:</h3>
            <div class="social-links">
                <a href="https://www.facebook.com/" target="_blank" class="social-btn fb" title="Facebook">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a href="https://www.instagram.com/" target="_blank" class="social-btn ig" title="Instagram">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="https://telegram.org/" target="_blank" class="social-btn tg" title="Telegram">
                    <i class="fa-brands fa-telegram"></i>
                </a>
                <a href="https://www.tiktok.com/" target="_blank" class="social-btn tt" title="TikTok">
                    <i class="fa-brands fa-tiktok"></i>
                </a>
            </div>
        </div>

        <div class="form-box" style="margin: 0; max-width: 100%;">
            <h3 style="color: var(--primary); margin-top: 0;">Napisz wiadomość</h3>
            <form method="post">
                <label>Twoje Imię</label>
                <input type="text" name="name" required placeholder="Wpisz imię...">

                <label>Twój Email</label>
                <input type="email" name="email" required placeholder="Wpisz email...">

                <label>Temat</label>
                <select name="topic">
                    <option>Pytanie o ofertę</option>
                    <option>Problem z płatnością</option>
                    <option>Współpraca</option>
                    <option>Inne</option>
                </select>

                <label>Treść wiadomości</label>
                <textarea name="message" rows="5" required placeholder="Opisz sprawę..."></textarea>

                <button type="submit" name="send_message" class="btn">Wyślij Wiadomość <i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>

    </div>
</div>

<?php require_once 'views/footer.php'; ?>