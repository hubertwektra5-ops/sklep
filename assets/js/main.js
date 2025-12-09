// assets/js/main.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('WinZone Sklep załadowany.');

    // Potwierdzenie usuwania
    const deleteLinks = document.querySelectorAll('a[href*="delete"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm("Czy na pewno chcesz usunąć ten element?")) {
                e.preventDefault();
            }
        });
    });
});