document.addEventListener('DOMContentLoaded', function() {
    const hamburgerButton = document.querySelector('.hamburger-icon');
    const header = document.querySelector('header');

    if (hamburgerButton && header) {
        hamburgerButton.addEventListener('click', function() {
            header.classList.toggle('nav-open');
        });
    }

    // Functionaliteit voor het contactformulier op de contactpagina
    const openFormButton = document.getElementById('open-form-button');
    const formContainer = document.getElementById('contact-form-container');
    const appointmentForm = document.getElementById('appointment-form');
    const confirmationMessage = document.getElementById('confirmation-message');

    if (openFormButton && formContainer && appointmentForm && confirmationMessage) {
        // Toon het formulier bij klik op de knop
        openFormButton.addEventListener('click', function() {
            formContainer.classList.add('active');
            openFormButton.style.display = 'none'; // Verberg de knop na klik
        });

        // Basis formulier submit afhandeling (client-side)
        appointmentForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Voorkom standaard formulier verzending

            // Hier zou je normaal de formulierdata verzenden naar je server-side script via AJAX (fetch of XMLHttpRequest).
            // Je server-side script zou dan de reCAPTCHA verifiëren en de e-mail versturen.
            // Voor nu simuleren we een succesvolle verzending en tonen we de bevestiging.

            console.log('Formulier ingediend (simulatie). Gegevens zouden nu naar de server gestuurd worden.');
            // Toon bevestigingsboodschap
            confirmationMessage.textContent = 'Bedankt! Je aanvraag werd goed verzonden.';
            confirmationMessage.style.display = 'block';

            // Optioneel: Verberg het formulier na verzending
            // formContainer.classList.remove('active');
            // openFormButton.style.display = 'block'; // Toon de knop eventueel weer

            // Optioneel: Reset het formulier
            // appointmentForm.reset();

            // BELANGRIJK: Implementeer hier de echte AJAX request naar je server-side script!
        });
    }
});