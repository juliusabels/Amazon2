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
    $product_id = intval($_POST['product_id']);
    $user_id = $_SESSION['user_id'];
    $rating = intval($_POST['rating']);
    $comment = $conn->real_escape_string($_POST['comment']);
    $date = date('Y-m-d H:i:s');

    $insert_sql = $conn->prepare("INSERT INTO ratings (productId, userId, rating, comment, date) VALUES (?, ?, ?, ?, ?)");
    $insert_sql->bind_param("iiiss", $product_id, $user_id, $rating, $comment, $date);

    if ($insert_sql->execute() === TRUE) {
        echo "Bewertung erfolgreich hinzugefügt.";
    } else {
        echo "Fehler beim Hinzufügen der Bewertung: " . $conn->error;
    }

    $insert_sql->close();
}

$conn->close();
header("Location: product.php?id=" . $product_id);
exit();
?>