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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="./css/main.css">

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

<!-- Header-Bereich mit Titel der Webseite und Suchleiste -->
<header>
        <div class="container">
            <div class="text-center">
                <h1 class="my-3">
                    <img class="img-fluid img-thumbnail" style="max-width: 30%"
                            src="./img/hilfe.jpg"
                            height=50%;
                            alt="Scamazon"
                            loading="lazy" />
                </h1>
            </div>
            
            <h1 class="mx-auto text-center">Willkommen bei Fakezon</h1>
            <p  class="mx-auto text-center">Ihr Online-Marktplatz für alles Mögliche!</p>
        </div>
        <!-- Kategorien-Buttons -->
        <nav class="navbar navbar-expand-lg bg-warning px-5 mx-auto">
            <div class="container-fluid">
	            <a class="navbar-brand p-2" href="index.php">Alle Produkte</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item mx-3">
                            <a class="nav-link" aria-current="page" href="index.php?category=Oberteile">Oberteile</a>
                        </li>
                        <li class="nav-item mx-3">
                            <a class="nav-link" href="index.php?category=Accessoires">Accessoires</a>
                        </li>
                    </ul>
                    <form class="d-flex" role="search" method="GET" action="index.php">
                        <input class="form-control me-2" type="search" placeholder="Nach Produkten suchen..." aria-label="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button class="btn btn-outline-success" type="submit">Suchen</button>
                    </form>
                </div>
            </div>
        </nav>

        
    </header>

<body class = body>

<div class="container mx- px-5 mt-5">
    <h1 class="text-center my-5"> Bestellinformationen</h1>
    <?php if ($product): ?>
         <!-- Produktdetails anzeigen -->
         <h2 class="fst-italic">Produkt: <?php echo htmlspecialchars($product['name']); ?></h2>
         <p class="mb-5">Preis: €<?php echo number_format($product['price'], 2); ?></p>
    <?php else: ?>
        <p class="my-5">Produkt konnte nicht gefunden werden.</p>
        <?php exit; ?>
    <?php endif; ?>
    
    <!-- Bestellformular -->
    <form class="row g-3" method="POST" action="checkout.php?product_id=<?php echo $product_id; ?>">
        <div class="col-md-12">
            <label for="name" class="form-label">Name:</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Max Mustermann" required>
        </div>
        <div class="col-md-12">
            <label for="email" class="form-label">E-Mail:</label>
            <input type="text" class="form-control" id="email" name="email" placeholder="max.mustermann@bsp.com" required>
        </div>
        <div class="col-md-12">
            <label for="address" class="form-label">Adresse:</label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Muster Straße 1" required>
        </div>
        <div class="col-md-4">
            <label for="zip" class="form-label">PLZ:</label>
            <input type="text" class="form-control" id="zip" name="zip" placeholder="40547" required>
        </div>
        <div class="col-md-8">
            <label for="city" class="form-label">Stadt:</label>
            <input type="text" class="form-control" id="city" name="city" placeholder="Düsseldorf" required>
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary"> Absenden </button><br><br>
    </form>
</div>

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
    
<?php
}
?>

<!-- Footer-Bereich -->
<footer class="container border-top border-dark py-2 mt-3">
        <p>Alle Rechte vorbehalten &copy; <?php echo date("Y"); ?> Fakezon</p>
    </footer>

</body>
</html>

<?php
// Verbindung zur Datenbank schließen
$conn->close();
?>

