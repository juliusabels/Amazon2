<?php
// Datenbankverbindung herstellen
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fakezon";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verbindung überprüfen
if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}

// Session starten
session_start();

// Ursprungsseite speichern (falls nicht bereits gesetzt)
if (isset($_SERVER['HTTP_REFERER']) && !isset($_SESSION['referrer']) && strpos($_SERVER['HTTP_REFERER'], 'login.php') === false) {
    $_SESSION['referrer'] = $_SERVER['HTTP_REFERER'];
}

// Funktion zum Validieren von Eingaben
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Fehlermeldungen initialisieren
$errors = [];

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = validate_input($_POST["firstname"]);
    $name = validate_input($_POST["name"]);
    $email = validate_input($_POST["email"]);
    $password = validate_input($_POST["password"]);
    $password_confirm = validate_input($_POST["password_confirm"]);

    //Default Nutzername erstellen
    $username = strtolower($firstname) . "." . strtolower($name);

    // Validierungen
    if (empty($name)) {
        $errors[] = "Bitte geben Sie einen Namen ein.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Bitte geben Sie eine gültige E-Mail-Adresse ein.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Das Passwort muss mindestens 8 Zeichen lang sein.";
    }
    if ($password !== $password_confirm) {
        $errors[] = "Die Passwörter stimmen nicht überein.";
    }

    // Überprüfen, ob die E-Mail bereits registriert ist
    $email_check_query = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($email_check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Diese E-Mail-Adresse ist bereits registriert.";
    }
    $stmt->close();

    // Wenn keine Fehler vorhanden sind, Benutzer registrieren
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT); // Passwort verschlüsseln
        $insert_query = "INSERT INTO users (firstname, name, username, email, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssss", $firstname, $name, $username, $email, $password_hash);

        if ($stmt->execute()) {
            // Erfolgreiche Registrierung
            echo "<script>alert('Registrierung erfolgreich! Sie können sich jetzt einloggen.');</script>";
            $redirect_url = isset($_SESSION['referrer']) ? $_SESSION['referrer'] : 'login.php';
            unset($_SESSION['referrer']);
            header("Location: $redirect_url");
            exit();
        } else {
            $errors[] = "Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.";
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
    <title>Registrierung - Fakezon</title>
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


<div class="container my-5">
        <h1 class="text-center my-5">Registrieren Sie sich bei Fakezon</h1>
        <form class="row g-3" method="POST" action="registrierung.php">
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
            <div class="col-md-6">
                <label for="firstname" class="form-label">Vorname:</label>
                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Max" required>
            </div>
            <div class="col-md-6">
                <label for="name" class="form-label">Nachname:</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Mustermann" required>
            </div>
            <div class="col-md-12">
                <label for="email" class="form-label">E-Mail:</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="max.mustermann@beispiel.com" required>
            </div>
            <div class="col-md-12">
                <label for="password" class="form-label">Passwort:</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Passwort" required>
            </div>
            <div class="col-md-12">
                <label for="password_confirm" class="form-label">Passwort bestätigen:</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Passwort bestätigen" required>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Registrieren</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <p>Bereits registriert? <a href="login.php">Hier einloggen</a></p>
        </div>
    </div>

<footer class="container border-top border-dark py-2">
    <p>Alle Rechte vorbehalten &copy; <?php echo date("Y"); ?> Fakezon</p>
</footer>

</body>
</html>
