<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$page_title = 'Gestion des utilisateurs';
global $pdo;

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

if ($search) {
    $stmt = $pdo->prepare("
        SELECT * FROM users 
        WHERE role = 'user' AND (username LIKE ? OR email LIKE ? OR full_name LIKE ?) 
        ORDER BY created_at DESC
    ");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC");
}
$users = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="admin-main">
    <div class="admin-header">
        <h1><i class="fas fa-users"></i> Gestion des utilisateurs</h1>
    </div>

    <div class="admin-content">
        <!-- Recherche -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Rechercher un utilisateur..." 
                           value="<?= htmlspecialchars($search) ?>" style="flex:1;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </form>
            </div>
        </div>

        <!-- Liste des utilisateurs -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Nom d'utilisateur</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Date d'inscription</th>
                                <th>Commandes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($users)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Aucun utilisateur trouvé</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($users as $user): ?>
                            <?php
                            $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(total_amount) as total_spent FROM orders WHERE user_id = ?");
                            $stmt->execute([$user['id']]);
                            $stats = $stmt->fetch();
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                                <td><?= formatDateShort($user['created_at']) ?></td>
                                <td>
                                    <?= $stats['total'] ?? 0 ?> commandes<br>
                                    <small><?= formatPrice($stats['total_spent'] ?? 0) ?></small>
                                </td>
                                <td>
                                    <a href="user_detail.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger delete-confirm">
                                        <i class="fas fa-trash"></i>
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

<?php include 'includes/footer.php'; ?>