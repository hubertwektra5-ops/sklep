<?php require_once 'config/db.php'; require_once 'views/header.php'; ?>

<section class="hero-section">
    <div class="container">
        <div class="hero-wrapper">
            
            <div class="hero-text">
                <h1 class="hero-title">Profesjonalne analizy sportowe</h1>
                <p class="hero-subtitle">Piłka Nożna • NBA • NHL • Tenis</p>
                <a href="offer.php" class="hero-btn">Zobacz Ofertę</a>
            </div>

            <div class="hero-image">
                <img src="assets/img/logo.png" alt="WinZone Big" class="hero-logo">
            </div>

        </div>
    </div>
</section>

<section class="features-section">
    <div class="container features-grid">
        <div class="feature-box">
            <i class="fa-solid fa-ranking-star"></i>
            <h3>Wysokie Kursy</h3>
            <p>Polujemy na okazje, nie na pewniaki po kursie 1.1.</p>
        </div>
        <div class="feature-box">
            <i class="fa-solid fa-lock"></i>
            <h3>Bezpieczeństwo</h3>
            <p>Szyfrowane dane i bezpieczne płatności.</p>
        </div>
        <div class="feature-box">
            <i class="fa-solid fa-bolt"></i>
            <h3>Szybki Dostęp</h3>
            <p>Automat wysyła typy natychmiast na maila.</p>
        </div>
    </div>
</section>

<section class="reviews-section">
    <div class="container">
        <h2 class="reviews-header">Opinie naszych klientów</h2>
        <div class="reviews-scroll">
            <div class="review-card">
                <img src="assets/img/opinia1.jpg" alt="Opinia 1" class="review-img">
            </div>
            <div class="review-card">
                <img src="assets/img/opinia2.jpg" alt="Opinia 2" class="review-img">
            </div>
            <div class="review-card">
                <img src="assets/img/opinia3.jpg" alt="Opinia 3" class="review-img">
            </div>
        </div>
    </div>
</section>

<?php require_once 'views/footer.php'; ?>