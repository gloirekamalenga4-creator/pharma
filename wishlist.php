<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Ma liste de souhaits';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$wishlist = getWishlist($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
    removeFromWishlist($_SESSION['user_id'], $_POST['product_id']);
    $_SESSION['success'] = 'Produit retiré de votre liste';
    header('Location: wishlist.php');
    exit();
}

include 'includes/header.php';
?>

<div class="container">
    <div class="wishlist-page">
        <h1><i class="fas fa-heart"></i> Ma liste de souhaits</h1>

        <?php if(empty($wishlist)): ?>
        <div class="empty-state">
            <i class="fas fa-heart" style="color: #ff6b6b;"></i>
            <h3>Votre liste de souhaits est vide</h3>
            <p>Ajoutez vos produits préférés pour les retrouver facilement</p>
            <a href="products.php" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag"></i> Découvrir les produits
            </a>
        </div>
        <?php else: ?>
        <div class="products-grid">
            <?php foreach($wishlist as $item): ?>
            <div class="product-card">
                <div class="product-wishlist-remove">
                    <form method="POST">
                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                        <button type="submit" name="remove" class="btn-remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </div>
                
                <a href="product_detail.php?id=<?= $item['product_id'] ?>">
                    <img src="uploads/products/<?= $item['image_url'] ?: 'placeholder.jpg' ?>" 
                         alt="<?= htmlspecialchars($item['name']) ?>"
                         class="product-image">
                </a>
                
                <div class="product-info">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <div class="product-price"><?= formatPrice($item['price']) ?></div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=<?= $item['product_id'] ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        <button onclick="addToCart(<?= $item['product_id'] ?>)" class="btn btn-success">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.wishlist-page {
    padding: 40px 0;
}

.wishlist-page h1 {
    font-size: 28px;
    margin-bottom: 30px;
}

.wishlist-page h1 i {
    color: #ff6b6b;
}

.product-wishlist-remove {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1;
}

.btn-remove {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: var(--shadow);
}

.btn-remove:hover {
    background: var(--danger);
    color: white;
}

.product-card {
    position: relative;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 10px;
    box-shadow: var(--shadow);
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 24px;
    color: var(--dark);
    margin-bottom: 10px;
}

.empty-state p {
    color: var(--gray);
    margin-bottom: 30px;
}
</style>

<?php include 'includes/footer.php'; ?>