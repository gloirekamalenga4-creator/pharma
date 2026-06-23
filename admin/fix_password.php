<?php
require_once '../config/database.php';

// Le mot de passe que vous voulez utiliser
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Mettre à jour tous les admins avec ce mot de passe
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE role = 'admin'");
$result = $stmt->execute([$hashed_password]);

// Vérifier si des admins existent
$count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();

if ($count == 0) {
    // Créer un admin s'il n'existe pas
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'admin')");
    $stmt->execute(['admin', 'admin@planetdepots.com', $hashed_password, 'Administrateur']);
    $created = true;
} else {
    $created = false;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Réinitialisation Admin</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f4f6f9;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { color: #28a745; }
        .info { color: #17a2b8; }
        .box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #2c7da0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #2c7da0;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .btn:hover { background: #1e4a6f; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔑 Réinitialisation Admin</h1>
        
        <div class="box">
            <p><strong>Statut :</strong></p>
            <?php if($result): ?>
                <p class="success">✅ Mot de passe mis à jour avec succès !</p>
            <?php else: ?>
                <p class="info">⚠️ Erreur lors de la mise à jour</p>
            <?php endif; ?>
            
            <?php if($created): ?>
                <p class="success">✅ Nouvel administrateur créé !</p>
            <?php endif; ?>
            
            <p>Administrateurs trouvés : <strong><?= $count ?></strong></p>
        </div>
        
        <div class="box">
            <h3>📋 Identifiants de connexion</h3>
            <p><strong>Nom d'utilisateur :</strong> <code>admin</code></p>
            <p><strong>Mot de passe :</strong> <code>admin123</code></p>
            <p><strong>Email :</strong> <code>admin@planetdepots.com</code></p>
        </div>
        
        <a href="login.php" class="btn">🔐 Aller à la page de connexion</a>
    </div>
</body>
</html>