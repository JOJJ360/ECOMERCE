<?php
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - Beauty Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="../index.php">
                    <i class="fas fa-spa"></i> Beauty Store
                </a>
                <a href="../index.php" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left"></i> Continuar Comprando
                </a>
            </div>
        </nav>
    </header>

    <!-- Carrinho -->
    <section class="py-5">
        <div class="container">
            <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Seu Carrinho</h2>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-body" id="cart-items">
                            <!-- Itens do carrinho serão carregados via JavaScript -->
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Seu carrinho está vazio</p>
                                <a href="../index.php" class="btn btn-primary">
                                    <i class="fas fa-shopping-bag"></i> Começar a Comprar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Resumo do Pedido</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span id="subtotal">R$ 0,00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Frete</span>
                                <span id="shipping">R$ 0,00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total</strong>
                                <strong id="total">R$ 0,00</strong>
                            </div>
                            
                            <?php if (isLoggedIn()): ?>
                                <a href="checkout.php" class="btn btn-primary w-100">
                                    <i class="fas fa-credit-card"></i> Finalizar Compra
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary w-100">
                                    <i class="fas fa-sign-in-alt"></i> Fazer Login para Continuar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    
    <script>
    // Carregar itens do carrinho
    function loadCartItems() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartItemsElement = document.getElementById('cart-items');
        
        if (cart.length === 0) {
            cartItemsElement.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Seu carrinho está vazio</p>
                    <a href="../index.php" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> Começar a Comprar
                    </a>
                </div>
            `;
            return;
        }
        
        let html = '';
        cart.forEach(item => {
            html += `
                <div class="cart-item mb-3 p-3 border rounded">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="https://via.placeholder.com/100x100/ff66b2/ffffff?text=${encodeURIComponent(item.name.substring(0, 10))}" 
                                 class="img-fluid rounded" alt="${item.name}">
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-1">${item.name}</h6>
                            <p class="text-muted mb-0">Preço: R$ ${item.price.toFixed(2).replace('.', ',')}</p>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                                <input type="number" class="form-control text-center quantity-input" 
                                       value="${item.quantity}" min="1" 
                                       onchange="updateQuantity(${item.id}, this.value)"
                                       data-id="${item.id}">
                                <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <strong>R$ ${(item.price * item.quantity).toFixed(2).replace('.', ',')}</strong>
                        </div>
                        <div class="col-md-1 text-end">
                            <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        cartItemsElement.innerHTML = html;
        calculateTotal();
    }
    
    // Atualizar quantidade
    function updateQuantity(productId, quantity) {
        if (quantity < 1) return;
        
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const product = cart.find(item => item.id === productId);
        
        if (product) {
            product.quantity = parseInt(quantity);
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCartItems();
            updateCartCount();
        }
    }
    
    // Carregar carrinho quando a página carregar
    document.addEventListener('DOMContentLoaded', function() {
        loadCartItems();
        updateCartCount();
    });
    </script>
</body>
</html>