<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$page_title = 'Paramètres';
$error = '';
$success = '';

// Récupérer les paramètres actuels
$settings = [
    'site_name' => SITE_NAME,
    'site_email' => SITE_EMAIL,
    'site_phone' => SITE_PHONE,
    'site_address' => SITE_ADDRESS,
    'site_url' => SITE_URL
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_settings'])) {
        // Mettre à jour les paramètres (à implémenter avec une table settings)
        // Pour l'instant, on simule la mise à jour
        $success = 'Paramètres mis à jour avec succès !';
    }
}

include 'includes/header.php';
?>

<div class="admin-main">
    <div class="admin-header">
        <h1><i class="fas fa-cog"></i> Paramètres</h1>
    </div>

    <div class="admin-content">
        <?php if($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="admin-grid-2">
            <!-- Paramètres généraux -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-globe"></i> Paramètres généraux</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label>Nom du site</label>
                            <input type="text" name="site_name" class="form-control" 
                                   value="<?= htmlspecialchars($settings['site_name']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Email du site</label>
                            <input type="email" name="site_email" class="form-control" 
                                   value="<?= htmlspecialchars($settings['site_email']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" name="site_phone" class="form-control" 
                                   value="<?= htmlspecialchars($settings['site_phone']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Adresse</label>
                            <input type="text" name="site_address" class="form-control" 
                                   value="<?= htmlspecialchars($settings['site_address']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>URL du site</label>
                            <input type="url" name="site_url" class="form-control" 
                                   value="<?= htmlspecialchars($settings['site_url']) ?>">
                        </div>
                        
                        <button type="submit" name="update_settings" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </form>
                </div>
            </div>

            <!-- Informations système -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-server"></i> Informations système</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <strong>Version PHP</strong>
                        <span><?= phpversion() ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Version MySQL</strong>
                        <span><?= $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Serveur</strong>
                        <span><?= $_SERVER['SERVER_SOFTWARE'] ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Dossier racine</strong>
                        <span><?= $_SERVER['DOCUMENT_ROOT'] ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Max execution time</strong>
                        <span><?= ini_get('max_execution_time') ?>s</span>
                    </div>
                    <div class="info-item">
                        <strong>Memory limit</strong>
                        <span><?= ini_get('memory_limit') ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Upload max file size</strong>
                        <span><?= ini_get('upload_max_filesize') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance -->
        <div class="card mt-4">
            <div class="card-header">
                <h3><i class="fas fa-tools"></i> Maintenance</h3>
            </div>
            <div class="card-body">
                <div class="maintenance-actions">
                    <a href="backup_database.php" class="btn btn-primary">
                        <i class="fas fa-database"></i> Sauvegarder la base de données
                    </a>
                    <a href="clear_cache.php" class="btn btn-warning">
                        <i class="fas fa-broom"></i> Vider le cache
                    </a>
                    <a href="check_products.php" class="btn btn-info">
                        <i class="fas fa-check-circle"></i> Vérifier les produits
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid var(--gray-light);
}

.info-item:last-child {
    border-bottom: none;
}

.info-item strong {
    color: var(--dark);
}

.info-item span {
    color: var(--gray);
}

.mt-4 {
    margin-top: 20px;
}

.maintenance-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .admin-grid-2 {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>