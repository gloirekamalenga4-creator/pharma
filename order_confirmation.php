<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Confirmation de commande';

if (!isset($_SESSION['order_number'])) {
    header('Location: index.php');
    exit();
}

$order_number = $_SESSION['order_number'];
unset($_SESSION['order_number']);

// Récupérer les détails de la commande
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? AND user_id = ?");
$stmt->execute([$order_number, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit();
}

$order_items = getOrderItems($order['id']);

include 'includes/header.php';
?>

<div class="container">
    <div class="confirmation-page">
        <div class="confirmation-card">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1>Commande confirmée !</h1>
            <p class="confirmation-subtitle">
                Merci pour votre commande. Nous vous remercions de votre confiance.
            </p>
            
            <div class="order-details">
                <div class="order-number">
                    <strong>N° de commande :</strong>
                    <span><?= $order_number ?></span>
                </div>
                <div class="order-date">
                    <strong>Date :</strong>
                    <span><?= formatDate($order['created_at']) ?></span>
                </div>
                <div class="order-total">
                    <strong>Total :</strong>
                    <span><?= formatPrice($order['total_amount']) ?></span>
                </div>
                <div class="order-status">
                    <strong>Statut :</strong>
                    <span class="badge badge-<?= $order['status'] ?>"><?= $order['status'] ?></span>
                </div>
            </div>
            
            <div class="order-items">
                <h3>Articles commandés</h3>
                <?php foreach($order_items as $item): ?>
                <div class="order-item">
                    <span><?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?></span>
                    <span><?= formatPrice($item['price'] * $item['quantity']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="confirmation-actions">
                <a href="orders.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Suivre ma commande
                </a>
                <a href="products.php" class="btn btn-success">
                    <i class="fas fa-shopping-cart"></i> Continuer mes achats
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.confirmation-page {
    padding: 40px 0;
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.confirmation-card {
    background: white;
    padding: 50px;
    border-radius: 15px;
    box-shadow: var(--shadow);
    text-align: center;
    max-width: 600px;
    width: 100%;
}

.confirmation-icon {
    font-size: 80px;
    color: var(--success);
    margin-bottom: 20px;
}

.confirmation-card h1 {
    font-size: 28px;
    color: var(--dark);
    margin-bottom: 10px;
}

.confirmation-subtitle {
    color: var(--gray);
    margin-bottom: 30px;
}

.order-details {
    background: var(--light);
    padding: 20px;
    border-radius: 8px;
    text-align: left;
    margin-bottom: 20px;
}

.order-details div {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--gray-light);
}

.order-details div:last-child {
    border-bottom: none;
}

.order-items {
    text-align: left;
    margin-bottom: 30px;
}

.order-items h3 {
    font-size: 16px;
    margin-bottom: 15px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--gray-light);
    font-size: 14px;
}

.confirmation-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 480px) {
    .confirmation-card {
        padding: 30px 20px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>