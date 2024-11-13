<?php
// Verbindung zur Datenbank herstellen (Datenbank-Details anpassen)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fakezon";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verbindung überprüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Produkt-ID aus der URL abrufen
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Produktdetails abrufen
$product_sql = "SELECT name, price FROM products WHERE id = $product_id";
$product_result = $conn->query($product_sql);
$product = $product_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellseite - Fakezon</title>
    <script>
        // Funktion, um Pop-up mit Bestellnummer anzuzeigen
        function showThankYouPopup(orderNumber) {
            alert("Vielen Dank für Ihre Bestellung! Ihre Bestellnummer ist: " + orderNumber);
        }
    </script>
</head>
<body>

<h1>Bestellinformationen</h1>

<?php if ($product): ?>
    <!-- Produktdetails anzeigen -->
    <h2>Produkt: <?php echo htmlspecialchars($product['name']); ?></h2>
    <p>Preis: €<?php echo number_format($product['price'], 2); ?></p>
<?php else: ?>
    <p>Produkt konnte nicht gefunden werden.</p>
    <?php exit; ?>
<?php endif; ?>

<?php
// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Eingabedaten sichern
    $name = htmlspecialchars($_POST['name']);
    $address = htmlspecialchars($_POST['address']);
    $zip = htmlspecialchars($_POST['zip']);
    $city = htmlspecialchars($_POST['city']);
    $email = htmlspecialchars($_POST['email']);

    // Zufällige Bestellnummer generieren
    $orderNumber = "FAKEZON-" . rand(100000, 999999);

    // E-Mail-Versand vorbereiten
    $to = $email;
    $subject = "Bestellbestätigung - Fakezon";
    $message = "Hallo $name,\n\nVielen Dank für Ihre Bestellung bei Fakezon!\n\n" .
               "Produkt: " . $product['name'] . "\n" .
               "Preis: €" . number_format($product['price'], 2) . "\n" .
               "Bestellnummer: $orderNumber\n\n" .
               "Ihre Lieferadresse:\n" .
               "Name: $name\n" .
               "Adresse: $address\n" .
               "PLZ: $zip\n" .
               "Ort: $city\n\n" .
               "Wir werden Ihre Bestellung so bald wie möglich bearbeiten.\n\n" .
               "Mit freundlichen Grüßen,\nDas Fakezon-Team";
    $headers = "From: noreply@fakezon.com";

    // E-Mail senden
    if (mail($to, $subject, $message, $headers)) {
        // E-Mail erfolgreich gesendet, Bestellnummer im Pop-up anzeigen
        echo "<script>showThankYouPopup('$orderNumber');</script>";
    } else {
        echo "<p>Die Bestellung konnte nicht bearbeitet werden. Bitte versuchen Sie es später erneut.</p>";
    }
} else {
?>
    <!-- Bestellformular -->
    <form method="POST" action="checkout.php?product_id=<?php echo $product_id; ?>">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="address">Adresse:</label><br>
        <input type="text" id="address" name="address" required><br><br>

        <label for="zip">PLZ:</label><br>
        <input type="text" id="zip" name="zip" required><br><br>

        <label for="city">Ort:</label><br>
        <input type="text" id="city" name="city" required><br><br>

        <label for="email">E-Mail:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <button type="submit">Absenden</button>
    </form>
<?php
}
?>

</body>
</html>

<?php
// Verbindung zur Datenbank schließen
$conn->close();
?>

