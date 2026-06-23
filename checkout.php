<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Validation de commande';

if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    $_SESSION['error'] = 'Veuillez vous connecter pour passer commande';
    header('Location: login.php');
    exit();
}

$cart_items = getCartItems();
$total = getCartTotal();

if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

$user = getUserById($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = sanitize($_POST['shipping_address']);
    $payment_method = sanitize($_POST['payment_method']);
    $notes = sanitize($_POST['notes'] ?? '');
    
    if (empty($shipping_address)) {
        $_SESSION['error'] = 'Veuillez renseigner votre adresse de livraison';
    } else {
        $order_number = createOrder(
            $_SESSION['user_id'],
            $total + calculateShipping(getCartSubtotal()),
            $shipping_address,
            $payment_method
        );
        
        if ($order_number) {
            $_SESSION['order_number'] = $order_number;
            header('Location: order_confirmation.php');
            exit();
        } else {
            $_SESSION['error'] = 'Une erreur est survenue lors de la validation de votre commande';
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="checkout-page">
        <h1><i class="fas fa-check-circle"></i> Validation de commande</h1>

        <div class="checkout-grid">
            <!-- Formulaire -->
            <div class="checkout-form">
                <h3>Informations de livraison</h3>
                
                <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Adresse de livraison *</label>
                        <textarea name="shipping_address" class="form-control" rows="4" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group                    <div class="form-group">
                        <label>Mode de paiement *</label>
                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card" checked>
                                <i class="fas fa-credit-card"></i>
                                <span>Carte bancaire</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="mobile_money">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Mobile Money</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="paypal">
                                <i class="fab fa-paypal"></i>
                                <span>PayPal</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="transfer">
                                <i class="fas fa-university"></i>
                                <span>Virement bancaire</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes (optionnel)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Instructions particulières..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle"></i> Confirmer ma commande
                        </button>
                        <a href="cart.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour au panier
                        </a>
                    </div>
                </form>
            </div>

            <!-- Résumé -->
            <div class="checkout-summary">
                <h3>Récapitulatif</h3>
                
                <?php foreach($cart_items as $item): ?>
                <div class="summary-item">
                    <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                    <span><?= formatPrice($item['price'] * $item['quantity']) ?></span>
                </div>
                <?php endforeach; ?>
                
                <div class="summary-divider"></div>
                
                <div class="summary-row">
                    <span>Sous-total</span>
                    <span><?= formatPrice(getCartSubtotal()) ?></span>
                </div>
                
                <div class="summary-row">
                    <span>Livraison</span>
                    <span><?= formatPrice(calculateShipping(getCartSubtotal())) ?></span>
                </div>
                
                <div class="summary-row total">
                    <span>Total</span>
                    <span><?= formatPrice($total + calculateShipping(getCartSubtotal())) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.checkout-page {
    padding: 40px 0;
}

.checkout-page h1 {
    font-size: 28px;
    margin-bottom: 30px;
}

.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 30px;
}

.checkout-form {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: var(--shadow);
}

.checkout-form h3 {
    font-size: 20px;
    margin-bottom: 20px;
    color: var(--dark);
}

.payment-methods {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.payment-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    border: 2px solid var(--gray-light);
    border-radius: 8px;
    cursor: pointer;
    transition: var(--transition);
}

.payment-option:hover {
    border-color: var(--primary);
}

.payment-option input[type="radio"] {
    display: none;
}

.payment-option:has(input:checked) {
    border-color: var(--primary);
    background: rgba(44, 125, 160, 0.05);
}

.payment-option i {
    font-size: 20px;
    color: var(--primary);
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.checkout-summary {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: var(--shadow);
    position: sticky;
    top: 100px;
    align-self: start;
}

.checkout-summary h3 {
    font-size: 18px;
    margin-bottom: 20px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
    border-bottom: 1px solid var(--gray-light);
}

.summary-divider {
    border-top: 2px solid var(--gray-light);
    margin: 15px 0;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
}

.summary-row.total {
    font-size: 18px;
    font-weight: 700;
    border-top: 2px solid var(--gray-light);
    padding-top: 15px;
    margin-top: 10px;
}

@media (max-width: 992px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .payment-methods {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>