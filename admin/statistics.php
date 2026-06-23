<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$page_title = 'Statistiques';
global $pdo;

// Statistiques générales
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_products_active = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_orders_delivered = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'delivered'")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled'")->fetchColumn();

// Ventes par mois (derniers 12 mois)
$monthly_sales = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
           COUNT(*) as orders, 
           SUM(total_amount) as revenue 
    FROM orders 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC
")->fetchAll();

// Ventes par catégorie
$category_sales = $pdo->query("
    SELECT c.name, COUNT(oi.id) as total_items, SUM(oi.total) as total_revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN categories c ON p.category_id = c.id
    GROUP BY c.id
    ORDER BY total_revenue DESC
    LIMIT 5
")->fetchAll();

// Commandes par statut
$status_counts = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM orders 
    GROUP BY status
")->fetchAll();

include 'includes/header.php';
?>

<div class="admin-main">
    <div class="admin-header">
        <h1><i class="fas fa-chart-line"></i> Statistiques</h1>
    </div>

    <div class="admin-content">
        <!-- Stats générales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--primary);"><i class="fas fa-box"></i></div>
                <div class="stat-info">
                    <h3><?= $total_products ?></h3>
                    <p>Produits total</p>
                    <small><?= $total_products_active ?> actifs</small>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--success);"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-info">
                    <h3><?= $total_orders ?></h3>
                    <p>Commandes</p>
                    <small><?= $total_orders_delivered ?> livrées</small>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--warning);"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3><?= $total_users ?></h3>
                    <p>Utilisateurs</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--danger);"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-info">
                    <h3><?= formatPrice($total_revenue ?: 0) ?></h3>
                    <p>Chiffre d'affaires</p>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="stats-grid-2">
            <!-- Ventes mensuelles -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-alt"></i> Ventes mensuelles</h3>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="250"></canvas>
                </div>
            </div>

            <!-- Commandes par statut -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie"></i> Commandes par statut</h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Ventes par catégorie -->
        <div class="card mt-4">
            <div class="card-header">
                <h3><i class="fas fa-tags"></i> Ventes par catégorie</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Articles vendus</th>
                                <th>Chiffre d'affaires</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($category_sales)): ?>
                            <tr>
                                <td colspan="3" class="text-center">Aucune donnée disponible</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($category_sales as $cat): ?>
                            <tr>
                                <td><?= htmlspecialchars($cat['name']) ?></td>
                                <td><?= $cat['total_items'] ?></td>
                                <td><?= formatPrice($cat['total_revenue']) ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des ventes mensuelles
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const monthlyData = <?= json_encode($monthly_sales) ?>;
    
    new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Chiffre d\'affaires',
                data: monthlyData.map(item => item.revenue),
                backgroundColor: 'rgba(44, 125, 160, 0.7)',
                borderColor: '#2c7da0',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' FCFA';
                        }
                    }
                }
            }
        }
    });

    // Graphique des statuts
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = <?= json_encode($status_counts) ?>;
    const colors = {
        'pending': '#ffc107',
        'confirmed': '#17a2b8',
        'shipped': '#007bff',
        'delivered': '#28a745',
        'cancelled': '#dc3545',
        'refunded': '#6c757d'
    };
    
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(item => item.status),
            datasets: [{
                data: statusData.map(item => item.count),
                backgroundColor: statusData.map(item => colors[item.status] || '#6c757d')
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>

<style>
.stats-grid-2 {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-top: 20px;
}

.mt-4 {
    margin-top: 20px;
}

@media (max-width: 768px) {
    .stats-grid-2 {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>