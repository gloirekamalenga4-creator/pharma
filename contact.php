<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Contact';
$success = '';
$error = '';

// Récupérer les infos de l'utilisateur connecté de manière sécurisée
$user_name = '';
$user_email = '';

if (isLoggedIn()) {
    // Récupérer les données de l'utilisateur depuis la base de données
    $user = getUserById($_SESSION['user_id']);
    if ($user) {
        $user_name = $user['full_name'];
        $user_email = $user['email'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Tous les champs sont requis';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide';
    } else {
        // Envoyer l'email
        $to = SITE_EMAIL;
        $body = "Nom: $name\n";
        $body .= "Email: $email\n";
        $body .= "Sujet: $subject\n\n";
        $body .= "Message:\n$message";
        
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        
        if (mail($to, $subject, $body, $headers)) {
            $success = 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.';
        } else {
            $error = 'Erreur lors de l\'envoi du message. Veuillez réessayer.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="contact-page">
        <h1><i class="fas fa-envelope"></i> Contactez-nous</h1>

        <div class="contact-grid">
            <div class="contact-form">
                <h3>Envoyez-nous un message</h3>
                
                <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Nom complet *</label>
                        <input type="text" name="name" class="form-control" required 
                               value="<?= htmlspecialchars($user_name) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control" required 
                               value="<?= htmlspecialchars($user_email) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Sujet *</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Message *</label>
                        <textarea name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Envoyer
                    </button>
                </form>
            </div>

            <div class="contact-info-side">
                <h3>Nos coordonnées</h3>
                <ul>
                    <li>
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Téléphone</strong>
                            <a href="tel:<?= SITE_PHONE ?>"><?= SITE_PHONE ?></a>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email</strong>
                            <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Adresse</strong>
                            <span><?= SITE_ADDRESS ?></span>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Horaires d'ouverture</strong>
                            <span>Lundi - Samedi : 8h - 20h</span>
                            <span>Dimanche : Fermé</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.contact-page {
    padding: 40px 0;
}

.contact-page h1 {
    font-size: 28px;
    margin-bottom: 30px;
}

.contact-page h1 i {
    color: var(--primary);
}

.contact-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.contact-form {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: var(--shadow);
}

.contact-form h3 {
    font-size: 20px;
    margin-bottom: 20px;
}

.contact-info-side {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: var(--shadow);
    align-self: start;
}

.contact-info-side h3 {
    font-size: 20px;
    margin-bottom: 20px;
}

.contact-info-side ul {
    list-style: none;
}

.contact-info-side ul li {
    display: flex;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid var(--gray-light);
}

.contact-info-side ul li:last-child {
    border-bottom: none;
}

.contact-info-side ul li i {
    font-size: 20px;
    color: var(--primary);
    width: 25px;
    margin-top: 3px;
}

.contact-info-side ul li div {
    display: flex;
    flex-direction: column;
}

.contact-info-side ul li strong {
    font-size: 14px;
    color: var(--dark);
}

.contact-info-side ul li span,
.contact-info-side ul li a {
    color: var(--gray);
}

.contact-info-side ul li a:hover {
    color: var(--primary);
}

.alert {
    padding: 12px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: var(--dark);
    margin-bottom: 5px;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid var(--gray-light);
    border-radius: 5px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(44, 125, 160, 0.1);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .contact-form {
        padding: 20px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>