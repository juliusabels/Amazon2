- Nutzerprofile + Login + Registrierung

Julius:
- Kommentarfunktion

Tom:
- Warenkorb icon
- kommentieren
- code sortieren




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