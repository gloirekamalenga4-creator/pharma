<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Résultats de recherche';
$query = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$products = [];

if (!empty($query)) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.name LIKE ? OR p.description LIKE ? 
        AND p.is_active = 1 
        ORDER BY p.created_at DESC
    ");
    $search_term = "%$query%";
    $stmt->execute([$search_term, $search_term]);
    $products = $stmt->fetchAll();
}

include 'includes/header.php';
?>

<div class="container">
    <div class="search-page">
        <h1><i class="fas fa-search"></i> Recherche</h1>
        
        <form method="GET" class="search-form">
            <input type="text" name="q" placeholder="Rechercher un produit..." 
                   value="<?= htmlspecialchars($query) ?>" required>
            <button type="submit"><i class="fas fa-search"></i> Rechercher</button>
        </form>

        <?php if (!empty($query)): ?>
        <p class="search-results-count">
            <?= count($products) ?> résultat(s) pour "<strong><?= htmlspecialchars($query) ?></strong>"
        </p>

        <?php if(empty($products)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>Aucun résultat trouvé</h3>
            <p>Essayez avec d'autres mots-clés ou parcourez nos catégories</p>
            <a href="products.php" class="btn btn-primary">
                <i class="fas fa-box"></i> Voir tous les produits
            </a>
        </div>
        <?php else: ?>
        <div class="products-grid">
            <?php foreach($products as $product): ?>
            <div class="product-card">
                <a href="product_detail.php?id=<?= $product['id'] ?>">
                    <img src="uploads/products/<?= $product['image_url'] ?: 'placeholder.jpg' ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         class="product-image">
                </a>
                <div class="product-info">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="product-category"><?= htmlspecialchars($product['category_name'] ?? 'Non catégorisé') ?></p>
                    <div class="product-price"><?= formatPrice($product['price']) ?></div>
                    <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">
                        Voir détails
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.search-page {
    padding: 40px 0;
}

.search-page h1 {
    font-size: 28px;
    margin-bottom: 30px;
}

.search-page h1 i {
    color: var(--primary);
}

.search-form {
    display: flex;
    gap: 10px;
    max-width: 600px;
    margin-bottom: 30px;
}

.search-form input {
    flex: 1;
    padding: 14px 20px;
    border: 2px solid var(--gray-light);
    border-radius: 8px;
    font-size: 16px;
}

.search-form input:focus {
    border-color: var(--primary);
    outline: none;
}

.search-form button {
    padding: 14px 30px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: var(--transition);
}

.search-form button:hover {
    background: var(--primary-dark);
}

.search-results-count {
    color: var(--gray);
    margin-bottom: 20px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 48px;
    color: var(--gray-light);
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