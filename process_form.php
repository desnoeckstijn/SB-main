<?php
// Dit script verwerkt de contactformulier gegevens, verifieert reCAPTCHA en slaat data op in de database.

// Stel headers in voor JSON response
header('Content-Type: application/json');

// Standaard response array
$response = array('success' => false, 'message' => 'Er ging iets mis bij de verwerking.');

// Database Connectie Gegevens
// <<< VERVANG DEZE PLACEHOLDERS met jouw database gegevens
$db_host = 'db'; // Of de naam van je database service in Docker Compose
$db_name = 'contactdb'; // <<<
$db_user = 'admin'; // <<<
$db_pass = 'geheim'; // <<<
$db_table = 'aanvragen'; // De naam van de tabel die je hebt aangemaakt

// reCAPTCHA Geheime Sleutel
// <<< VERVANG DEZE PLACEHOLDER met je EIGEN geheime reCAPTCHA sleutel
$recaptcha_secret = 'YOUR_RECAPTCHA_SECRET_KEY';

// Controleer of het een POST request is
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Ontvang en Sanitize formuliergegevens

    $naam = htmlspecialchars(trim($_POST['naam'] ?? ''));
    $adres = htmlspecialchars(trim($_POST['adres'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $massage = htmlspecialchars(trim($_POST['massage'] ?? ''));
    $tijdstip = htmlspecialchars(trim($_POST['tijdstip'] ?? ''));
    $opmerkingen = htmlspecialchars(trim($_POST['opmerkingen'] ?? ''));
   // $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    // 2. Valideer verplichte velden
    if (empty($naam) || empty($adres) || empty($email) || empty($phone) || empty($massage)) { //  || empty($recaptcha_response)
        $response['message'] = 'Vul alstublieft alle verplichte velden in.';
        echo json_encode($response);
        exit;
    }

   /* // 3. reCAPTCHA Verificatie
    $verification_url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $verification_url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); // Laat deze AAN staan in productie!
    $recaptcha_result = curl_exec($curl);
    $curl_error = curl_error($curl);
    curl_close($curl);

    if ($curl_error) {
         // error_log("cURL Error: " . $curl_error);
         $response['message'] = 'Er kon geen verbinding worden gemaakt voor reCAPTCHA verificatie.';
         echo json_encode($response);
         exit;
    }

    $recaptcha_decoded = json_decode($recaptcha_result, true);
    $recaptcha_success = $recaptcha_decoded['success'];
    $recaptcha_score = $recaptcha_decoded['score'] ?? null; // v2 heeft meestal geen score, v3 wel

    // 4. Verwerk op basis van reCAPTCHA resultaat (en andere validatie)
    if ($recaptcha_success) { // Contoleer of reCAPTCHA succesvol was

        // 5. Maak verbinding met de database
*/
error_log("Poging tot verbinden met database. Host: " . $db_host . ", DB: " . $db_name . ", User: " . $db_user);
try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4"; // Voeg hier poort=3306 toe als test
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    error_log("Database verbinding succesvol!"); // Deze lijn zal je zien als de connectie lukt

} catch (\PDOException $e) {
     error_log("Database Connectie Fout: " . $e->getMessage()); // DEZE fout wil je zien om de precieze reden te achterhalen
     $response['message'] = 'Er kon geen verbinding worden gemaakt met de database.'; // Deze boodschap krijgt de gebruiker
     echo json_encode($response);
     exit;
}

// 6. Sla de gegevens op in de database
// Pas de kolomnamen hier aan als ze anders zijn in je tabel
$insert_sql = "INSERT INTO $db_table (name, address, email, phone, massage_choice, preferred_time, comments, submission_time, recaptcha_success) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 1)"; // recaptcha_success is nu 1 (true)

        try {
            $stmt = $pdo->prepare($insert_sql);
            $stmt->execute([$naam, $adres, $email, $phone, $massage, $tijdstip, $opmerkingen]);

            // Als de insert succesvol was
            $response['success'] = true;
            $response['message'] = 'Bedankt! Je aanvraag werd goed verzonden en opgeslagen.';

            // --- E-mail Notificatie (Optioneel, hier later te implementeren) ---
            // Je kunt hier de e-mailverzending activeren na succesvolle database opslag,
            // of een apart proces gebruiken dat de database periodiek controleert.
            // Voor nu staat deze logica hieronder uit commentaar.
            /*
            $to = 'ola@sientebien.be'; // Vervang door je e-mail
            $subject = 'Nieuwe afspraak aanvraag via Siente Bien Website';

            $email_body = "Je hebt een nieuwe afspraak aanvraag ontvangen:\n\n";
            $email_body .= "Naam: " . $naam . "\n";
            $email_body .= "Adres: " . $adres . "\n";
            $email_body .= "E-mail: " . $email . "\n";
            $email_body .= "Telefoon: " . $phone . "\n";
            $email_body .= "Keuze massage: " . $massage . "\n";
            $email_body .= "Voorkeur tijdstip: " . ($tijdstip ? $tijdstip : 'Niet opgegeven') . "\n";
            $email_body .= "Opmerkingen:\n" . ($opmerkingen ? $opmerkingen : 'Geen') . "\n";

            $headers = "From: Siente Bien Website <no-reply@jouwdomein.be>\r\n"; // Vervang door een geldig afzender adres
            $headers .= "Reply-To: " . $naam . " <" . $email . ">\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/plain; charset=UTF-8\r\n";

            // mail($to, $subject, $email_body, $headers);
            // Overweeg error handling voor mail() als je dit inschakelt.
            */
            // --- Einde E-mail Notificatie ---

        } catch (\PDOException $e) {
             // Log database insert fouten
             // error_log("Database Insert Fout: " . $e->getMessage());
             $response['message'] = 'Er is een fout opgetreden bij het opslaan van je aanvraag.';
        }

//    } else {
//        // reCAPTCHA verificatie mislukt
//        $response['message'] = 'reCAPTCHA verificatie mislukt. Probeer het opnieuw.';
//        // error_log("reCAPTCHA Error: " . print_r($recaptcha_decoded['error-codes'], true)); // Log errors van Google
//    }

} else {
    // Ongeldige aanvraagmethode
    $response['message'] = 'Ongeldige aanvraagmethode.';
}

// 7. Stuur de JSON response terug naar de client (JavaScript)
echo json_encode($response);

?>