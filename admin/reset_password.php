<?php
require_once '../config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? 'admin';
    $password = $_POST['password'] ?? 'admin123';
    
    // Hacher le mot de passe
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    
    // Mettre à jour ou créer l'admin
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hashed, $username]);
        $message = "✅ Mot de passe mis à jour pour : $username";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'admin')");
        $stmt->execute([$username, $username . '@planetdepots.com', $hashed, 'Administrateur']);
        $message = "✅ Nouvel admin créé : $username";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Réinitialisation Admin</title>
    <style>
        body { font-family: Arial; max-width: 500px; margin: 50px auto; padding: 20px; }
        .box { background: #f8f9fa; padding: 30px; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 10px 20px; background: #2c7da0; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="box">
        <h2>🔑 Réinitialisation Admin</h2>
        
        <?php if($message): ?>
        <p class="success"><?= $message ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <label>Nom d'utilisateur</label>
            <input type="text" name="username" value="admin">
            
            <label>Nouveau mot de passe</label>
            <input type="text" name="password" value="admin123">
            
            <button type="submit">Réinitialiser</button>
        </form>
        
        <hr>
        <p><strong>Identifiants par défaut :</strong></p>
        <p>Utilisateur : admin</p>
        <p>Mot de passe : admin123</p>
    </div>
</body>
</html>