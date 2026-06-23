<?php
// Récupérer le nombre d'articles dans le panier
$cart_count = getCartCount();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Administration</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS Personnalisé -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<!-- ============================================================
    HEADER ADMIN
============================================================ -->
<header>
    <div class="container">
        <div class="header-wrapper">
            <!-- Logo -->
            <div class="logo">
                <a href="../index.php">
                    <i class="fas fa-hospital-user"></i>
                    <span>Planet <span class="highlight">Dépôts</span></span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="nav-links">
                <a href="../index.php">
                    <i class="fas fa-home"></i>
                    <span>Accueil</span>
                </a>
                <a href="../products.php">
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
                        <a href="../dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Tableau de bord
                        </a>
                        <a href="../profile.php">
                            <i class="fas fa-user-edit"></i> Mon profil
                        </a>
                        <a href="../orders.php">
                            <i class="fas fa-shopping-bag"></i> Mes commandes
                        </a>
                        <a href="../wishlist.php">
                            <i class="fas fa-heart"></i> Liste de souhaits
                        </a>
                        <?php if (isAdmin()): ?>
                        <hr>
                        <a href="index.php" class="text-primary">
                            <i class="fas fa-shield-alt"></i> Administration
                        </a>
                        <?php endif; ?>
                        <hr>
                        <a href="../logout.php" class="text-danger">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <a href="../cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cart_count > 0): ?>
                    <span class="cart-badge"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </div>
</header>

<!-- ============================================================
    MAIN CONTENT
============================================================ -->
<main>
    <div class="admin-wrapper">