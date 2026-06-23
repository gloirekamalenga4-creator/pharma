<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$page_title = 'Tableau de bord';
global $pdo;

// Statistiques
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled'")->fetchColumn();

// Commandes récentes
$recent_orders = $pdo->query("
    SELECT o.*, u.full_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 10
")->fetchAll();

// Produits en rupture
$low_stock = $pdo->query("
    SELECT * FROM products 
    WHERE stock <= min_stock AND is_active = 1 
    ORDER BY stock ASC 
    LIMIT 10
")->fetchAll();

$cart_count = getCartCount();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Planet Dépôts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f9;
        }
        
        /* ============================================================
           HEADER
        ============================================================ */
        header {
            background: linear-gradient(135deg, #1e4a6f, #2c7da0);
            color: white;
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .header-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .logo a {
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
        }
        .logo i { font-size: 1.8rem; color: #e9c46a; }
        .logo .highlight { color: #e9c46a; }
        
        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }
        .nav-links a:hover { color: #e9c46a; }
        
        .cart-link { position: relative; font-size: 1.2rem; }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -12px;
            background: #e63946;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            min-width: 20px;
            text-align: center;
        }
        
        .dropdown { position: relative; }
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            color: #1e2a3a;
            min-width: 200px;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
            padding: 8px 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 100;
        }
        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .dropdown-menu a {
            color: #1e2a3a;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .dropdown-menu a:hover { background: #f8f9fa; color: #2c7da0; }
        .dropdown-menu hr { border: none; border-top: 1px solid #e9ecef; margin: 5px 0; }
        .text-danger { color: #e63946 !important; }
        .text-primary { color: #2c7da0 !important; }

        /* ============================================================
           SIDEBAR
        ============================================================ */
        .admin-layout {
            display: flex;
            min-height: calc(100vh - 70px);
        }
        
        .sidebar {
            width: 250px;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            padding: 20px 0;
            flex-shrink: 0;
        }
        .sidebar-menu {
            list-style: none;
        }
        .sidebar-menu li {
            margin-bottom: 2px;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 25px;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        .sidebar-menu li a:hover {
            background: #f8f9fa;
            color: #2c7da0;
        }
        .sidebar-menu li a.active {
            background: #e9ecef;
            color: #2c7da0;
            border-right: 3px solid #2c7da0;
            font-weight: 600;
        }
        .sidebar-menu li a i {
            width: 20px;
            text-align: center;
        }
        .sidebar-menu .divider {
            height: 1px;
            background: #e9ecef;
            margin: 10px 20px;
        }

        /* ============================================================
           MAIN CONTENT
        ============================================================ */
        .main-content {
            flex: 1;
            padding: 30px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .page-header h1 {
            font-size: 24px;
            color: #1e2a3a;
        }
        .page-header h1 i {
            color: #2c7da0;
            margin-right: 10px;
        }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }
        .stat-info h3 { font-size: 24px; color: #1e2a3a; }
        .stat-info p { color: #6c757d; font-size: 14px; margin: 0; }

        /* Cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header h3 { font-size: 16px; color: #1e2a3a; }
        .card-header h3 i { color: #2c7da0; margin-right: 8px; }
        .card-body { padding: 20px; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .btn-sm { padding: 5px 12px; font-size: 12px; }
        .btn-primary { background: #2c7da0; color: white; }
        .btn-primary:hover { background: #1e4a6f; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #e63946; color: white; }
        .btn-danger:hover { opacity: 0.9; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-warning:hover { background: #e0a800; }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #000; }
        .badge-danger { background: #e63946; color: white; }
        .badge-info { background: #17a2b8; color: white; }
        .badge-secondary { background: #6c757d; color: white; }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .table thead th {
            background: #f8f9fa;
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
        }
        .table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #e9ecef;
        }
        .table tbody tr:hover { background: #f8f9fa; }
        .table-responsive { overflow-x: auto; }
        .text-muted { color: #6c757d; }
        .text-center { text-align: center; }

        .stock-list {
            list-style: none;
        }
        .stock-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
        }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .dashboard-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ============================================================
    HEADER
============================================================ -->
<header>
    <div class="container">
        <div class="header-wrapper">
            <div class="logo">
                <a href="../index.php">
                    <i class="fas fa-hospital-user"></i>
                    <span>Planet <span class="highlight">Dépôts</span></span>
                </a>
            </div>
            <nav class="nav-links">
                <a href="../index.php"><i class="fas fa-home"></i> Accueil</a>
                <a href="../products.php"><i class="fas fa-box"></i> Produits</a>
                
                <div class="dropdown">
                    <a href="#"><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_name']) ?> <i class="fas fa-chevron-down"></i></a>
                    <div class="dropdown-menu">
                        <a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                        <a href="../profile.php"><i class="fas fa-user-edit"></i> Mon profil</a>
                        <a href="../orders.php"><i class="fas fa-shopping-bag"></i> Mes commandes</a>
                        <a href="../wishlist.php"><i class="fas fa-heart"></i> Liste de souhaits</a>
                        <hr>
                        <a href="index.php" class="text-primary"><i class="fas fa-shield-alt"></i> Administration</a>
                        <hr>
                        <a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
                
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
    ADMIN LAYOUT
============================================================ -->
<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Produits</a></li>
            <li><a href="add_product.php"><i class="fas fa-plus-circle"></i> Ajouter un produit</a></li>
            <li><a href="categories.php"><i class="fas fa-tags"></i> Catégories</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
            <li><a href="statistics.php"><i class="fas fa-chart-line"></i> Statistiques</a></li>
            <li class="divider"></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Paramètres</a></li>
            <li><a href="../index.php"><i class="fas fa-globe"></i> Voir le site</a></li>
            <li><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-tachometer-alt"></i> Tableau de bord</h1>
            <div>
                <span style="color: #6c757d; font-size: 14px;">
                    <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                </span>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #2c7da0;"><i class="fas fa-box"></i></div>
                <div class="stat-info">
                    <h3><?= $total_products ?></h3>
                    <p>Produits</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #28a745;"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-info">
                    <h3><?= $total_orders ?></h3>
                    <p>Commandes</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #ffc107;"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3><?= $total_users ?></h3>
                    <p>Utilisateurs</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #e63946;"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-info">
                    <h3><?= formatPrice($total_revenue ?: 0) ?></h3>
                    <p>Chiffre d'affaires</p>
                </div>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-clock"></i> Dernières commandes</h3>
                    <a href="orders.php" class="btn btn-sm btn-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    <?php if(empty($recent_orders)): ?>
                    <p class="text-muted">Aucune commande</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Client</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_orders as $order): ?>
                                <tr>
                                    <td><?= $order['order_number'] ?></td>
                                    <td><?= htmlspecialchars($order['full_name'] ?? 'N/A') ?></td>
                                    <td><?= formatPrice($order['total_amount']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $order['status'] ?>">
                                            <?= $order['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-exclamation-triangle"></i> Stock critique</h3>
                    <a href="products.php" class="btn btn-sm btn-primary">Gérer</a>
                </div>
                <div class="card-body">
                    <?php if(empty($low_stock)): ?>
                    <p style="color: #28a745;">✅ Tous les produits sont en stock</p>
                    <?php else: ?>
                    <ul class="stock-list">
                        <?php foreach($low_stock as $product): ?>
                        <li>
                            <span><?= htmlspecialchars($product['name']) ?></span>
                            <span class="badge badge-danger">Stock: <?= $product['stock'] ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>