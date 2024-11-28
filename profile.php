<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = $conn->real_escape_string($_POST['username']);
    $user_id = $_SESSION['user_id'];

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['tmp_name'])) {
        $profile_picture = base64_encode(file_get_contents($_FILES['profile_picture']['tmp_name']));
        $update_sql = $conn->prepare("UPDATE users SET username = ?, profile_picture = ? WHERE id = ?");
        $update_sql->bind_param("ssi", $new_username, $profile_picture, $user_id);
    } else {
        $update_sql = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $update_sql->bind_param("si", $new_username, $user_id);
    }

    if ($update_sql->execute() === TRUE) {
        echo "Profil erfolgreich aktualisiert.";
    } else {
        echo "Fehler beim Aktualisieren des Profils: " . $conn->error;
    }
}

$user_sql = $conn->prepare("SELECT username, firstname, name, profile_picture FROM users WHERE id = ?");
$user_sql->bind_param("i", $_SESSION['user_id']);
$user_sql->execute();
$user = $user_sql->get_result()->fetch_assoc();
$pfp = !empty($user['profile_picture']) ? "data:image/jpeg;base64," . $user['profile_picture'] : "img/unknown_user.png";

$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="./css/main.css">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['firstname']); ?> - Fakezon</title>
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
    <nav class="navbar navbar-expand-lg bg-warning px-5 py-4 mx-auto flex-row-reverse ">
            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
            </div>
    </nav>
</header>

<h2 class="display-3 text-center mt-3">Profil von <?php echo htmlspecialchars($user['username']); ?></h2>
<div class="container my-5 mx-auto">
    <div class="row">
        <div class="col-md-6 text-center align-middle my-auto">
            <img src="<?php echo $pfp; ?>" class="img-fluid img-thumbnail" alt="Profilbild">
        </div>
        <div class="col-md-6">
            <h3>Profilinformationen</h3>
            <div class="row my-3">
                <div class="col-md-6">
                    <strong>Vorname:</strong>
                </div>
                <div class="col-md-6">
                    <?php echo htmlspecialchars($user['firstname']); ?>
                </div>
            </div>
            <div class="row my-3">
                <div class="col-md-6">
                    <strong>Nachname:</strong>
                </div>
                <div class="col-md-6">
                    <?php echo htmlspecialchars($user['name']); ?>
                </div>
            </div>
            <div class="row my-3">
                <div class="col-md-6">
                    <strong>Nutzername:</strong>
                </div>
                <div class="col-md-6">
                    <?php echo htmlspecialchars($user['username']); ?>
                </div>
            </div>

            <!-- Form to update username and profile picture -->
            <form method="POST" action="profile.php" enctype="multipart/form-data">
                <div class="form-group my-2">
                    <label for="username">Neuer Nutzername:</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                </div>
                <div class="form-group my-2">
                    <label for="profile_picture">Neues Profilbild:</label>
                    <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary mt-3" href="index.php">Profil aktualisieren</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center align-middle my-auto">
            <!-- Benutzer-Login -->
            <div class="mx-auto">
                    <?php
                    if (isset($_SESSION['user_id'])) {
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