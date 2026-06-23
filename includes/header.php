<?php
$cart_count = getCartCount();
$categories = getCategories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= SITE_NAME ?> - Votre pharmacie en ligne de confiance">
    <meta name="keywords" content="pharmacie, médicaments, parapharmacie, santé, ordonnance">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?><?= SITE_NAME ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS Personnalisé -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>

<!-- ============================================================
    HEADER
============================================================ -->
<header>
    <div class="container">
        <div class="header-wrapper">
            <!-- Logo -->
            <div class="logo">
                <a href="index.php">
                    <i class="fas fa-hospital-user"></i>
                    <span>Planet Dépôts</span>
                </a>
            </div>

            <!-- Barre de recherche -->
            <div class="search-bar">
                <form action="search.php" method="GET">
                    <input type="text" name="q" placeholder="Rechercher un médicament, un produit..." 
                           value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Navigation -->
            <nav class="nav-links">
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <span>Accueil</span>
                </a>
                <a href="products.php">
                    <i class="fas fa-box"></i>
                    <span>Produits</span>
                </a>
                
                <?php if (isLoggedIn()): ?>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        <i class="fas fa-user"></i>
                        <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-menu">
                        <a href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Tableau de bord
                        </a>
                        <a href="profile.php">
                            <i class="fas fa-user-edit"></i> Mon profil
                        </a>
                        <a href="orders.php">
                            <i class="fas fa-shopping-bag"></i> Mes commandes
                        </a>
                        <a href="wishlist.php">
                            <i class="fas fa-heart"></i> Liste de souhaits
                        </a>
                        <?php if (isAdmin()): ?>
                        <hr>
                        <a href="admin/index.php" class="text-primary">
                            <i class="fas fa-shield-alt"></i> Administration
                        </a>
                        <?php endif; ?>
                        <hr>
                        <a href="logout.php" class="text-danger">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <a href="login.php">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Connexion</span>
                </a>
                <a href="register.php" class="btn-register">
                    <i class="fas fa-user-plus"></i>
                    <span>Inscription</span>
                </a>
                <?php endif; ?>
                
                <a href="cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cart_count > 0): ?>
                    <span class="cart-badge"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
            </nav>

            <!-- Bouton mobile -->
            <button class="mobile-toggle" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<!-- ============================================================
    MOBILE MENU
============================================================ -->
<div class="mobile-menu">
    <div class="container">
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Produits</a></li>
            <?php if (isLoggedIn()): ?>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="profile.php"><i class="fas fa-user-edit"></i> Mon profil</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Mes commandes</a></li>
            <li><a href="wishlist.php"><i class="fas fa-heart"></i> Liste de souhaits</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            <?php else: ?>
            <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
            <li><a href="register.php"><i class="fas fa-user-plus"></i> Inscription</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<main>