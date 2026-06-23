<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    global $pdo;
    
    // Vérifier si des produits utilisent cette catégorie
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        $_SESSION['error'] = 'Impossible de supprimer : ' . $count . ' produit(s) utilisent cette catégorie';
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Catégorie supprimée avec succès';
    }
}

header('Location: categories.php');
exit();
?>