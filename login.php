<?php
// Datenbankverbindung herstellen
$servername = "localhost";
$username = "root";
$input_password = "";
$dbname = "fakezon";

$conn = new mysqli($servername, $username, $input_password, $dbname);

// Verbindung überprüfen
if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}

// Session starten
session_start();

// Prüfen, ob der Benutzer bereits eingeloggt ist
if (isset($_SESSION['user_id'])) {
    // Zur vorherigen Seite weiterleiten
    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
    header("Location: $redirect_url");
    exit;
}

// Ursprungsseite speichern (falls nicht die Registrierung)
if (isset($_SERVER['HTTP_REFERER']) && !isset($_SESSION['referrer']) && strpos($_SERVER['HTTP_REFERER'], 'registrierung.php') === false) {
    $_SESSION['referrer'] = $_SERVER['HTTP_REFERER'];
}

// Fehlermeldungen initialisieren
$errors = [];

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST["email"]));
    $input_password = htmlspecialchars(trim($_POST["password"]));

    // Validierung
    if (empty($email)) {
        $errors[] = "Bitte geben Sie Ihre E-Mail-Adresse ein.";
    }
    if (empty($input_password)) {
        $errors[] = "Bitte geben Sie Ihr Passwort ein.";
    }

    // Wenn keine Validierungsfehler vorliegen
    if (empty($errors)) {
        $query = "SELECT id, username, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Benutzer gefunden?
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $user_name, $hashed_password);
            $stmt->fetch();

            // Passwort überprüfen
            if (password_verify($input_password, $hashed_password)) {
                // Login erfolgreich, Benutzer in der Session speichern
                $_SESSION["user_id"] = $user_id;
                $_SESSION["user_name"] = $user_name;

                // Weiterleitung zur vorherigen Seite oder Startseite
                $redirect_url = isset($_SESSION['referrer']) ? $_SESSION['referrer'] : 'index.php';
                unset($_SESSION['referrer']);
                header("Location: $redirect_url");
                exit();
            } else {
                $errors[] = "Falsches Passwort.";
            }
        } else {
            $errors[] = "Es existiert kein Benutzer mit dieser E-Mail-Adresse.";
        }

        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fakezon</title>
    <link rel="stylesheet" href="css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="body">
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
    <div class="container mt-5">
        <h1 class="text-center">Einloggen bei Fakezon</h1>
        <form class="row g-3" method="POST" action="login.php">
            <!-- Fehlermeldungen anzeigen -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Formularfelder -->
            <div class="col-md-12">
                <label for="email" class="form-label">E-Mail:</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="max.mustermann@beispiel.com" required>
            </div>
            <div class="col-md-12">
                <label for="password" class="form-label">Passwort:</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Passwort" required>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Einloggen</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <p>Noch nicht registriert? <a href="registrierung.php">Hier registrieren</a></p>
        </div>
    </div>

    <footer class="container border-top border-dark py-2">
        <p>Alle Rechte vorbehalten &copy; <?php echo date("Y"); ?> Fakezon</p>
    </footer>

</body>
</html>
