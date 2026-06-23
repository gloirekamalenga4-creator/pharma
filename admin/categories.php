<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$page_title = 'Gestion des catégories';
global $pdo;

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$cart_count = getCartCount();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des catégories - Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f9;
        }
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

        .admin-layout { display: flex; min-height: calc(100vh - 70px); }
        .sidebar {
            width: 250px;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            padding: 20px 0;
            flex-shrink: 0;
        }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { margin-bottom: 2px; }
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
        .sidebar-menu li a:hover { background: #f8f9fa; color: #2c7da0; }
        .sidebar-menu li a.active {
            background: #e9ecef;
            color: #2c7da0;
            border-right: 3px solid #2c7da0;
            font-weight: 600;
        }
        .sidebar-menu li a i { width: 20px; text-align: center; }
        .sidebar-menu .divider { height: 1px; background: #e9ecef; margin: 10px 20px; }

        .main-content { flex: 1; padding: 30px; }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .page-header h1 { font-size: 24px; color: #1e2a3a; }
        .page-header h1 i { color: #2c7da0; margin-right: 10px; }

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

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-body { padding: 20px; }
        .table-responsive { overflow-x: auto; }
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
            vertical-align: middle;
        }
        .table tbody tr:hover { background: #f8f9fa; }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-secondary { background: #6c757d; color: white; }
        .text-center { text-align: center; }
        .text-muted { color: #6c757d; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
        }
    </style>
</head>
<body>

<!-- HEADER -->
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

<!-- ADMIN LAYOUT -->
<div class="admin-layout">
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Produits</a></li>
            <li><a href="add_product.php"><i class="fas fa-plus-circle"></i> Ajouter un produit</a></li>
            <li><a href="categories.php" class="active"><i class="fas fa-tags"></i> Catégories</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
            <li class="divider"></li>
            <li><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-tags"></i> Gestion des catégories</h1>
            <a href="add_category.php" class="btn btn-success">
                <i class="fas fa-plus"></i> Ajouter une catégorie
            </a>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
        <div style="background:#d4edda;color:#155724;padding:12px 15px;border-radius:5px;margin-bottom:20px;border:1px solid #c3e6cb;">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
        <div style="background:#f8d7da;color:#721c24;padding:12px 15px;border-radius:5px;margin-bottom:20px;border:1px solid #f5c6cb;">
            <?= $_SESSION['error'] ?>
        </div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Icône</th>
                                <th>Nom</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($categories)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Aucune catégorie trouvée</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($categories as $cat): ?>
                            <tr>
                                <td>
                                    <?php if($cat['icon']): ?>
                                    <i class="fas <?= $cat['icon'] ?> fa-2x" style="color: #2c7da0;"></i>
                                    <?php else: ?>
                                    <i class="fas fa-tag fa-2x" style="color: #6c757d;"></i>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                                <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                                <td><?= htmlspecialchars(substr($cat['description'] ?? '', 0, 50)) ?>...</td>
                                <td>
                                    <?php if($cat['is_active']): ?>
                                    <span class="badge badge-success">Actif</span>
                                    <?php else: ?>
                                    <span class="badge badge-secondary">Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_category.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete_category.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette catégorie ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>