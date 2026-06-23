<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = getUserById($id);

if (!$user || $user['role'] === 'admin') {
    header('Location: users.php');
    exit();
}

$page_title = 'Détail utilisateur - ' . $user['full_name'];
global $pdo;

// Statistiques
$stmt = $pdo->prepare("SELECT COUNT(*) as total_orders, SUM(total_amount) as total_spent FROM orders WHERE user_id = ?");
$stmt->execute([$id]);
$stats = $stmt->fetch();

// Commandes
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$id]);
$orders = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="admin-main">
    <div class="admin-header">
        <h1><i class="fas fa-user-circle"></i> <?= htmlspecialchars($user['full_name']) ?></h1>
        <a href="users.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="admin-content">
        <div class="admin-grid">
            <!-- Informations -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle"></i> Informations personnelles</h3>
                </div>
                <div class="card-body">
                    <p><strong>Nom d'utilisateur :</strong> <?= htmlspecialchars($user['username']) ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Téléphone :</strong> <?= htmlspecialchars($user['phone'] ?? 'Non renseigné') ?></p>
                    <p><strong>Adresse :</strong> <?= nl2br(htmlspecialchars($user['address'] ?? 'Non renseignée')) ?></p>
                    <p><strong>Membre depuis :</strong> <?= formatDate($user['created_at']) ?></p>
                    <p><strong>Statut :</strong> 
                        <span class="badge badge-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>">
                            <?= $user['status'] ?>
                        </span>
                    </p>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-bar"></i> Statistiques</h3>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <h3><?= $stats['total_orders'] ?? 0 ?></h3>
                            <p>Commandes</p>
                        </div>
                        <div class="stat-item">
                            <h3><?= formatPrice($stats['total_spent'] ?? 0) ?></h3>
                            <p>Dépensé</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des commandes -->
        <div class="card mt-4">
            <div class="card-header">
                <h3><i class="fas fa-shopping-bag"></i> Historique des commandes</h3>
            </div>
            <div class="card-body">
                <?php if(empty($orders)): ?>
                <p class="text-muted">Aucune commande passée</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>N° commande</th>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td><?= $order['order_number'] ?></td>
                                <td><?= formatDateShort($order['created_at']) ?></td>
                                <td><?= formatPrice($order['total_amount']) ?></td>
                                <td>
                                    <?php $badge = getStatusBadge($order['status']); ?>
                                    <span class="badge badge-<?= $badge['class'] ?>"><?= $badge['label'] ?></span>
                                </td>
                                <td>
                                    <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.stat-item {
    text-align: center;
    padding: 15px;
    background: var(--light);
    border-radius: 8px;
}

.stat-item h3 {
    font-size: 24px;
    color: var(--primary);
    margin: 0;
}

.stat-item p {
    margin: 5px 0 0;
    color: var(--gray);
}

.mt-4 {
    margin-top: 20px;
}
</style>

<?php include 'includes/footer.php'; ?>