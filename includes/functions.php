<?php
/**
 * Fonctions globales de l'application
 * Planet Dépôts Pharmaceutique
 */

// ============================================================
// GESTION DU PANIER
// ============================================================

function getCartItems() {
    global $pdo;
    if (isLoggedIn()) {
        $stmt = $pdo->prepare("
            SELECT c.*, p.name, p.price, p.image_url, p.slug 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchAll();
    } elseif (isset($_SESSION[CART_SESSION_KEY])) {
        $items = [];
        foreach ($_SESSION[CART_SESSION_KEY] as $product_id => $quantity) {
            $product = getProductById($product_id);
            if ($product) {
                $items[] = [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image_url' => $product['image_url'],
                    'slug' => $product['slug']
                ];
            }
        }
        return $items;
    }
    return [];
}

function getCartTotal() {
    $total = 0;
    $items = getCartItems();
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

function getCartCount() {
    global $pdo;
    if (isLoggedIn()) {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    } elseif (isset($_SESSION[CART_SESSION_KEY])) {
        return array_sum($_SESSION[CART_SESSION_KEY]);
    }
    return 0;
}

function addToCart($product_id, $quantity = 1) {
    global $pdo;
    if (isLoggedIn()) {
        $stmt = $pdo->prepare("
            INSERT INTO cart (user_id, product_id, quantity) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE quantity = quantity + ?
        ");
        return $stmt->execute([$_SESSION['user_id'], $product_id, $quantity, $quantity]);
    } else {
        if (!isset($_SESSION[CART_SESSION_KEY])) {
            $_SESSION[CART_SESSION_KEY] = [];
        }
        if (isset($_SESSION[CART_SESSION_KEY][$product_id])) {
            $_SESSION[CART_SESSION_KEY][$product_id] += $quantity;
        } else {
            $_SESSION[CART_SESSION_KEY][$product_id] = $quantity;
        }
        return true;
    }
}

function removeFromCart($product_id) {
    global $pdo;
    if (isLoggedIn()) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$_SESSION['user_id'], $product_id]);
    } else {
        if (isset($_SESSION[CART_SESSION_KEY][$product_id])) {
            unset($_SESSION[CART_SESSION_KEY][$product_id]);
            return true;
        }
    }
    return false;
}

function updateCartQuantity($product_id, $quantity) {
    global $pdo;
    if ($quantity <= 0) {
        return removeFromCart($product_id);
    }
    if (isLoggedIn()) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$quantity, $_SESSION['user_id'], $product_id]);
    } else {
        if (isset($_SESSION[CART_SESSION_KEY][$product_id])) {
            $_SESSION[CART_SESSION_KEY][$product_id] = $quantity;
            return true;
        }
    }
    return false;
}

function clearCart() {
    global $pdo;
    if (isLoggedIn()) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } else {
        unset($_SESSION[CART_SESSION_KEY]);
    }
}

function getCartSubtotal() {
    $total = 0;
    $items = getCartItems();
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

function calculateShipping($total) {
    if ($total >= 50000) { // Livraison gratuite à partir de 50 000 FCFA
        return 0;
    }
    return 2000; // Frais de port fixes
}

// ============================================================
// GESTION DES PRODUITS
// ============================================================

function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getProductBySlug($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function getProducts($category_id = null, $limit = null, $offset = 0) {
    global $pdo;
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1";
    $params = [];
    
    if ($category_id) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getFeaturedProducts($limit = 8) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * FROM products 
        WHERE is_featured = 1 AND is_active = 1 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getLatestProducts($limit = 8) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * FROM products 
        WHERE is_active = 1 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getRelatedProducts($product_id, $category_id, $limit = 4) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * FROM products 
        WHERE category_id = ? AND id != ? AND is_active = 1 
        ORDER BY RAND() 
        LIMIT ?
    ");
    $stmt->execute([$category_id, $product_id, $limit]);
    return $stmt->fetchAll();
}

function getLowStockProducts($limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * FROM products 
        WHERE stock <= min_stock AND is_active = 1 
        ORDER BY stock ASC 
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getCategories() {
    global $pdo;
    return $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();
}

function getCategoryById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getCategoryBySlug($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function getStockStatus($stock, $min_stock = 5) {
    if ($stock <= 0) {
        return ['class' => 'danger', 'label' => 'Rupture de stock'];
    } elseif ($stock <= $min_stock) {
        return ['class' => 'warning', 'label' => 'Stock faible (' . $stock . ')'];
    }
    return ['class' => 'success', 'label' => 'En stock (' . $stock . ')'];
}

function isProductInStock($product_id, $quantity = 1) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $result = $stmt->fetch();
    return $result && $result['stock'] >= $quantity;
}

// ============================================================
// GESTION DES COMMANDES
// ============================================================

function createOrder($user_id, $total, $shipping_address, $payment_method) {
    global $pdo;
    $order_number = generateOrderNumber();
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("
            INSERT INTO orders (order_number, user_id, total_amount, shipping_address, payment_method) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$order_number, $user_id, $total, $shipping_address, $payment_method]);
        $order_id = $pdo->lastInsertId();
        
        $cart_items = getCartItems();
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, quantity, price, total) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $total_item = $item['price'] * $item['quantity'];
            $stmt->execute([
                $order_id, 
                $item['product_id'], 
                $item['name'], 
                $item['quantity'], 
                $item['price'], 
                $total_item
            ]);
            
            // Mettre à jour le stock
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }
        
        clearCart();
        $pdo->commit();
        return $order_number;
    } catch(Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function getOrders($user_id = null) {
    global $pdo;
    if ($user_id) {
        $stmt = $pdo->prepare("
            SELECT * FROM orders 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->query("
            SELECT o.*, u.full_name, u.email 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC
        ");
    }
    return $stmt->fetchAll();
}

function getOrderById($id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT o.*, u.full_name, u.email, u.phone, u.address 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getOrderItems($order_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT oi.*, p.image_url 
        FROM order_items oi 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll();
}

function updateOrderStatus($order_id, $status) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $order_id]);
}

function getStatusBadge($status) {
    $classes = [
        'pending' => 'warning',
        'processing' => 'info',
        'confirmed' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
        'refunded' => 'secondary'
    ];
    $labels = [
        'pending' => 'En attente',
        'processing' => 'En traitement',
        'confirmed' => 'Confirmée',
        'shipped' => 'Expédiée',
        'delivered' => 'Livrée',
        'cancelled' => 'Annulée',
        'refunded' => 'Remboursée'
    ];
    return [
        'class' => $classes[$status] ?? 'secondary',
        'label' => $labels[$status] ?? $status
    ];
}

// ============================================================
// GESTION DES UTILISATEURS
// ============================================================

function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function getUserByUsername($username) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch();
}

function createUser($username, $email, $password, $full_name, $phone = null, $address = null) {
    global $pdo;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password, full_name, phone, address) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([$username, $email, $hashed_password, $full_name, $phone, $address]);
}

function updateUser($id, $data) {
    global $pdo;
    $fields = [];
    $params = [];
    
    foreach ($data as $key => $value) {
        if ($key !== 'id') {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
    }
    
    $params[] = $id;
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function deleteUser($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    return $stmt->execute([$id]);
}

// ============================================================
// GESTION DES AVIS
// ============================================================

function getProductReviews($product_id, $limit = null) {
    global $pdo;
    $sql = "
        SELECT r.*, u.full_name 
        FROM reviews r 
        LEFT JOIN users u ON r.user_id = u.id 
        WHERE r.product_id = ? AND r.is_approved = 1 
        ORDER BY r.created_at DESC
    ";
    if ($limit) {
        $sql .= " LIMIT ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id, $limit]);
    } else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id]);
    }
    return $stmt->fetchAll();
}

function getProductRating($product_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT AVG(rating) as average, COUNT(*) as total 
        FROM reviews 
        WHERE product_id = ? AND is_approved = 1
    ");
    $stmt->execute([$product_id]);
    return $stmt->fetch();
}

function addReview($product_id, $user_id, $rating, $title, $comment) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO reviews (product_id, user_id, rating, title, comment, is_approved) 
        VALUES (?, ?, ?, ?, ?, 0)
    ");
    return $stmt->execute([$product_id, $user_id, $rating, $title, $comment]);
}

// ============================================================
// GESTION DE LA WISHLIST
// ============================================================

function getWishlist($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT w.*, p.name, p.price, p.image_url, p.slug 
        FROM wishlist w 
        JOIN products p ON w.product_id = p.id 
        WHERE w.user_id = ?
        ORDER BY w.created_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function addToWishlist($user_id, $product_id) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)");
    return $stmt->execute([$user_id, $product_id]);
}

function removeFromWishlist($user_id, $product_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    return $stmt->execute([$user_id, $product_id]);
}

function isInWishlist($user_id, $product_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    return $stmt->fetch() !== false;
}

// ============================================================
// FONCTIONS UTILITAIRES
// ============================================================

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    if ($difference < 60) {
        return 'Il y a ' . $difference . ' secondes';
    } elseif ($difference < 3600) {
        $minutes = floor($difference / 60);
        return 'Il y a ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    } elseif ($difference < 86400) {
        $hours = floor($difference / 3600);
        return 'Il y a ' . $hours . ' heure' . ($hours > 1 ? 's' : '');
    } elseif ($difference < 604800) {
        $days = floor($difference / 86400);
        return 'Il y a ' . $days . ' jour' . ($days > 1 ? 's' : '');
    } else {
        return formatDate($datetime);
    }
}

function getMonthName($month) {
    $months = [
        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
    ];
    return $months[(int)$month] ?? $month;
}

function sendEmail($to, $subject, $message, $from = null) {
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= 'From: ' . ($from ?: SITE_EMAIL) . "\r\n";
    return mail($to, $subject, $message, $headers);
}

function generateToken() {
    return bin2hex(random_bytes(32));
}
?>