<?php
// Eenvoudige admin pagina om aanvragen te bekijken
// WAARSCHUWING: Deze authenticatie methode is NIET veilig voor productie!
// Gebruik voor een live website een robuuster login systeem met sessies.

// Configureer hier je admin wachtwoord
$admin_password = 'jouw_super_geheime_admin_wachtwoord'; // <<< VERVANG DIT

// Controleer het ingevoerde wachtwoord
if (!isset($_POST['password']) || $_POST['password'] !== $admin_password) {
    // Toon login formulier als wachtwoord niet klopt of niet is ingevoerd
    ?>
    <!DOCTYPE html>
    <html lang="nl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - Siente Bien</title>
        <link rel="stylesheet" href="style.css"> <!-- Gebruik eventueel bestaande styling -->
        <style>
            .login-container {
                max-width: 400px;
                margin: 100px auto;
                padding: 30px;
                background-color: #f4ede7;
                border-radius: 10px;
                text-align: center;
            }
            .login-container h1 { margin-bottom: 20px; color: #333; }
            .login-container input[type="password"] { 
                width: 100%; 
                padding: 10px; 
                margin-bottom: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
                box-sizing: border-box;
            }
             .login-container button { 
                background-color: #a6c79a; 
                color: #fff; 
                padding: 10px 20px; 
                border: none; 
                border-radius: 25px; 
                cursor: pointer; 
                font-size: 1em;
                transition: background-color 0.3s ease;
            }
            .login-container button:hover { background-color: #84a17c; }
             .error-message { color: red; margin-top: 15px; }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h1>Admin Login</h1>
             <?php if (isset($_POST['password'])) { ?>
                 <p class="error-message">Onjuist wachtwoord.</p>
            <?php } ?>
            <form method="POST" action="admin.php">
                <input type="password" name="password" placeholder="Wachtwoord" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit; // Stop script executie na tonen login formulier
}

// Als het wachtwoord correct is, toon de admin pagina

// Database Connectie Gegevens (Moet overeenkomen met process_form.php)
// <<< VERVANG DEZE PLACEHOLDERS met jouw database gegevens
$db_host = 'db'; 
$db_name = 'contactdb'; 
$db_user = 'admin'; 
$db_pass = 'geheim'; 
$db_table = 'aanvragen'; 

$pdo = null;

// 5. Maak verbinding met de database
try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);

} catch (\PDOException $e) {
     // Log database connectie fouten
     error_log("Admin Database Connectie Fout: " . $e->getMessage());
     // Toon een foutmelding aan de admin gebruiker
     die('Er kon geen verbinding worden gemaakt met de database. Controleer de logs.');
}

// 4. Gegevens ophalen uit de database
$select_sql = "SELECT * FROM $db_table ORDER BY submission_time DESC";
$aanvragen = [];

try {
    $stmt = $pdo->query($select_sql);
    $aanvragen = $stmt->fetchAll();

} catch (\PDOException $e) {
    error_log("Admin Database Query Fout: " . $e->getMessage());
    die('Er is een fout opgetreden bij het ophalen van de aanvragen. Controleer de logs.');
}

$pdo = null; // Sluit de database verbinding

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Aanvragen - Siente Bien</title>
    <link rel="stylesheet" href="style.css"> <!-- Gebruik de algemene styling -->
    <style>
        /* Specifieke styling voor de admin pagina tabellen */
        .admin-table {
            width: 95%; /* Responsieve breedte */
            max-width: 1200px; /* Maximale breedte op grote schermen */
            margin: 20px auto; /* Centreert de tabel horizontaal */
            border-collapse: collapse; /* Verwijder ruimte tussen cellen */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: block; /* Zorgt ervoor dat margin: auto werkt */
            overflow-x: auto; /* Voegt scrollbar toe op kleine schermen indien nodig */
        }
        .admin-table th, .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .admin-table th {
            background-color: #a6c79a; /* Zachtgroene achtergrond */
            color: white;
        }
        .admin-table tbody tr:nth-child(even) {
            background-color: #f8f4f1; /* Crèmekleurige achtergrond voor even rijen */
        }
         .admin-table tbody tr:hover {
            background-color: #e0e0e0; /* Lichte grijze achtergrond bij hover */
        }
        .admin-table td {
            word-wrap: break-word; /* Zorgt dat lange teksten breken */
        }
         /* Pas de content-section padding aan voor de admin pagina om de tabel meer ruimte te geven */
        .content-section.admin-content {
            padding: 20px; /* Minder padding rond de content sectie op admin pagina */
        }

        @media (max-width: 768px) {
            .admin-table th, .admin-table td {
                padding: 8px 10px; /* Pas padding aan op kleinere schermen */
            }
        }

    </style>
</head>
<body>

    <main>
        <section class="content-section admin-content"> <!-- Voeg extra klasse toe -->
            <h1>Aanvragen Overzicht</h1>

            <?php if (empty($aanvragen)): ?>
                <p>Er zijn nog geen aanvragen ontvangen.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Naam</th>
                            <th>Adres</th>
                            <th>E-mail</th>
                            <th>Telefoon</th>
                            <th>Massage Keuze</th>
                            <th>Voorkeur Tijdstip</th>
                            <th>Opmerkingen</th>
                            <th>Datum/Tijd</th>
                            <th>reCAPTCHA Succes</th>
                            <th>reCAPTCHA Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aanvragen as $aanvraag): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($aanvraag['ID'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($aanvraag['naam']); ?></td>
                                <td><?php echo htmlspecialchars($aanvraag['adres']); ?></td>
                                <td><?php echo htmlspecialchars($aanvraag['email']); ?></td>
                                <td><?php echo htmlspecialchars($aanvraag['phone']); ?></td>
                                <td><?php echo htmlspecialchars($aanvraag['massage']); ?></td>
                                <td><?php echo htmlspecialchars($aanvraag['tijdstip']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($aanvraag['opmerkingen'])); ?></td>
                                <td><?php echo htmlspecialchars($aanvraag['submission_time']); ?></td>
                                <td><?php echo $aanvraag['recaptcha_success'] ? 'Ja' : 'Nee'; ?></td>
                                <td><?php echo htmlspecialchars($aanvraag['recaptcha_score'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

</body>
</html>
