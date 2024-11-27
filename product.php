<?php
    session_start();
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
                    <a href="index.php">
                        <img class="img-fluid img-thumbnail" style="max-width: 30%"
                            src="./img/logo.jpg"
                            height=50%;
                            alt="Scamazon"
                            loading="lazy" />
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
                            <a class="nav-link" aria-current="page" href="index.php?category=Oberteile">Oberteile</a>
                        </li>
                        <li class="nav-item mx-3">
                            <a class="nav-link" href="index.php?category=Accessoires">Accessoires</a>
                        </li>
                    </ul>
                    <form class="d-flex" method="GET" action="index.php">
                        <input class="form-control me-2" type="text" name="search" placeholder="Nach Produkten suchen..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button class="btn btn-outline-success" type="submit">Suchen</button>
                        <i class="fas fa-user"></i>
                    </form>
                    <!-- Benutzer-Login -->
                    <div class="text-end px-4">
                        <?php
                        if (isset($_SESSION['user_id'])) {
                            $user_sql = "SELECT firstname, profile_picture FROM users WHERE id = '" . $_SESSION['user_id'] . "'";
                            $user = $conn->query($user_sql)->fetch_assoc();
                            $pfp = !empty($user['profile_picture']) ? "data:image/jpeg;base64," . $user['profile_picture'] : "img/unknown_user.png";

                            echo '<a href="profile.php" class="p"><img src="' . $pfp . '" class="rounded-circle" width="30" height="30" alt="Profilbild"></a>';
                            echo " ";
                            echo '<a href="logout.php" class="btn btn-danger btn-sm"> Logout</a>';

                        } else {
                            echo '<a href="login.php" class="btn btn-primary btn-sm">Login</a>';
                            echo '<a href="registrierung.php" class="btn btn-secondary btn-sm">Registrieren</a>';
                        }
                        ?>
                    </div>
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
                            <?php
                            $ratings_sql = "SELECT rating FROM ratings WHERE productId = $product_id";
                            $ratings = $conn->query($ratings_sql);

                            if ($ratings->num_rows > 0) {
                                $rating_sum = 0;
                                foreach ($ratings as $rating) {
                                    $rating_sum += $rating['rating'];
                                }
                                $average_rating = $rating_sum / $ratings->num_rows;
                                echo "<p>Bewertung: " . number_format($average_rating, 1) . "/5</p>";
                                echo "<p>Anzahl der Bewertungen " . $ratings->num_rows . " </p>";
                            }
                            ?>

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
                                    $similar_sql = "SELECT id, name, image_url, price FROM products WHERE category = '$category' AND id != $product_id LIMIT 4";
                                    $similar_result = $conn->query($similar_sql);

                                    if ($similar_result->num_rows > 0) {
                                        while ($similar_product = $similar_result->fetch_assoc()) {
                                            echo "<div class='card col mb-4 mx-auto bg-secondary' style='width:17rem;' id='cardHover'>";
                                            if (!empty($similar_product['image_url'])) {
                                                echo '<a href="product.php?id=' . $similar_product["id"] . '" ><img class="card-img-top rounded mt-3" alt="Bild konnte nicht geladen werden" width="300" height="300" src="data:image/jpeg;base64,'.base64_encode($similar_product['image_url']).'"/></a>';
                                            }
                                            echo "<div class='card-body'>";
                                            echo "<h2 class='card-title text-center'><a href='product.php?id=" . $similar_product['id'] . "' >" . htmlspecialchars($similar_product['name']) . "</a></h2>";
                                            echo "<p class='card-footer text-center border'>Preis: €" . number_format($similar_product['price'], 2) . "</p>
                                                </div>";
                                            echo "</div>";
                                        }
                                    } else {
                                        echo "<p>Keine ähnlichen Produkte gefunden.</p>";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- Zurück-zur-Startseite Button -->
                    <div class="text-center my-4">
                        <a href="index.php">
                            <button class="btn btn-primary">Zurück zur Startseite</button>
                        </a>
                    </div>
                </div>
            </div>
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

    <!-- Rezensionen -->
    <?php

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $check_rating_sql = "SELECT id FROM ratings WHERE productId = $product_id AND userId = $user_id";
        $check_rating_result = $conn->query($check_rating_sql);

        if ($check_rating_result->num_rows > 0) {
            echo '<p>Sie haben dieses Produkt bereits bewertet.</p>';
        } else {
            echo '<h4>Produkt bewerten</h4>';
            echo '<form method="POST" action="add_rating.php">';
            echo '<input type="hidden" name="product_id" value="' . $product_id . '">';
            echo '<div class="form-group">';
            echo '<label for="rating">Bewertung:</label>';
            echo '<select class="form-control" id="rating" name="rating">';
            echo '<option value="1">1/5</option>';
            echo '<option value="2">2/5</option>';
            echo '<option value="3">3/5</option>';
            echo '<option value="4">4/5</option>';
            echo '<option value="5">5/5</option>';
            echo '</select>';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label for="comment">Kommentar:</label>';
            echo '<textarea class="form-control" id="comment" name="comment" rows="3"></textarea>';
            echo '</div>';
            echo '<br>';
            echo '<button type="submit" class="btn btn-primary">Bewertung abschicken</button>';
            echo '</form>';
        }
    } else {
        echo '<p>Bitte <a href="login.php">melden Sie sich an</a>, um eine Bewertung abzugeben.</p>';
    }
    echo '<br>';
    echo '<h2>Rezensionen</h2>';
    $ratings_sql = "SELECT id, userId, rating, comment, date FROM ratings WHERE productId = $product_id ORDER BY date DESC";
    $ratings = $conn->query($ratings_sql);
    if ($ratings->num_rows > 0) {
        while ($rating = $ratings->fetch_assoc()) {
            $user_sql = "SELECT username, profile_picture FROM users WHERE id = " . $rating['userId'];
            $user = $conn->query($user_sql)->fetch_assoc();

            $pfp = !empty($user['profile_picture']) ? "data:image/jpeg;base64," . $user['profile_picture'] : "img/unknown_user.png";
            echo  "<p><strong>" . '<img src="' . $pfp . '" class="rounded-circle" width="20" height="20" alt="Profilbild">' . " " . htmlspecialchars($user['username']) . "</strong> - Bewertung: " . $rating['rating'] . "/5</p>";
            echo "<p>" . htmlspecialchars($rating['comment']) . " - " . date("d/m/y H:i", strtotime($rating['date'])) . "</p>";
        }
    } else {
        echo "<p>Keine Rezensionen verfügbar</p>";
    }

    ?>

</body>
</html>

<?php
    // Verbindung schließen
    $conn->close();
?>