<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Inscription';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    // Validation
    if (empty($username) || empty($email) || empty($full_name) || empty($password)) {
        $error = 'Tous les champs sont requis';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères';
    } elseif ($password !== $confirm) {
        $error = 'Les mots de passe ne correspondent pas';
    } else {
        // Vérifier si l'utilisateur existe déjà
        if (getUserByEmail($email)) {
            $error = 'Cet email est déjà utilisé';
        } elseif (getUserByUsername($username)) {
            $error = 'Ce nom d\'utilisateur est déjà pris';
        } else {
            $result = createUser($username, $email, $password, $full_name, $phone);
            if ($result) {
                $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                header("refresh:2;url=login.php");
            } else {
                $error = 'Erreur lors de l\'inscription. Veuillez réessayer.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="auth-page">
        <div class="auth-card">
            <h2><i class="fas fa-user-plus"></i> Inscription</h2>
            
            <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Nom d'utilisateur *</label>
                    <input type="text" name="username" class="form-control" required 
                           placeholder="john_doe">
                </div>
                
                <div class="form-group">
                    <label>Nom complet *</label>
                    <input type="text" name="full_name" class="form-control" required 
                           placeholder="John Doe">
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" required 
                           placeholder="john@example.com">
                </div>
                
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="phone" class="form-control" 
                           placeholder="+225 XX XX XX XX">
                </div>
                
                <div class="form-group">
                    <label>Mot de passe * (6 caractères minimum)</label>
                    <input type="password" name="password" class="form-control" required 
                           placeholder="••••••••" minlength="6">
                </div>
                
                <div class="form-group">
                    <label>Confirmer le mot de passe *</label>
                    <input type="password" name="confirm_password" class="form-control" required 
                           placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
            </form>
            
            <div class="auth-links">
                <a href="login.php">Déjà inscrit ? Connectez-vous</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>