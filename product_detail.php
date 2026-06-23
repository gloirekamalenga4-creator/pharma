<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProductById($id);

if (!$product) {
    header('Location: products.php');
    exit();
}

$page_title = $product['name'];
$related_products = getRelatedProducts($id, $product['category_id']);
$reviews = getProductReviews($id);
$rating = getProductRating($id);

include 'includes/header.php';
?>

<div class="container">
    <div class="product-detail">
        <!-- Fil d'Ariane -->
        <nav class="breadcrumb">
            <a href="index.php">Accueil</a>
            <span>›</span>
            <a href="products.php">Produits</a>
            <span>›</span>
            <span><?= htmlspecialchars($product['name']) ?></span>
        </nav>

        <div class="product-detail-grid">
            <!-- Image -->
            <div class="product-image-section">
                <div class="product-main-image">
                    <img src="uploads/products/<?= $product['image_url'] ?: 'placeholder.jpg' ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         id="mainImage">
                </div>
            </div>

            <!-- Informations -->
            <div class="product-info-section">
                <h1><?= htmlspecialchars($product['name']) ?></h1>
                
                <?php if($product['brand']): ?>
                <p class="product-brand">
                    <i class="fas fa-tag"></i> Marque : <?= htmlspecialchars($product['brand']) ?>
                </p>
                <?php endif; ?>

                <!-- Avis -->
                <div class="product-rating">
                    <div class="stars">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= round($rating['average'] ?? 0) ? 'active' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span>(<?= $rating['total'] ?? 0 ?> avis)</span>
                </div>

                <div class="product-price-large">
                    <?= formatPrice($product['price']) ?>
                    <?php if($product['compare_price']): ?>
                    <span class="old-price"><?= formatPrice($product['compare_price']) ?></span>
                    <?php endif; ?>
                </div>

                <?php if($product['prescription_required']): ?>
                <div class="prescription-alert">
                    <i class="fas fa-prescription-bottle"></i>
                    Ce produit nécessite une ordonnance médicale.
                </div>
                <?php endif; ?>

                <div class="product-stock-status">
                    <?php $status = getStockStatus($product['stock'], $product['min_stock']); ?>
                    <span class="stock-<?= $status['class'] ?>">
                        <i class="fas fa-circle"></i> <?= $status['label'] ?>
                    </span>
                </div>

                <div class="product-description">
                    <h4>Description</h4>
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>

                <?php if($product['stock'] > 0): ?>
                <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="quantity-selector">
                        <label>Quantité</label>
                        <div class="quantity-control">
                            <button type="button" onclick="changeQuantity(-1)">-</button>
                            <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" id="quantity">
                            <button type="button" onclick="changeQuantity(1)">+</button>
                        </div>
                        <span class="stock-info">Stock : <?= $product['stock'] ?> unités</span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-cart-plus"></i> Ajouter au panier
                    </button>
                </form>
                <?php else: ?>
                <div class="out-of-stock-alert">
                    <i class="fas fa-times-circle"></i>
                    Produit actuellement en rupture de stock
                </div>
                <?php endif; ?>

                <!-- Actions supplémentaires -->
                <div class="product-extra-actions">
                    <?php if(isLoggedIn()): ?>
                    <button onclick="addToWishlist(<?= $product['id'] ?>)" class="btn btn-outline">
                        <i class="fas fa-heart"></i> Ajouter à ma liste
                    </button>
                    <?php endif; ?>
                    <a href="contact.php?product=<?= $product['id'] ?>" class="btn btn-outline">
                        <i class="fas fa-question-circle"></i> Poser une question
                    </a>
                </div>
            </div>
        </div>

        <!-- Avis clients -->
        <div class="reviews-section">
            <h3>Avis clients</h3>
            
            <?php if(empty($reviews)): ?>
            <p class="no-reviews">Aucun avis pour ce produit pour le moment.</p>
            <?php else: ?>
            <?php foreach($reviews as $review): ?>
            <div class="review-item">
                <div class="review-header">
                    <strong><?= htmlspecialchars($review['full_name']) ?></strong>
                    <div class="review-stars">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= $review['rating'] ? 'active' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="review-date"><?= timeAgo($review['created_at']) ?></span>
                </div>
                <?php if($review['title']): ?>
                <h4><?= htmlspecialchars($review['title']) ?></h4>
                <?php endif; ?>
                <p><?= htmlspecialchars($review['comment']) ?></p>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>

            <?php if(isLoggedIn()): ?>
            <div class="add-review">
                <h4>Donnez votre avis</h4>
                <form method="POST" action="add_review.php">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="form-group">
                        <label>Note</label>
                        <div class="rating-input">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" required>
                            <label for="star<?= $i ?>"><i class="fas fa-star"></i></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Titre</label>
                        <input type="text" name="title" class="form-control" placeholder="Titre de votre avis">
                    </div>
                    <div class="form-group">
                        <label>Commentaire</label>
                        <textarea name="comment" class="form-control" rows="4" placeholder="Votre avis sur ce produit..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Publier mon avis</button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <!-- Produits similaires -->
        <?php if(!empty($related_products)): ?>
        <div class="related-products">
            <h3>Produits similaires</h3>
            <div class="products-grid">
                <?php foreach($related_products as $related): ?>
                <div class="product-card">
                    <a href="product_detail.php?id=<?= $related['id'] ?>">
                        <img src="uploads/products/<?= $related['image_url'] ?: 'placeholder.jpg' ?>" 
                             alt="<?= htmlspecialchars($related['name']) ?>"
                             class="product-image">
                    </a>
                    <div class="product-info">
                        <h4><?= htmlspecialchars($related['name']) ?></h4>
                        <div class="product-price"><?= formatPrice($related['price']) ?></div>
                        <a href="product_detail.php?id=<?= $related['id'] ?>" class="btn btn-sm btn-primary">
                            Voir
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.product-detail {
    padding: 30px 0;
}

.breadcrumb {
    margin-bottom: 30px;
    font-size: 14px;
    color: var(--gray);
}

.breadcrumb a {
    color: var(--primary);
    text-decoration: none;
}

.breadcrumb span {
    margin: 0 8px;
}

.product-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    margin-bottom: 50px;
}

.product-main-image {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: var(--shadow);
    text-align: center;
}

.product-main-image img {
    max-width: 100%;
    max-height: 400px;
    object-fit: contain;
}

.product-info-section h1 {
    font-size: 28px;
    color: var(--dark);
    margin-bottom: 10px;
}

.product-brand {
    color: var(--gray);
    font-size: 14px;
    margin-bottom: 15px;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.stars {
    color: #ddd;
}

.stars .active {
    color: var(--warning);
}

.product-price-large {
    font-size: 32px;
    font-weight: 700;
    color: var(--primary);
    margin: 20px 0;
}

.product-price-large .old-price {
    font-size: 20px;
    color: var(--gray);
    text-decoration: line-through;
    font-weight: 400;
    margin-left: 10px;
}

.prescription-alert {
    background: #fff3cd;
    padding: 15px 20px;
    border-radius: 8px;
    color: #856404;
    margin: 15px 0;
}

.prescription-alert i {
    margin-right: 10px;
}

.product-stock-status {
    margin: 15px 0;
}

.stock-success { color: var(--success); }
.stock-warning { color: var(--warning); }
.stock-danger { color: var(--danger); }

.product-description {
    margin: 20px 0;
}

.product-description h4 {
    margin-bottom: 10px;
}

.add-to-cart-form {
    margin: 20px 0;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.quantity-control {
    display: flex;
    align-items: center;
    border: 2px solid var(--gray-light);
    border-radius: 5px;
    overflow: hidden;
}

.quantity-control button {
    width: 40px;
    height: 40px;
    border: none;
    background: var(--light);
    cursor: pointer;
    font-size: 18px;
    transition: var(--transition);
}

.quantity-control button:hover {
    background: var(--primary);
    color: white;
}

.quantity-control input {
    width: 60px;
    height: 40px;
    border: none;
    text-align: center;
    font-size: 16px;
}

.stock-info {
    color: var(--gray);
    font-size: 14px;
}

.btn-lg {
    padding: 14px 40px;
    font-size: 16px;
}

.out-of-stock-alert {
    background: #f8d7da;
    padding: 15px 20px;
    border-radius: 8px;
    color: #721c24;
    margin: 15px 0;
}

.product-extra-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.btn-outline {
    background: transparent;
    border: 2px solid var(--gray-light);
    color: var(--dark);
}

.btn-outline:hover {
    background: var(--light);
    border-color: var(--primary);
}

.reviews-section {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 1px solid var(--gray-light);
}

.reviews-section h3 {
    font-size: 22px;
    margin-bottom: 20px;
}

.review-item {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: var(--shadow);
    margin-bottom: 15px;
}

.review-header {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

.review-stars {
    color: #ddd;
}

.review-stars .active {
    color: var(--warning);
}

.review-date {
    color: var(--gray);
    font-size: 13px;
}

.no-reviews {
    color: var(--gray);
    padding: 20px 0;
}

.add-review {
    margin-top: 30px;
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: var(--shadow);
}

.rating-input {
    display: flex;
    flex-direction: row-reverse;
    gap: 5px;
}

.rating-input input {
    display: none;
}

.rating-input label {
    font-size: 30px;
    color: #ddd;
    cursor: pointer;
    transition: var(--transition);
}

.rating-input input:checked ~ label,
.rating-input label:hover,
.rating-input label:hover ~ label {
    color: var(--warning);
}

.related-products {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 1px solid var(--gray-light);
}

.related-products h3 {
    font-size: 22px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .product-detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function changeQuantity(delta) {
    const input = document.getElementById('quantity');
    let value = parseInt(input.value) + delta;
    if (value < 1) value = 1;
    if (value > <?= $product['stock'] ?>) value = <?= $product['stock'] ?>;
    input.value = value;
}

function addToWishlist(productId) {
    fetch('ajax/add_to_wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Ajouté à votre liste de souhaits', 'success');
        } else {
            showNotification(data.message || 'Erreur', 'error');
        }
    })
    .catch(() => showNotification('Erreur de connexion', 'error'));
}
</script>

<?php include 'includes/footer.php'; ?>