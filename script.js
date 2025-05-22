// ... (bestaande code voor hamburgermenu) ...

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

        // Formulier submit afhandeling (stuurt data naar server)
        appointmentForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Voorkom standaard formulier verzending

            // Verzamel formuliergegevens
            const formData = new FormData(appointmentForm);
            const formActionUrl = appointmentForm.getAttribute('action');

            // Verberg eerdere berichten en reset
            confirmationMessage.style.display = 'none';
            confirmationMessage.textContent = '';


            // Stuur gegevens met fetch API naar je server-side script
            fetch(formActionUrl, {
                method: 'POST',
                body: formData // FormData bevat alle velden, inclusief reCAPTCHA response
            })
            .then(response => {
                // Controleer of de response OK is (status 200-299)
                if (!response.ok) {
                     throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json(); // Verwacht een JSON antwoord
            })
            .then(data => {
                // Verwerk het antwoord van de server
                if (data.success) {
                    confirmationMessage.textContent = data.message; // Toon succesboodschap van server
                    confirmationMessage.style.display = 'block';
                    confirmationMessage.style.backgroundColor = '#dff0d8'; // Groene achtergrond
                    confirmationMessage.style.color = '#3c763d'; // Donkergroene tekst
                    appointmentForm.reset(); // Reset formulier na succesvolle verzending

                    // Optioneel: Verberg het formulier na succes
                    // formContainer.classList.remove('active');
                    // openFormButton.style.display = 'block'; // Toon de knop eventueel weer
                } else {
                    confirmationMessage.textContent = data.message; // Toon foutboodschap van server
                    confirmationMessage.style.display = 'block';
                    confirmationMessage.style.backgroundColor = '#f2dede'; // Rode/roze achtergrond
                    confirmationMessage.style.color = '#a94442'; // Donkerrode tekst
                }
            })
            .catch((error) => {
                // Afhandeling van netwerkfouten e.d.
                console.error('Error:', error);
                confirmationMessage.textContent = 'Er is een algemene fout opgetreden. Probeer het later opnieuw.';
                confirmationMessage.style.display = 'block';
                confirmationMessage.style.backgroundColor = '#f2dede';
                confirmationMessage.style.color = '#a94442';
            });
        });
    }
// ... (einde DOMContentLoaded) ...