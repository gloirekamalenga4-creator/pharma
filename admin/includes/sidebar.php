<div class="admin-sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-hospital-user"></i>
        <span>Admin</span>
    </div>
    <nav class="sidebar-nav">
        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="products.php" class="<?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">
            <i class="fas fa-box"></i> Produits
        </a>
        <a href="add_product.php" class="<?= basename($_SERVER['PHP_SELF']) == 'add_product.php' ? 'active' : '' ?>">
            <i class="fas fa-plus-circle"></i> Ajouter produit
        </a>
        <a href="categories.php" class="<?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">
            <i class="fas fa-tags"></i> Catégories
        </a>
        <a href="orders.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">
            <i class="fas fa-shopping-cart"></i> Commandes
        </a>
        <a href="users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Utilisateurs
        </a>
        <a href="statistics.php" class="<?= basename($_SERVER['PHP_SELF']) == 'statistics.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i> Statistiques
        </a>
        <hr>
        <a href="settings.php">
            <i class="fas fa-cog"></i> Paramètres
        </a>
        <a href="../index.php" target="_blank">
            <i class="fas fa-globe"></i> Voir le site
        </a>
        <a href="logout.php" class="text-danger">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </nav>
</div>