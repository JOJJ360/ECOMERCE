<?php
require_once '../includes/config.php';

// Verificar se está logado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Processar checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodo_pagamento = $_POST['metodo_pagamento'] ?? '';
    $endereco = [
        'rua' => $_POST['rua'] ?? '',
        'numero' => $_POST['numero'] ?? '',
        'complemento' => $_POST['complemento'] ?? '',
        'bairro' => $_POST['bairro'] ?? '',
        'cidade' => $_POST['cidade'] ?? '',
        'estado' => $_POST['estado'] ?? '',
        'cep' => $_POST['cep'] ?? ''
    ];
    
    // Obter carrinho do localStorage via JavaScript
    // Em um sistema real, isso seria processado no servidor
    // Aqui estamos apenas simulando
    
    $_SESSION['checkout_data'] = [
        'metodo_pagamento' => $metodo_pagamento,
        'endereco' => $endereco
    ];
    
    header('Location: sucesso.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Beauty Store</title>
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
                <div class="text-white">
                    <i class="fas fa-shopping-cart"></i> Finalização da Compra
                </div>
            </div>
        </nav>
    </header>

    <!-- Checkout Steps -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="checkout-steps">
                <div class="step active">
                    <i class="fas fa-user"></i> Login
                </div>
                <div class="step active">
                    <i class="fas fa-map-marker-alt"></i> Endereço
                </div>
                <div class="step">
                    <i class="fas fa-credit-card"></i> Pagamento
                </div>
                <div class="step">
                    <i class="fas fa-check"></i> Confirmação
                </div>
            </div>
        </div>
    </section>

    <!-- Checkout Form -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Endereço de Entrega</h5>
                        </div>
                        <div class="card-body">
                            <form id="checkout-form" method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="cep" class="form-label">CEP *</label>
                                        <input type="text" class="form-control" id="cep" name="cep" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="rua" class="form-label">Rua *</label>
                                        <input type="text" class="form-control" id="rua" name="rua" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="numero" class="form-label">Número *</label>
                                        <input type="text" class="form-control" id="numero" name="numero" required>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label for="complemento" class="form-label">Complemento</label>
                                        <input type="text" class="form-control" id="complemento" name="complemento">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="bairro" class="form-label">Bairro *</label>
                                        <input type="text" class="form-control" id="bairro" name="bairro" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cidade" class="form-label">Cidade *</label>
                                        <input type="text" class="form-control" id="cidade" name="cidade" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="estado" class="form-label">Estado *</label>
                                        <select class="form-select" id="estado" name="estado" required>
                                            <option value="">Selecione...</option>
                                            <option value="SP">São Paulo</option>
                                            <option value="RJ">Rio de Janeiro</option>
                                            <option value="MG">Minas Gerais</option>
                                            <option value="ES">Espírito Santo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <div class="card-header bg-light mt-4">
                                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> Forma de Pagamento</h5>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="metodo_pagamento" 
                                                   id="cartao" value="cartao" checked>
                                            <label class="form-check-label" for="cartao">
                                                <i class="fas fa-credit-card"></i> Cartão de Crédito
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="metodo_pagamento" 
                                                   id="pix" value="pix">
                                            <label class="form-check-label" for="pix">
                                                <i class="fas fa-qrcode"></i> PIX
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="metodo_pagamento" 
                                                   id="boleto" value="boleto">
                                            <label class="form-check-label" for="boleto">
                                                <i class="fas fa-barcode"></i> Boleto
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check-circle"></i> Finalizar Pedido
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow sticky-top" style="top: 20px;">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Resumo do Pedido</h5>
                        </div>
                        <div class="card-body">
                            <div id="order-summary">
                                <!-- Resumo será carregado via JavaScript -->
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span id="checkout-subtotal">R$ 0,00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Frete</span>
                                <span id="checkout-shipping">R$ 0,00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total</strong>
                                <strong id="checkout-total">R$ 0,00</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    
    <script>
    // Carregar resumo do pedido
    function loadOrderSummary() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const orderSummary = document.getElementById('order-summary');
        
        if (cart.length === 0) {
            orderSummary.innerHTML = '<p class="text-muted">Nenhum item no carrinho</p>';
            return;
        }
        
        let html = '';
        let subtotal = 0;
        
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;
            
            html += `
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <small>${item.name}</small><br>
                        <small class="text-muted">${item.quantity} x R$ ${item.price.toFixed(2).replace('.', ',')}</small>
                    </div>
                    <small>R$ ${itemTotal.toFixed(2).replace('.', ',')}</small>
                </div>
            `;
        });
        
        orderSummary.innerHTML = html;
        
        const shipping = subtotal > 200 ? 0 : 15.90;
        const total = subtotal + shipping;
        
        document.getElementById('checkout-subtotal').textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
        document.getElementById('checkout-shipping').textContent = 'R$ ' + shipping.toFixed(2).replace('.', ',');
        document.getElementById('checkout-total').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
    }
    
    // Validar formulário
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Preencha todos os campos obrigatórios!');
        } else {
            // Limpar carrinho após finalizar compra
            localStorage.removeItem('cart');
        }
    });
    
    // Carregar resumo quando a página carregar
    document.addEventListener('DOMContentLoaded', function() {
        loadOrderSummary();
    });
    </script>
</body>
</html>