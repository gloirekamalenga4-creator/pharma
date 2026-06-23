<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Détail de la commande';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = getOrderById($order_id);

if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    header('Location: orders.php');
    exit();
}

$items = getOrderItems($order_id);

include 'includes/header.php';
?>

<div class="container">
    <div class="order-detail-page">
        <div class="order-detail-header">
            <h1>Commande #<?= $order['order_number'] ?></h1>
            <a href="orders.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <div class="order-detail-grid">
            <!-- Informations -->
            <div class="order-info">
                <div class="info-group">
                    <label>Date de commande</label>
                    <span><?= formatDate($order['created_at']) ?></span>
                </div>
                <div class="info-group">
                    <label>Statut</label>
                    <?php $badge = getStatusBadge($order['status']); ?>
                    <span class="badge badge-<?= $badge['class'] ?>"><?= $badge['label'] ?></span>
                </div>
                <div class="info-group">
                    <label>Mode de paiement</label>
                    <span><?= $order['payment_method'] ?></span>
                </div>
                <div class="info-group">
                    <label>Adresse de livraison</label>
                    <span><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></span>
                </div>
                <?php if($order['notes']): ?>
                <div class="info-group">
                    <label>Notes</label>
                    <span><?= nl2br(htmlspecialchars($order['notes'])) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Articles -->
            <div class="order-items-detail">
                <h3>Articles commandés</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= formatPrice($item['price']) ?></td>
                            <td><?= formatPrice($item['price'] * $item['quantity']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Total</strong></td>
                            <td><strong><?= formatPrice($order['total_amount']) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.order-detail-page {
    padding: 40px 0;
}

.order-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.order-detail-header h1 {
    font-size: 24px;
}

.order-detail-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

.order-info {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: var(--shadow);
}

.info-group {
    margin-bottom: 15px;
}

.info-group label {
    display: block;
    font-size: 12px;
    text-transform: uppercase;
    color: var(--gray);
    letter-spacing: 0.5px;
    margin-bottom: 3px;
}

.info-group span {
    font-size: 15px;
    color: var(--dark);
}

.order-items-detail {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: var(--shadow);
}

.order-items-detail h3 {
    font-size: 18px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .order-detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>