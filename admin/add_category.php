<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$page_title = 'Ajouter une catégorie';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $slug = !empty($_POST['slug']) ? sanitize($_POST['slug']) : generateSlug($name);
    $description = sanitize($_POST['description']);
    $icon = sanitize($_POST['icon']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($name)) {
        $error = 'Le nom de la catégorie est requis';
    } else {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, icon, is_active) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$name, $slug, $description, $icon, $is_active]);
        
        if ($result) {
            $_SESSION['success'] = 'Catégorie ajoutée avec succès !';
            header('Location: categories.php');
            exit();
        } else {
            $error = 'Erreur lors de l\'ajout';
        }
    }
}

$cart_count = getCartCount();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une catégorie - Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; }
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
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .btn-primary { background: #2c7da0; color: white; }
        .btn-primary:hover { background: #1e4a6f; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-body { padding: 30px; }

        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #1e2a3a;
            margin-bottom: 5px;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #2c7da0;
            outline: none;
            box-shadow: 0 0 0 3px rgba(44,125,160,0.1);
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: 400;
        }
        .checkbox-label input[type="checkbox"] { width: 18px; height: 18px; }
        .form-actions { display: flex; gap: 10px; margin-top: 20px; }
        .text-muted { color: #6c757d; font-size: 12px; }
        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

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
            <h1><i class="fas fa-plus-circle"></i> Ajouter une catégorie</h1>
            <a href="categories.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Nom de la catégorie *</label>
                        <input type="text" name="name" class="form-control" required placeholder="Nom de la catégorie">
                    </div>

                    <div class="form-group">
                        <label>Slug (URL)</label>
                        <input type="text" name="slug" class="form-control" placeholder="ex: medicaments">
                        <small class="text-muted">Laisser vide pour génération automatique</small>
                    </div>

                    <div class="form-group">
                        <label>Icône (FontAwesome)</label>
                        <input type="text" name="icon" class="form-control" placeholder="ex: fa-pills, fa-hand-holding-heart">
                        <small class="text-muted">Voir <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a></small>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Description de la catégorie"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" checked>
                            <span>Actif</span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <a href="categories.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

</body>
</html>