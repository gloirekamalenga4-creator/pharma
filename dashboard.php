<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Tableau de bord';

if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'dashboard.php';
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Statistiques
global $pdo;
$stmt = $pdo->prepare("SELECT COUNT(*) as total_orders, SUM(total_amount) as total_spent FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_orders = $stmt->fetchAll();

$cart_count = getCartCount();
$wishlist_count = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
$wishlist_count->execute([$user_id]);
$wishlist_count = $wishlist_count->fetchColumn();

include 'includes/header.php';
?>

<div class="container">
    <div class="dashboard-page">
        <div class="dashboard-header">
            <h1>Bonjour, <?= htmlspecialchars($_SESSION['user_name']) ?> !</h1>
            <p>Bienvenue sur votre espace personnel</p>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-shopping-bag"></i>
                <div>
                    <h3><?= $stats['total_orders'] ?? 0 ?></h3>
                    <p>Commandes</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-money-bill-wave"></i>
                <div>
                    <h3><?= formatPrice($stats['total_spent'] ?? 0) ?></h3>
                    <p>Dépensé</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <div>
                    <h3><?= $cart_count ?></h3>
                    <p>Panier</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-heart"></i>
                <div>
                    <h3><?= $wishlist_count ?></h3>
                    <p>Liste de souhaits</p>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Dernières commandes -->
            <div class="dashboard-card">
                <h3><i class="fas fa-clock"></i> Dernières commandes</h3>
                <?php if(empty($recent_orders)): ?>
                <p class="text-muted">Aucune commande pour le moment</p>
                <a href="products.php" class="btn btn-primary">Commander maintenant</a>
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
                            <?php foreach($recent_orders as $order): ?>
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
                <a href="orders.php" class="btn btn-sm btn-secondary">Voir toutes mes commandes</a>
                <?php endif; ?>
            </div>

            <!-- Actions rapides -->
            <div class="dashboard-card">
                <h3><i class="fas fa-bolt"></i> Actions rapides</h3>
                <div class="quick-actions">
                    <a href="products.php" class="quick-action">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Commander</span>
                    </a>
                    <a href="profile.php" class="quick-action">
                        <i class="fas fa-user-edit"></i>
                        <span>Mon profil</span>
                    </a>
                    <a href="wishlist.php" class="quick-action">
                        <i class="fas fa-heart"></i>
                        <span>Liste de souhaits</span>
                    </a>
                    <?php if(isAdmin()): ?>
                    <a href="admin/index.php" class="quick-action">
                        <i class="fas fa-shield-alt"></i>
                        <span>Administration</span>
                    </a>
                    <?php endif; ?>
                    <a href="logout.php" class="quick-action">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-page {
    padding: 40px 0;
}

.dashboard-header {
    margin-bottom: 30px;
}

.dashboard-header h1 {
    font-size: 28px;
    color: var(--dark);
}

.dashboard-header p {
    color: var(--gray);
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-card i {
    font-size: 30px;
    color: var(--primary);
}

.stat-card h3 {
    font-size: 24px;
    margin: 0;
    color: var(--dark);
}

.stat-card p {
    margin: 0;
    color: var(--gray);
    font-size: 14px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.dashboard-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: var(--shadow);
}

.dashboard-card h3 {
    font-size: 18px;
    margin-bottom: 20px;
    color: var(--dark);
}

.dashboard-card h3 i {
    color: var(--primary);
    margin-right: 8px;
}

.quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.quick-action {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    border-radius: 8px;
    background: var(--light);
    color: var(--dark);
    text-decoration: none;
    transition: var(--transition);
}

.quick-action:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
}

.quick-action i {
    font-size: 20px;
}

.text-muted {
    color: var(--gray);
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>