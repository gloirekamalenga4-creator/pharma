<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = getOrderById($id);

if (!$order) {
    header('Location: orders.php');
    exit();
}

$items = getOrderItems($id);

// Mettre à jour le statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    if (updateOrderStatus($id, $new_status)) {
        $_SESSION['success'] = 'Statut mis à jour avec succès';
        $order = getOrderById($id);
    }
}

$page_title = 'Détail de la commande #' . $order['order_number'];

include 'includes/header.php';
?>

<div class="admin-main">
    <div class="admin-header">
        <h1><i class="fas fa-file-invoice"></i> Commande #<?= $order['order_number'] ?></h1>
        <a href="orders.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="admin-content">
        <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="admin-grid">
            <!-- Informations client -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user"></i> Informations client</h3>
                </div>
                <div class="card-body">
                    <p><strong>Nom :</strong> <?= htmlspecialchars($order['full_name'] ?? 'N/A') ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($order['email'] ?? 'N/A') ?></p>
                    <p><strong>Téléphone :</strong> <?= htmlspecialchars($order['phone'] ?? 'N/A') ?></p>
                    <p><strong>Adresse :</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                </div>
            </div>

            <!-- Informations commande -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle"></i> Informations commande</h3>
                </div>
                <div class="card-body">
                    <p><strong>Date :</strong> <?= formatDate($order['created_at']) ?></p>
                    <p><strong>Paiement :</strong> <?= $order['payment_method'] ?></p>
                    <p><strong>Statut :</strong></p>
                    
                    <form method="POST">
                        <div class="form-row">
                            <select name="status" class="form-control">
                                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>En attente</option>
                                <option value="confirmed" <?= $order['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmée</option>
                                <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Expédiée</option>
                                <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Livrée</option>
                                <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Annulée</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary">
                                <i class="fas fa-sync"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Articles commandés -->
        <div class="card mt-4">
            <div class="card-header">
                <h3><i class="fas fa-boxes"></i> Articles commandés</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
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
</div>

<style>
.admin-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.mt-4 {
    margin-top: 20px;
}

.form-row {
    display: flex;
    gap: 10px;
    align-items: center;
}

@media (max-width: 768px) {
    .admin-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>