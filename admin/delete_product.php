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
    
    // Récupérer l'image
    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if ($product) {
        // Supprimer l'image
        if ($product['image_url'] && file_exists('../uploads/products/' . $product['image_url'])) {
            unlink('../uploads/products/' . $product['image_url']);
        }
        
        // Supprimer le produit
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = 'Produit supprimé avec succès';
    }
}

header('Location: products.php');
exit();
?>