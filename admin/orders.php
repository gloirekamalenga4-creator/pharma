<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$page_title = 'Gestion des commandes';
global $pdo;

// Filtres
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Requête
$sql = "SELECT o.*, u.full_name, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id";
$conditions = [];
$params = [];

if ($status) {
    $conditions[] = "o.status = ?";
    $params[] = $status;
}

if ($search) {
    $conditions[] = "(o.order_number LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY o.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="admin-main">
    <div class="admin-header">
        <h1><i class="fas fa-shopping-cart"></i> Gestion des commandes</h1>
    </div>

    <div class="admin-content">
        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="search-form">
                    <select name="status" class="form-control" style="width:auto;">
                        <option value="">Tous les statuts</option>
                        <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>En attente</option>
                        <option value="confirmed" <?= $status == 'confirmed' ? 'selected' : '' ?>>Confirmée</option>
                        <option value="shipped" <?= $status == 'shipped' ? 'selected' : '' ?>>Expédiée</option>
                        <option value="delivered" <?= $status == 'delivered' ? 'selected' : '' ?>>Livrée</option>
                        <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Annulée</option>
                    </select>
                    
                    <input type="text" name="search" placeholder="Rechercher..." 
                           value="<?= htmlspecialchars($search) ?>" style="flex:1;">
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </form>
            </div>
        </div>

        <!-- Liste des commandes -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>N° commande</th>
                                <th>Client</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Aucune commande trouvée</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td><strong><?= $order['order_number'] ?></strong></td>
                                <td>
                                    <?= htmlspecialchars($order['full_name'] ?? 'N/A') ?><br>
                                    <small><?= htmlspecialchars($order['email'] ?? '') ?></small>
                                </td>
                                <td><?= formatPrice($order['total_amount']) ?></td>
                                <td>
                                    <?php $badge = getStatusBadge($order['status']); ?>
                                    <span class="badge badge-<?= $badge['class'] ?>"><?= $badge['label'] ?></span>
                                </td>
                                <td><?= formatDate($order['created_at']) ?></td>
                                <td>
                                    <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.search-form {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.search-form .form-control {
    padding: 10px 15px;
    border: 2px solid var(--gray-light);
    border-radius: var(--radius);
}
</style>

<?php include 'includes/footer.php'; ?>