<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Mon profil';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = sanitize($_POST['full_name']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        
        $result = updateUser($user_id, [
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address
        ]);
        
        if ($result) {
            $_SESSION['user_name'] = $full_name;
            $success = 'Profil mis à jour avec succès !';
            $user = getUserById($user_id);
        } else {
            $error = 'Erreur lors de la mise à jour';
        }
    }
    
    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        
        if (!password_verify($current, $user['password'])) {
            $error = 'Mot de passe actuel incorrect';
        } elseif (strlen($new) < 6) {
            $error = 'Le nouveau mot de passe doit contenir au moins 6 caractères';
        } elseif ($new !== $confirm) {
            $error = 'Les mots de passe ne correspondent pas';
        } else {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $result = updateUser($user_id, ['password' => $hashed]);
            if ($result) {
                $success = 'Mot de passe changé avec succès !';
            } else {
                $error = 'Erreur lors du changement de mot de passe';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="profile-page">
        <h1><i class="fas fa-user-edit"></i> Mon profil</h1>

        <?php if($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="profile-grid">
            <!-- Informations personnelles -->
            <div class="profile-card">
                <h3><i class="fas fa-user"></i> Informations personnelles</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Nom d'utilisateur</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label>Nom complet *</label>
                        <input type="text" name="full_name" class="form-control" 
                               value="<?= htmlspecialchars($user['full_name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Adresse</label>
                        <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </form>
            </div>

            <!-- Changer mot de passe -->
            <div class="profile-card">
                <h3><i class="fas fa-key"></i> Changer le mot de passe</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Mot de passe actuel *</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nouveau mot de passe *</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Confirmer le nouveau mot de passe *</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn btn-primary">
                        <i class="fas fa-key"></i> Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.profile-page {
    padding: 40px 0;
}

.profile-page h1 {
    font-size: 28px;
    margin-bottom: 30px;
}

.profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.profile-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: var(--shadow);
}

.profile-card h3 {
    font-size: 18px;
    margin-bottom: 20px;
    color: var(--dark);
}

.profile-card h3 i {
    color: var(--primary);
    margin-right: 8px;
}

.profile-card .form-control[disabled] {
    background: var(--light);
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>