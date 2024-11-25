<!DOCTYPE html>
<html lang="de">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide" type='text/css'>
<link rel="stylesheet" href="./css/main.css">


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
<body class="body">
    <!-- Header-Bereich mit Titel der Webseite und Suchleiste -->
    <header>
        <div class="container">
            <h1 class="mx-auto">Willkommen bei Fakezon</h1>
            <p>Ihr Online-Marktplatz für alles Mögliche!</p>
        </div>
        <!-- Kategorien-Buttons -->
        <nav class="navbar navbar-expand-md bg-warning p-0 mx-auto">
            <div class="container-fluid px-5 py-2">
                <a class="navbar-brand color-black" href="index.php">Alle Produkte</a>
                <button
                    class="navbar-toggler"
                    type="button"
                    data-toggle="collapse"
                    data-target="#navbarID"
                    aria-controls="navbarID"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarID">
                    <div class="navbar-nav mr-auto">
                        <a class="nav-link" href="index.php?category=Oberteile">Oberteile</a>
                        <a class="nav-link" href="index.php?category=Accessoires">Accessoires</a>
                        <!-- Weitere Kategorien können hier hinzugefügt werden -->
                    </div>

                    <!-- Suchformular -->
                    <form class="d-flex" method="GET" action="index.php">
                        <input class="form-control mr-2" type="search" name="search" placeholder="Nach Produkten suchen..." 
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button class="btn btn-outline-success" type="submit">Suchen</button>
                    </form>
                </div>
            </div>
        </nav>

        
    </header>
    
    <div  class="container mx-auto mt-4">
        <div class="row row-cols-4">

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

                    // Schleife durch die Produkte
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='card col mb-4' style='width:18rem;'>";
                        if (!empty($row['image_url'])) {
                            echo '<img class="card-img-top rounded mt-1" alt="Bild konnte nicht geladen werden" width="300" height="300" src="data:image/jpeg;base64,'.base64_encode($row['image_url']).'"/>';
                        }
                        echo "<div class='card-body'>";
                        echo "<h2 class='card-title'><a href='product.php?id=" . $row['id'] . "' >" . htmlspecialchars($row['name']) . "</a></h2>";
                        echo "<p class='card-footer text-center'>Preis: €" . number_format($row['price'], 2) . "</p>
                            </div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Keine Produkte gefunden.</p>";
                }

                // Verbindung schließen
                $conn->close();
            ?>

        <!-- Schließen des card-container und row div-->
        </div>
    </div>


    <!-- Scroll-to-Top Button -->
    <button class="btn btn-danger btn-floating btn-lg" onclick="scrollToTop()" id="scrollToTopBtn" title="Nach oben scrollen">
        ↑ Nach oben
    </button>

    <!-- Footer-Bereich -->
    <footer class="container border-top border-dark py-2">
        <p>Alle Rechte vorbehalten &copy; <?php echo date("Y"); ?> Fakezon</p>
    </footer>

</body>
</html>
