<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProductById($id);

if (!$product) {
    header('Location: products.php');
    exit();
}

$page_title = 'Modifier le produit';
$categories = getCategories();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $slug = !empty($_POST['slug']) ? sanitize($_POST['slug']) : generateSlug($name);
    $description = sanitize($_POST['description']);
    $short_description = sanitize($_POST['short_description']);
    $price = (float)$_POST['price'];
    $compare_price = !empty($_POST['compare_price']) ? (float)$_POST['compare_price'] : null;
    $stock = (int)$_POST['stock'];
    $min_stock = (int)$_POST['min_stock'];
    $category_id = (int)$_POST['category_id'];
    $brand = sanitize($_POST['brand']);
    $prescription_required = isset($_POST['prescription_required']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($name)) {
        $error = 'Le nom du produit est requis';
    } elseif ($price <= 0) {
        $error = 'Le prix doit être supérieur à 0';
    } elseif ($stock < 0) {
        $error = 'Le stock ne peut pas être négatif';
    } else {
        $image_url = $product['image_url'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($ext, $allowed)) {
                $filename = uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                    if ($image_url && file_exists($upload_dir . $image_url)) {
                        unlink($upload_dir . $image_url);
                    }
                    $image_url = $filename;
                } else {
                    $error = 'Erreur lors de l\'upload';
                }
            } else {
                $error = 'Format d\'image non autorisé';
            }
        }
        
        if (empty($error)) {
            global $pdo;
            $stmt = $pdo->prepare("
                UPDATE products SET 
                    name = ?, slug = ?, description = ?, short_description = ?, 
                    price = ?, compare_price = ?, stock = ?, min_stock = ?, 
                    category_id = ?, brand = ?, image_url = ?, 
                    prescription_required = ?, is_featured = ?, is_active = ?
                WHERE id = ?
            ");
            $result = $stmt->execute([
                $name, $slug, $description, $short_description,
                $price, $compare_price, $stock, $min_stock,
                $category_id, $brand, $image_url,
                $prescription_required, $is_featured, $is_active,
                $id
            ]);
            if ($result) {
                $_SESSION['success'] = 'Produit modifié avec succès !';
                header('Location: products.php');
                exit();
            } else {
                $error = 'Erreur lors de la modification';
            }
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
    <title>Modifier le produit - Administration</title>
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
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
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
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .current-image { margin-bottom: 10px; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .row { grid-template-columns: 1fr; }
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
            <li><a href="categories.php"><i class="fas fa-tags"></i> Catégories</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
            <li class="divider"></li>
            <li><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-edit"></i> Modifier le produit</h1>
            <a href="products.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="form-group">
                            <label>Nom du produit *</label>
                            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($product['name']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Slug (URL)</label>
                            <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($product['slug']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label>Prix (FCFA) *</label>
                            <input type="number" name="price" class="form-control" required step="0.01" value="<?= $product['price'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Prix comparatif</label>
                            <input type="number" name="compare_price" class="form-control" step="0.01" value="<?= $product['compare_price'] ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label>Stock *</label>
                            <input type="number" name="stock" class="form-control" required min="0" value="<?= $product['stock'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Stock minimum</label>
                            <input type="number" name="min_stock" class="form-control" min="0" value="<?= $product['min_stock'] ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label>Catégorie</label>
                            <select name="category_id" class="form-control">
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Marque</label>
                            <input type="text" name="brand" class="form-control" value="<?= htmlspecialchars($product['brand']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description courte</label>
                        <input type="text" name="short_description" class="form-control" value="<?= htmlspecialchars($product['short_description']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Description complète</label>
                        <textarea name="description" class="form-control" rows="6"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Image du produit</label>
                        <?php if($product['image_url']): ?>
                        <div class="current-image">
                            <img src="../uploads/products/<?= $product['image_url'] ?>" style="max-width:150px; border-radius:5px;">
                            <p><small class="text-muted">Image actuelle</small></p>
                        </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Laissez vide pour conserver l'image actuelle</small>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="prescription_required" <?= $product['prescription_required'] ? 'checked' : '' ?>>
                                <span>Ordonnance requise</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_featured" <?= $product['is_featured'] ? 'checked' : '' ?>>
                                <span>Produit en vedette</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_active" <?= $product['is_active'] ? 'checked' : '' ?>>
                                <span>Actif</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <a href="products.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

</body>
</html>