<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Nos produits';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Filtres
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Récupérer les produits
$products = getProducts($category_id, $limit, $offset);

// Compter le total pour la pagination
global $pdo;
if ($category_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ? AND is_active = 1");
    $stmt->execute([$category_id]);
    $total = $stmt->fetchColumn();
} else {
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1");
    $total = $stmt->fetchColumn();
}

$total_pages = ceil($total / $limit);

// Récupérer les catégories pour le filtre
$categories = getCategories();

include 'includes/header.php';
?>

<div class="container">
    <div class="products-page">
        <!-- Sidebar Filtres -->
        <aside class="products-sidebar">
            <div class="filter-box">
                <h3><i class="fas fa-filter"></i> Filtrer</h3>
                
                <div class="filter-group">
                    <h4>Catégories</h4>
                    <ul>
                        <li>
                            <a href="products.php" class="<?= !$category_id ? 'active' : '' ?>">
                                Tous les produits
                            </a>
                        </li>
                        <?php foreach($categories as $cat): ?>
                        <li>
                            <a href="?category=<?= $cat['id'] ?>" class="<?= $category_id == $cat['id'] ? 'active' : '' ?>">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Liste des produits -->
        <div class="products-content">
            <div class="products-header">
                <h2>Nos produits</h2>
                <p><?= $total ?> produit(s) trouvé(s)</p>
            </div>

            <?php if (empty($products)): ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>Aucun produit trouvé</h3>
                <p>Essayez de modifier vos filtres ou revenez plus tard.</p>
            </div>
            <?php else: ?>
            <div class="products-grid">
                <?php foreach($products as $product): ?>
                <div class="product-card">
                    <?php if($product['is_featured']): ?>
                    <span class="product-badge">En vedette</span>
                    <?php endif; ?>
                    
                    <?php if($product['stock'] <= 0): ?>
                    <span class="product-badge out-of-stock">Rupture</span>
                    <?php endif; ?>
                    
                    <a href="product_detail.php?id=<?= $product['id'] ?>">
                        <img src="uploads/products/<?= $product['image_url'] ?: 'placeholder.jpg' ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             class="product-image">
                    </a>
                    
                    <div class="product-info">
                        <h3>
                            <a href="product_detail.php?id=<?= $product['id'] ?>">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h3>
                        <p class="product-category">
                            <i class="fas fa-tag"></i> <?= htmlspecialchars($product['category_name'] ?? 'Non catégorisé') ?>
                        </p>
                        <div class="product-price">
                            <?= formatPrice($product['price']) ?>
                            <?php if($product['compare_price']): ?>
                            <span class="old-price"><?= formatPrice($product['compare_price']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($product['prescription_required']): ?>
                        <p class="prescription-badge">
                            <i class="fas fa-prescription-bottle"></i> Ordonnance requise
                        </p>
                        <?php endif; ?>
                        
                        <div class="product-actions">
                            <?php if($product['stock'] > 0): ?>
                            <button onclick="addToCart(<?= $product['id'] ?>)" class="btn btn-primary">
                                <i class="fas fa-cart-plus"></i> Ajouter
                            </button>
                            <?php else: ?>
                            <button class="btn btn-secondary" disabled>Rupture</button>
                            <?php endif; ?>
                            
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-outline">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <div class="pagination">
                <?php if($page > 1): ?>
                <a href="?page=<?= $page-1 ?>&category=<?= $category_id ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&category=<?= $category_id ?>" 
                   class="page-link <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if($page < $total_pages): ?>
                <a href="?page=<?= $page+1 ?>&category=<?= $category_id ?>" class="page-link">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.products-page {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 30px;
    padding: 40px 0;
}

.products-sidebar {
    position: sticky;
    top: 100px;
    align-self: start;
}

.filter-box {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: var(--shadow);
}

.filter-box h3 {
    font-size: 18px;
    margin-bottom: 20px;
    color: var(--dark);
}

.filter-box h3 i {
    color: var(--primary);
}

.filter-group {
    margin-bottom: 20px;
}

.filter-group h4 {
    font-size: 14px;
    color: var(--gray);
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.filter-group ul {
    list-style: none;
}

.filter-group ul li {
    margin-bottom: 5px;
}

.filter-group ul li a {
    display: block;
    padding: 8px 12px;
    border-radius: 5px;
    color: var(--dark);
    transition: var(--transition);
    font-size: 14px;
}

.filter-group ul li a:hover {
    background: var(--light);
    color: var(--primary);
}

.filter-group ul li a.active {
    background: var(--primary);
    color: white;
}

.products-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.products-header h2 {
    font-size: 24px;
    color: var(--dark);
}

.products-header p {
    color: var(--gray);
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 30px;
}

.page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    border-radius: 5px;
    background: white;
    color: var(--dark);
    text-decoration: none;
    transition: var(--transition);
    border: 1px solid var(--gray-light);
}

.page-link:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.page-link.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
}

.empty-state i {
    font-size: 64px;
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
}

@media (max-width: 768px) {
    .products-page {
        grid-template-columns: 1fr;
    }
    
    .products-sidebar {
        position: static;
    }
}
</style>

<?php include 'includes/footer.php'; ?>