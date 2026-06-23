<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Mon panier';
$cart_items = getCartItems();
$total = getCartTotal();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            if ($quantity <= 0) {
                removeFromCart($product_id);
            } else {
                updateCartQuantity($product_id, $quantity);
            }
        }
        $_SESSION['success'] = 'Panier mis à jour';
        header('Location: cart.php');
        exit();
    }
    
    if (isset($_POST['remove'])) {
        removeFromCart($_POST['product_id']);
        $_SESSION['success'] = 'Produit retiré du panier';
        header('Location: cart.php');
        exit();
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="cart-page">
        <h1><i class="fas fa-shopping-cart"></i> Mon panier</h1>

        <?php if(empty($cart_items)): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-basket"></i>
            <h2>Votre panier est vide</h2>
            <p>Découvrez nos produits et commencez vos achats</p>
            <a href="products.php" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag"></i> Continuer mes achats
            </a>
        </div>
        <?php else: ?>
        <form method="POST" class="cart-form">
            <div class="cart-grid">
                <!-- Liste des articles -->
                <div class="cart-items">
                    <?php foreach($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="uploads/products/<?= $item['image_url'] ?: 'placeholder.jpg' ?>" 
                                 alt="<?= htmlspecialchars($item['name']) ?>">
                        </div>
                        
                        <div class="cart-item-info">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="cart-item-price"><?= formatPrice($item['price']) ?></p>
                        </div>
                        
                        <div class="cart-item-quantity">
                            <input type="number" name="quantity[<?= $item['product_id'] ?>]" 
                                   value="<?= $item['quantity'] ?>" min="0" max="99">
                        </div>
                        
                        <div class="cart-item-total">
                            <?= formatPrice($item['price'] * $item['quantity']) ?>
                        </div>
                        
                        <div class="cart-item-actions">
                            <button type="submit" name="remove" value="1" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Résumé -->
                <div class="cart-summary">
                    <h3>Résumé de la commande</h3>
                    
                    <div class="summary-row">
                        <span>Sous-total</span>
                        <span><?= formatPrice(getCartSubtotal()) ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Livraison</span>
                        <span><?= formatPrice(calculateShipping(getCartSubtotal())) ?></span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total</span>
                        <span><?= formatPrice($total + calculateShipping(getCartSubtotal())) ?></span>
                    </div>
                    
                    <div class="cart-actions">
                        <button type="submit" name="update" class="btn btn-secondary">
                            <i class="fas fa-sync-alt"></i> Mettre à jour
                        </button>
                        <a href="checkout.php" class="btn btn-success btn-lg">
                            <i class="fas fa-check"></i> Passer à la caisse
                        </a>
                    </div>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<style>
.cart-page {
    padding: 40px 0;
}

.cart-page h1 {
    font-size: 28px;
    margin-bottom: 30px;
    color: var(--dark);
}

.cart-page h1 i {
    color: var(--primary);
}

.empty-cart {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 10px;
    box-shadow: var(--shadow);
}

.empty-cart i {
    font-size: 80px;
    color: var(--gray-light);
    margin-bottom: 20px;
}

.empty-cart h2 {
    font-size: 24px;
    color: var(--dark);
    margin-bottom: 10px;
}

.empty-cart p {
    color: var(--gray);
    margin-bottom: 30px;
}

.cart-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 30px;
}

.cart-items {
    background: white;
    border-radius: 10px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.cart-item {
    display: grid;
    grid-template-columns: 100px 1fr auto auto auto;
    gap: 15px;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid var(--gray-light);
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item-image img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
}

.cart-item-info h3 {
    font-size: 16px;
    color: var(--dark);
    margin-bottom: 5px;
}

.cart-item-price {
    color: var(--gray);
    font-size: 14px;
}

.cart-item-quantity input {
    width: 60px;
    padding: 8px;
    text-align: center;
    border: 2px solid var(--gray-light);
    border-radius: 5px;
    font-size: 14px;
}

.cart-item-total {
    font-weight: 600;
    color: var(--primary);
    font-size: 16px;
}

.cart-summary {
    background: white;
    border-radius: 10px;
    box-shadow: var(--shadow);
    padding: 25px;
    position: sticky;
    top: 100px;
}

.cart-summary h3 {
    font-size: 18px;
    margin-bottom: 20px;
    color: var(--dark);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid var(--gray-light);
    font-size: 14px;
}

.summary-row.total {
    border-bottom: none;
    font-size: 18px;
    font-weight: 700;
    padding-top: 15px;
    color: var(--dark);
}

.cart-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
}

.cart-actions .btn {
    width: 100%;
    justify-content: center;
}

@media (max-width: 992px) {
    .cart-grid {
        grid-template-columns: 1fr;
    }
    
    .cart-item {
        grid-template-columns: 70px 1fr;
        grid-template-rows: auto auto auto;
    }
    
    .cart-item-image {
        grid-row: span 3;
    }
    
    .cart-item-actions {
        grid-column: 2;
    }
}

@media (max-width: 480px) {
    .cart-item {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .cart-item-image {
        grid-row: 1;
        justify-self: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>