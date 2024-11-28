<?php
session_start(); // Startet die Session

session_unset(); // Löscht alle Session-Daten
session_destroy(); // Zerstört die Session

header("Location: index.php"); // Leitet den Benutzer zur Startseite weiter
exit(); // Beendet das Skript
?>