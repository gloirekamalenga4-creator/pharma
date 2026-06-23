/**
 * Planet Dépôts Pharmaceutique - JavaScript Principal
 * Version 1.0
 */

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ============================================================
    // Mobile Menu Toggle
    // ============================================================
    const mobileToggle = document.querySelector('.mobile-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');

    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        });
    }

    // ============================================================
    // Dropdown Menu (Mobile friendly)
    // ============================================================
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(function(dropdown) {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        if (toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const menu = dropdown.querySelector('.dropdown-menu');
                if (menu) {
                    menu.style.opacity = menu.style.opacity === '1' ? '0' : '1';
                    menu.style.visibility = menu.style.visibility === 'visible' ? 'hidden' : 'visible';
                    menu.style.transform = menu.style.transform === 'translateY(0)' ? 'translateY(10px)' : 'translateY(0)';
                }
            });
        }
    });

    // ============================================================
    // Add to Cart (AJAX)
    // ============================================================
    window.addToCart = function(productId) {
        fetch('ajax/add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Produit ajouté au panier !', 'success');
                updateCartCount(data.cart_count);
            } else {
                showNotification(data.message || 'Erreur', 'error');
            }
        })
        .catch(error => {
            showNotification('Erreur de connexion', 'error');
        });
    };

    // ============================================================
    // Update Cart Count
    // ============================================================
    function updateCartCount(count) {
        const badges = document.querySelectorAll('.cart-badge');
        badges.forEach(function(badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
        });
    }

    // ============================================================
    // Notifications
    // ============================================================
    function showNotification(message, type) {
        // Remove existing notifications
        const existing = document.querySelector('.notification');
        if (existing) {
            existing.remove();
        }

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;

        document.body.appendChild(notification);

        // Style the notification
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.5s ease;
            max-width: 400px;
            min-width: 300px;
        `;

        const closeBtn = notification.querySelector('.notification-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                notification.remove();
            });
        }

        // Auto-remove after 5 seconds
        setTimeout(function() {
            if (notification.parentNode) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100px)';
                notification.style.transition = 'all 0.5s ease';
                setTimeout(function() {
                    notification.remove();
                }, 500);
            }
        }, 5000);
    }

    // ============================================================
    // Add Animation Keyframes
    // ============================================================
    const styleSheet = document.createElement('style');
    styleSheet.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(styleSheet);

    // ============================================================
    // Product Quantity Input
    // ============================================================
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || 999;
            let value = parseInt(this.value) || min;
            
            if (value < min) value = min;
            if (value > max) value = max;
            
            this.value = value;
        });
    });

    // ============================================================
    // Form Validation
    // ============================================================
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const required = this.querySelectorAll('[required]');
            let isValid = true;

            required.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }

                // Email validation
                if (field.type === 'email' && field.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(field.value.trim())) {
                        field.classList.add('error');
                        isValid = false;
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                showNotification('Veuillez remplir tous les champs correctement', 'error');
            }
        });
    });

    // ============================================================
    // Password Strength Indicator
    // ============================================================
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            const value = this.value;
            const strength = document.getElementById('password-strength');
            if (strength) {
                let score = 0;
                if (value.length >= 8) score++;
                if (value.match(/[a-z]/)) score++;
                if (value.match(/[A-Z]/)) score++;
                if (value.match(/[0-9]/)) score++;
                if (value.match(/[^a-zA-Z0-9]/)) score++;

                const levels = ['Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
                const colors = ['#dc3545', '#ffc107', '#ffc107', '#28a745', '#28a745'];
                
                strength.textContent = levels[score] || 'Très faible';
                strength.style.color = colors[score] || '#dc3545';
            }
        });
    });

    // ============================================================
    // Delete Confirmation
    // ============================================================
    const deleteButtons = document.querySelectorAll('.delete-confirm');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    console.log('✅ Planet Dépôts Pharmaceutique - Script chargé');
});