<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;
    $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
    
    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Produit invalide']);
        exit();
    }
    
    $product = getProductById($product_id);
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
        exit();
    }
    
    if ($product['stock'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Stock insuffisant']);
        exit();
    }
    
    $result = addToCart($product_id, $quantity);
    if ($result) {
        $cart_count = getCartCount();
        echo json_encode([
            'success' => true,
            'message' => 'Produit ajouté au panier',
            'cart_count' => $cart_count
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>