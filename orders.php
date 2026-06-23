<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Mes commandes';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$orders = getOrders($_SESSION['user_id']);

include 'includes/header.php';
?>

<div class="container">
    <div class="orders-page">
        <h1><i class="fas fa-shopping-bag"></i> Mes commandes</h1>

        <?php if(empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-bag"></i>
            <h3>Vous n'avez pas encore passé de commande</h3>
            <p>Découvrez nos produits et faites votre première commande</p>
            <a href="products.php" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-cart"></i> Commencer maintenant
            </a>
        </div>
        <?php else: ?>
        <div class="orders-list">
            <?php foreach($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <span class="order-number">N° <?= $order['order_number'] ?></span>
                        <span class="order-date"><?= formatDate($order['created_at']) ?></span>
                    </div>
                    <?php $badge = getStatusBadge($order['status']); ?>
                    <span class="badge badge-<?= $badge['class'] ?>"><?= $badge['label'] ?></span>
                </div>
                
                <div class="order-body">
                    <div class="order-total">
                        <span>Total :</span>
                        <strong><?= formatPrice($order['total_amount']) ?></strong>
                    </div>
                    <div class="order-payment">
                        <span>Paiement :</span>
                        <span><?= $order['payment_method'] ?></span>
                    </div>
                </div>
                
                <div class="order-footer">
                    <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> Détails
                    </a>
                    <?php if($order['status'] === 'delivered'): ?>
                    <a href="#" class="btn btn-success btn-sm">
                        <i class="fas fa-star"></i> Donner un avis
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.orders-page {
    padding: 40px 0;
}

.orders-page h1 {
    font-size: 28px;
    margin-bottom: 30px;
}

.orders-page h1 i {
    color: var(--primary);
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

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.order-card {
    background: white;
    border-radius: 10px;
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: var(--transition);
}

.order-card:hover {
    box-shadow: var(--shadow-hover);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: var(--light);
    border-bottom: 1px solid var(--gray-light);
}

.order-number {
    font-weight: 600;
    color: var(--dark);
}

.order-date {
    color: var(--gray);
    font-size: 13px;
    margin-left: 15px;
}

.order-body {
    display: flex;
    justify-content: space-between;
    padding: 15px 20px;
    flex-wrap: wrap;
    gap: 10px;
}

.order-total {
    font-size: 16px;
}

.order-total strong {
    color: var(--primary);
}

.order-payment {
    color: var(--gray);
    font-size: 14px;
}

.order-footer {
    padding: 15px 20px;
    border-top: 1px solid var(--gray-light);
    display: flex;
    gap: 10px;
}

@media (max-width: 480px) {
    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .order-body {
        flex-direction: column;
    }
}
</style>

<?php include 'includes/footer.php'; ?>