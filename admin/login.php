<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Rediriger si déjà connecté
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND role = 'admin'");
    $stmt->execute([$username, $username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_name'] = $admin['full_name'];
        $_SESSION['user_role'] = $admin['role'];
        header('Location: index.php');
        exit();
    } else {
        $error = 'Identifiants invalides';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1e4a6f 0%, #2c7da0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo i {
            font-size: 48px;
            color: #2c7da0;
        }
        .login-logo h2 {
            font-size: 24px;
            color: #1e2a3a;
            margin-top: 10px;
        }
        .login-logo p {
            color: #6c757d;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #1e2a3a;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            border-color: #2c7da0;
            outline: none;
            box-shadow: 0 0 0 3px rgba(44, 125, 160, 0.1);
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: #2c7da0;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #1e4a6f;
            transform: translateY(-2px);
        }
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
        .login-footer a {
            color: #6c757d;
            text-decoration: none;
            font-size: 14px;
        }
        .login-footer a:hover {
            color: #2c7da0;
        }
        .login-demo {
            margin-top: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
            font-size: 13px;
            color: #6c757d;
        }
        .login-demo p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-logo">
                <i class="fas fa-hospital-user"></i>
                <h2><?= SITE_NAME ?></h2>
                <p>Espace Administration</p>
            </div>
            
            <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Nom d'utilisateur</label>
                    <input type="text" name="username" placeholder="admin" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
            
            <div class="login-footer">
                <a href="../index.php"><i class="fas fa-arrow-left"></i> Retour au site</a>
            </div>
            
            <div class="login-demo">
                <p><strong>🔑 Compte de démonstration</strong></p>
                <p>Nom : admin</p>
                <p>Mot de passe : Admin2024</p>
            </div>
        </div>
    </div>
</body>
</html>