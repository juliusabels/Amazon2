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

// Fehlermeldungen initialisieren
$errors = [];

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    // Validierung
    if (empty($email)) {
        $errors[] = "Bitte geben Sie Ihre E-Mail-Adresse ein.";
    }
    if (empty($password)) {
        $errors[] = "Bitte geben Sie Ihr Passwort ein.";
    }

    // Wenn keine Validierungsfehler vorliegen
    if (empty($errors)) {
        $query = "SELECT id, name, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Benutzer gefunden?
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $user_name, $hashed_password);
            $stmt->fetch();

            // Passwort überprüfen
            if (password_verify($password, $hashed_password)) {
                // Login erfolgreich, Benutzer in der Session speichern
                $_SESSION["user_id"] = $user_id;
                $_SESSION["user_name"] = $user_name;

                // Weiterleitung zur Startseite
                header("Location: index.php");
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
</body>
</html>
