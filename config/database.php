<?php
/**
 * Configuration de la base de données
 * Planet Dépôts Pharmaceutique
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'pharma_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuration du site
define('SITE_NAME', 'Planet Dépôts Pharmaceutique');
define('SITE_URL', 'http://localhost/pharma/');
define('SITE_EMAIL', 'contact@planetdepots.com');
define('SITE_PHONE', '+225 05 05 05 05');
define('SITE_ADDRESS', 'Abidjan, Côte d\'Ivoire');

// Configuration du panier
define('CART_SESSION_KEY', 'pharma_cart');

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// CONNEXION À LA BASE DE DONNÉES
// ============================================================
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die('❌ Erreur de connexion à la base de données : ' . $e->getMessage());
}

// ============================================================
// FONCTIONS DE BASE (UNIQUEMENT CELLES ESSENTIELLES)
// ============================================================

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function redirect($url) {
    header('Location: ' . SITE_URL . $url);
    exit();
}

function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' FCFA';
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

function formatDateShort($date) {
    return date('d/m/Y', strtotime($date));
}

function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}
?>