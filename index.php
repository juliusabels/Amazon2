<?php
session_start(); // Session starten

// Verbindung zur Datenbank herstellen
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fakezon";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verbindung überprüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Produkte laden (falls eine Kategorie ausgewählt wurde)
$category_filter = "";
if (isset($_GET['category'])) {
    $category_filter = "WHERE category = '" . $conn->real_escape_string($_GET['category']) . "'";
}

// Suchfunktion
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = "AND name LIKE '%" . $conn->real_escape_string($_GET['search']) . "%'";
}

// SQL-Query für Produkte
$sql = "SELECT id, name, price, image_url, category FROM products $category_filter $search_query";
$products_result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fakezon - Startseite</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./css/main.css">
</head>
<body>

<header>
    <div class="container">
        <div class="text-center">
            <h1 class="my-3">
                <a href="index.php">
                    <img class="img-fluid img-thumbnail" style="max-width: 30%" src="./img/logo.jpg" height="50%" alt="Scamazon" loading="lazy" />
                </a>
            </h1>
        </div>
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
                        <a class="nav-link" href="index.php?category=Oberteile">Oberteile</a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="nav-link" href="index.php?category=Accessoires">Accessoires</a>
                    </li>
                </ul>
                <form class="d-flex" method="GET" action="index.php">
                    <input class="form-control me-2" type="text" name="search" placeholder="Nach Produkten suchen..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button class="btn btn-outline-success" type="submit">Suchen</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Login / Registrieren / Logout -->
    <div class="text-end px-4">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Wenn der Benutzer eingeloggt ist -->
            <span>Willkommen, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        <?php else: ?>
            <!-- Wenn der Benutzer nicht eingeloggt ist -->
            <a href="login.php" class="btn btn-primary btn-sm">Login</a>
            <a href="registrierung.php" class="btn btn-secondary btn-sm">Registrieren</a>
        <?php endif; ?>
    </div>
</header>

<!-- Hauptinhalt der Seite -->
<div class="container my-5">
    <h2 class="text-center">Unsere Produkte</h2>
    <div class="row">
        <?php if ($products_result->num_rows > 0): ?>
            <?php while ($product = $products_result->fetch_assoc()): ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <?php if (!empty($product['image_url'])): ?>
                            <img class="card-img-top" src="data:image/jpeg;base64,<?php echo base64_encode($product['image_url']); ?>" alt="Bild von <?php echo htmlspecialchars($product['name']); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text">Preis: €<?php echo number_format($product['price'], 2); ?></p>
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Keine Produkte gefunden.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Footer-Bereich -->
<footer class="container border-top border-dark py-2">
    <p class="text-center">Alle Rechte vorbehalten &copy; <?php echo date("Y"); ?> Fakezon</p>
</footer>

</body>
</html>

<?php
// Verbindung zur Datenbank schließen
$conn->close();
?>

