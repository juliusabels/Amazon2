<?php
// Session starten
session_start();

// Alle Session-Daten löschen
session_unset();
session_destroy();

// Zurück zur Startseite leiten
header("Location: index.php");
exit();
?>
