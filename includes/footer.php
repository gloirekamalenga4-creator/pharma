</main>

<!-- ============================================================
    FOOTER
============================================================ -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <!-- Colonne 1 : À propos -->
            <div class="footer-col">
                <h4>
                    <i class="fas fa-hospital-user"></i>
                    <?= SITE_NAME ?>
                </h4>
                <p>
                    Votre pharmacie en ligne de confiance. Nous vous accompagnons 
                    dans votre santé avec des produits de qualité et un service 
                    personnalisé.
                </p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <!-- Colonne 2 : Liens rapides -->
            <div class="footer-col">
                <h4>Liens rapides</h4>
                <ul>
                    <li><a href="products.php">Nos produits</a></li>
                    <li><a href="about.php">À propos</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                </ul>
            </div>

            <!-- Colonne 3 : Services -->
            <div class="footer-col">
                <h4>Services</h4>
                <ul>
                    <li><a href="delivery.php">Livraison</a></li>
                    <li><a href="payment.php">Paiement sécurisé</a></li>
                    <li><a href="returns.php">Retours et remboursements</a></li>
                    <li><a href="prescription.php">Ordonnances en ligne</a></li>
                </ul>
            </div>

            <!-- Colonne 4 : Contact -->
            <div class="footer-col">
                <h4>Contact</h4>
                <ul>
                    <li>
                        <i class="fas fa-phone"></i>
                        <a href="tel:<?= SITE_PHONE ?>"><?= SITE_PHONE ?></a>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <?= SITE_ADDRESS ?>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        Lun-Sam: 8h - 20h
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer bottom -->
        <div class="footer-bottom">
            <p>
                &copy; <?= date('Y') ?> <?= SITE_NAME ?> - Tous droits réservés
            </p>
            <div class="payment-icons">
                <i class="fab fa-cc-visa" title="Visa"></i>
                <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                <i class="fab fa-cc-paypal" title="PayPal"></i>
                <i class="fas fa-mobile-alt" title="Mobile Money"></i>
            </div>
        </div>
    </div>
</footer>

<!-- ============================================================
    SCRIPTS
============================================================ -->
<script src="assets/js/main.js"></script>

<!-- Notification de session -->
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