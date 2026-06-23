<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Connexion';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    $user = getUserByEmail($email);
    if (!$user) {
        $user = getUserByUsername($email);
    }
    
    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] !== 'active') {
            $error = 'Votre compte est inactif. Contactez l\'administrateur.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Mettre à jour la dernière connexion
            $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            // Transférer le panier session vers la base de données
            if (isset($_SESSION[CART_SESSION_KEY]) && !empty($_SESSION[CART_SESSION_KEY])) {
                foreach ($_SESSION[CART_SESSION_KEY] as $product_id => $quantity) {
                    $stmt = $pdo->prepare("
                        INSERT INTO cart (user_id, product_id, quantity) 
                        VALUES (?, ?, ?) 
                        ON DUPLICATE KEY UPDATE quantity = quantity + ?
                    ");
                    $stmt->execute([$user['id'], $product_id, $quantity, $quantity]);
                }
                unset($_SESSION[CART_SESSION_KEY]);
            }
            
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
            } else {
                header('Location: dashboard.php');
            }
            exit();
        }
    } else {
        $error = 'Email ou mot de passe incorrect';
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="auth-page">
        <div class="auth-card">
            <h2><i class="fas fa-sign-in-alt"></i> Connexion</h2>
            
            <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Email ou nom d'utilisateur</label>
                    <input type="text" name="email" class="form-control" required 
                           placeholder="exemple@email.com">
                </div>
                
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" class="form-control" required 
                           placeholder="••••••••">
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        <span>Se souvenir de moi</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
            
            <div class="auth-links">
                <a href="register.php">Pas encore de compte ? Inscrivez-vous</a>
                <a href="forgot_password.php">Mot de passe oublié ?</a>
            </div>
            
            <div class="auth-demo">
                <p><strong>🔑 Comptes de démonstration</strong></p>
                <p>Admin : admin@planetdepots.com / Admin2024</p>
                <p>User : john@example.com / User2024</p>
            </div>
        </div>
    </div>
</div>

<style>
.auth-page {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 0;
}

.auth-card {
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: var(--shadow);
    max-width: 420px;
    width: 100%;
}

.auth-card h2 {
    text-align: center;
    font-size: 24px;
    margin-bottom: 30px;
    color: var(--dark);
}

.auth-card h2 i {
    color: var(--primary);
}

.btn-block {
    width: 100%;
    justify-content: center;
    padding: 14px;
}

.auth-links {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px;
    gap: 8px;
}

.auth-links a {
    color: var(--primary);
    text-decoration: none;
    font-size: 14px;
}

.auth-links a:hover {
    text-decoration: underline;
}

.auth-demo {
    margin-top: 25px;
    padding: 15px;
    background: var(--light);
    border-radius: 8px;
    text-align: center;
    font-size: 13px;
    color: var(--gray);
}

.auth-demo p {
    margin: 3px 0;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-weight: 400;
}
</style>

<?php include 'includes/footer.php'; ?>