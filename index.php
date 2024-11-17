<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fakezon - Startseite</title>
    <script>
        // Funktion zum Scrollen nach oben
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Funktion, um den Button bei Scroll-Position anzuzeigen/verstecken
        window.onscroll = function() {
            const scrollButton = document.getElementById("scrollToTopBtn");
            if (document.documentElement.scrollTop > 300) {
                scrollButton.style.display = "block";
            } else {
                scrollButton.style.display = "none";
            }
        };
    </script>
</head>
<body>

    <!-- Header-Bereich mit Titel der Webseite und Suchleiste -->
    <header>
        <h1>Willkommen bei Fakezon</h1>
        <p>Ihr Online-Marktplatz für alles Mögliche!</p>
        
        <!-- Kategorien-Buttons -->
        <nav>
            <a href="index.php">Alle Produkte</a>
            <a href="index.php?category=Oberteile">Oberteile</a>
            <a href="index.php?category=Accessoires">Accessoires</a>
            <!-- Weitere Kategorien können hier hinzugefügt werden -->
        </nav>

        <!-- Suchformular -->
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="Nach Produkten suchen..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Suchen</button>
        </form>
    </header>

    <!-- PHP-Code zum Abrufen und Filtern von Produkten aus der Datenbank -->
    <?php
    // Datenbankverbindung herstellen
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "fakezon";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verbindung überprüfen
    if ($conn->connect_error) {
        die("Verbindung fehlgeschlagen: " . $conn->connect_error);
    }

    // Suchbegriff und Kategorie-Filter festlegen
    $searchTerm = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

    // SQL-Abfrage aufbauen
    $sql = "SELECT id, name, description, price, image_url FROM products WHERE 1=1";

    // Filter für Kategorie
    if (!empty($category)) {
        $sql .= " AND category = '$category'";
    }

    // Filter für Suchbegriff
    if (!empty($searchTerm)) {
        $sql .= " AND name LIKE '%$searchTerm%'";
    }
    
    $result = $conn->query($sql);

    // Überprüfen, ob Produkte vorhanden sind
    if ($result->num_rows > 0) {
        echo "<section>";
        // Schleife durch die Produkte
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<h2><a href='product.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</a></h2>";
            echo "<p>Preis: €" . number_format($row['price'], 2) . "</p>";
            if (!empty($row['image_url'])) {
                echo '<img width="300" height="300" src="data:image/jpeg;base64,'.base64_encode($row['image_url']).'"/>';
            }
            echo "</div><hr>";
        }
        echo "</section>";
    } else {
        echo "<p>Keine Produkte gefunden.</p>";
    }

    // Verbindung schließen
    $conn->close();
    ?>

    <!-- Scroll-to-Top Button -->
    <button onclick="scrollToTop()" id="scrollToTopBtn" title="Nach oben scrollen"
            style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 1000; padding: 10px 15px;">
        ↑ Nach oben
    </button>

    <!-- Footer-Bereich -->
    <footer>
        <p>Alle Rechte vorbehalten &copy; <?php echo date("Y"); ?> Fakezon</p>
    </footer>

</body>
</html>
