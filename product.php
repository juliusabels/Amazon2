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
    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // SQL-Abfrage, um die Produktdetails zu laden
    $sql = "SELECT id, name, description, price, image_url, category, availability FROM products WHERE id = $product_id";
    $result = $conn->query($sql);

    // Produkt überprüfen
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "<p>Produkt nicht gefunden.</p>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Fakezon</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <?php if (!empty($product['image_url'])): ?>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($product['image_url']); ?>" alt="Bild von <?php echo htmlspecialchars($product['name']); ?>" width="500" height="500">
        <?php endif; ?>
    <p><?php echo htmlspecialchars_decode($product['description']); ?></p>
    <p>Preis: €<?php echo number_format($product['price'], 2); ?></p>
    <p><?php echo $product['availability'] ? 'Auf Lager' : 'Derzeit nicht verfügbar'; ?></p>

    <!-- Button zum Weiterleiten zur Bestellseite mit der Produkt-ID -->
    <a href="checkout.php?product_id=<?php echo $product['id']; ?>">
        <button>Kaufen</button>
    </a>

    <!-- Ähnliche Produkte aus der gleichen Kategorie anzeigen -->
    <h2>Ähnliche Produkte</h2>
    <?php
        $category = $product['category'];
        $similar_sql = "SELECT id, name FROM products WHERE category = '$category' AND id != $product_id LIMIT 4";
        $similar_result = $conn->query($similar_sql);

        if ($similar_result->num_rows > 0) {
            while ($similar_product = $similar_result->fetch_assoc()) {
                echo "<p><a href='product.php?id=" . $similar_product['id'] . "'>" . htmlspecialchars($similar_product['name']) . "</a></p>";
            }
        } else {
            echo "<p>Keine ähnlichen Produkte gefunden.</p>";
        }
    ?>

</body>
</html>

<?php
    // Verbindung schließen
    $conn->close();
?>