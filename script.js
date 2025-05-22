document.addEventListener('DOMContentLoaded', function() {
    const hamburgerButton = document.querySelector('.hamburger-icon');
    const header = document.querySelector('header');

    if (hamburgerButton && header) {
        hamburgerButton.addEventListener('click', function() {
            header.classList.toggle('nav-open');
        });
    }
});