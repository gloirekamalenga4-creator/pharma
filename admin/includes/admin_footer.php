</div>
</main>

<!-- ============================================================
    FOOTER
============================================================ -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <!-- Colonne 1 -->
            <div class="footer-col">
                <h4><i class="fas fa-hospital-user"></i> <?= SITE_NAME ?></h4>
                <p>Votre pharmacie en ligne de confiance. Nous vous accompagnons dans votre santé.</p>
            </div>

            <!-- Colonne 2 -->
            <div class="footer-col">
                <h4>Liens rapides</h4>
                <ul>
                    <li><a href="../products.php">Nos produits</a></li>
                    <li><a href="../about.php">À propos</a></li>
                    <li><a href="../contact.php">Contact</a></li>
                </ul>
            </div>

            <!-- Colonne 3 -->
            <div class="footer-col">
                <h4>Contact</h4>
                <ul>
                    <li><i class="fas fa-phone"></i> <?= SITE_PHONE ?></li>
                    <li><i class="fas fa-envelope"></i> <?= SITE_EMAIL ?></li>
                    <li><i class="fas fa-map-marker-alt"></i> <?= SITE_ADDRESS ?></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?> - Tous droits réservés</p>
            <div class="payment-icons">
                <i class="fab fa-cc-visa" title="Visa"></i>
                <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                <i class="fas fa-mobile-alt" title="Mobile Money"></i>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="../assets/js/main.js"></script>

<!-- Notifications -->
<?php if (isset($_SESSION['success'])): ?>
<script>
    showNotification('<?= $_SESSION['success'] ?>', 'success');
</script>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<script>
    showNotification('<?= $_SESSION['error'] ?>', 'error');
</script>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>

</body>
</html>