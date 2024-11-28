<?php
session_start(); // Startet die Session

// Überprüft, ob der Benutzer eingeloggt ist, andernfalls wird er zur Login-Seite weitergeleitet
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verbindung zur Datenbank herstellen (Datenbank-Details anpassen)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fakezon";

$conn = new mysqli($servername, $username, $password, $dbname); // Erstellt eine neue Verbindung zur Datenbank

// Verbindung überprüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error); // Beendet das Skript und gibt eine Fehlermeldung aus, wenn die Verbindung fehlschlägt
}

// Überprüft, ob das Formular per POST-Methode abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = intval($_POST['product_id']); // Holt die Produkt-ID aus dem POST-Request und konvertiert sie in eine Ganzzahl
    $user_id = $_SESSION['user_id']; // Holt die Benutzer-ID aus der Session
    $rating = intval($_POST['rating']); // Holt die Bewertung aus dem POST-Request und konvertiert sie in eine Ganzzahl
    $comment = $conn->real_escape_string($_POST['comment']); // Holt den Kommentar aus dem POST-Request und sichert ihn gegen SQL-Injection
    $date = date('Y-m-d H:i:s'); // Holt das aktuelle Datum und die Uhrzeit

    // Bereitet die SQL-Abfrage zum Einfügen der Bewertung vor
    $insert_sql = $conn->prepare("INSERT INTO ratings (productId, userId, rating, comment, date) VALUES (?, ?, ?, ?, ?)");
    $insert_sql->bind_param("iiiss", $product_id, $user_id, $rating, $comment, $date); // Bindet die Parameter an die SQL-Abfrage

    // Führt die SQL-Abfrage aus und überprüft, ob sie erfolgreich war
    if ($insert_sql->execute() === TRUE) {
        echo "Bewertung erfolgreich hinzugefügt."; // Gibt eine Erfolgsmeldung aus
    } else {
        echo "Fehler beim Hinzufügen der Bewertung: " . $conn->error; // Gibt eine Fehlermeldung aus
    }

    $insert_sql->close(); // Schließt das vorbereitete Statement
}

$conn->close(); // Schließt die Verbindung zur Datenbank
header("Location: product.php?id=" . $product_id); // Leitet den Benutzer zur Produktseite weiter
exit(); // Beendet das Skript
?>