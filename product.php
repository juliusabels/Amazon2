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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="./css/main.css">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Fakezon</title>
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


<body class="body" id="body">

    <!-- Header-Bereich mit Titel der Webseite und Suchleiste -->
    <header>
        <div class="container">
            <div class="text-center">
                <h1 class="my-3">
                    <img class="img-fluid img-thumbnail" style="max-width: 30%"
                            src="./img/logo.jpg"
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


    <div class="container d-flex justify-content-center my-50">
        <div class="row">
            <div class= "col-md-10" >
                <div class="card card-body">
                    <div class="media align-items-center align-items-lg-start text-center text-lg-left flex-column flex-lg-row">
                        <div class="mr-2 mb-3 mb-lg-0">
                            <?php if (!empty($product['image_url'])): ?>
                                <img class="img-responsive rounded border-black "src="data:image/jpeg;base64,<?php echo base64_encode($product['image_url']); ?>" alt="Bild von <?php echo htmlspecialchars($product['name']); ?>" width="75%">
                            <?php endif; ?>
                        </div>
                        <div class="media-body">
                            <h2 class="media title font-weight-semibold fst-italic">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </h2>

                            <p class="my-3">
                                <?php echo htmlspecialchars_decode($product['description']); ?>
                            </p>
                        </div>

                        <div class="pt-3 pt-lg-0 pl-lg-3 text-center border order-black">
                            <h3 class="mb-0 font-weight-semibold">
                                Preis: €<?php echo number_format($product['price'], 2); ?>
                            </h3>
                            <div class="text-muted">
                                <?php echo $product['availability'] ? 'Auf Lager' : 'Derzeit nicht verfügbar'; ?>
                            </div>
                            <!-- Button zum Weiterleiten zur Bestellseite mit der Produkt-ID -->
                            <a href="checkout.php?product_id=<?php echo $product['id']; ?>">
                                <button class="btn btn-warning mt-4 mb-2 text-shite" type="button">Kaufen</button>
                            </a>
                        </div>

                        <div class="py-3">
                            <!-- Ähnliche Produkte aus der gleichen Kategorie anzeigen -->
                            <h2>Ähnliche Produkte</h2>
                            <div class="container d-md-block">
                                <?php
                                    $category = $product['category'];
                                    $similar_sql = "SELECT id, name FROM products WHERE category = '$category' AND id != $product_id LIMIT 4";
                                    $similar_result = $conn->query($similar_sql);

                                    if ($similar_result->num_rows > 0) {
                                        while ($similar_product = $similar_result->fetch_assoc()) {
//Youns vielleicht noch Bilder einfügen                                       
                                            echo "<br><p> <a class='productSimilar rounded p-2 my-4 mx-2' href='product.php?id=" . $similar_product['id'] . "'>" . htmlspecialchars($similar_product['name']) . "</a></p>";
                                        }
                                    } else {
                                        echo "<p>Keine ähnlichen Produkte gefunden.</p>";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zurück-zur-Startseite Button -->
    <div class="text-center my-4">
        <a href="index.php">
            <button class="btn btn-primary">Zurück zur Startseite</button>
        </a>
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

<?php
    // Verbindung schließen
    $conn->close();
?>
