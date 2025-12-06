// Funções gerais do e-commerce

// Inicializar carrinho na sessão
function initCart() {
    if (!localStorage.getItem('cart')) {
        localStorage.setItem('cart', JSON.stringify([]));
    }
}

// Adicionar produto ao carrinho
function addToCart(productId, productName, price, quantity = 1) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Verificar se o produto já está no carrinho
    const existingProduct = cart.find(item => item.id === productId);
    
    if (existingProduct) {
        existingProduct.quantity += quantity;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: price,
            quantity: quantity
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    showAlert('Produto adicionado ao carrinho!', 'success');
}

// Remover produto do carrinho
function removeFromCart(productId) {
    if (confirm('Tem certeza que deseja remover este produto do carrinho?')) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart = cart.filter(item => item.id !== productId);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        
        // Recarregar a página do carrinho se estiver nela
        if (window.location.pathname.includes('carrinho.php')) {
            location.reload();
        }
    }
}

// Atualizar quantidade no carrinho
function updateQuantity(productId, quantity) {
    if (quantity < 1) {
        removeFromCart(productId);
        return;
    }
    
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const product = cart.find(item => item.id === productId);
    
    if (product) {
        product.quantity = parseInt(quantity);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        
        // Recalcular total se estiver na página do carrinho
        if (window.location.pathname.includes('carrinho.php') || 
            window.location.pathname.includes('checkout.php')) {
            calculateTotal();
        }
    }
}

// Atualizar contador do carrinho
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const count = cart.reduce((total, item) => total + item.quantity, 0);
    const cartCountElement = document.getElementById('cart-count');
    
    if (cartCountElement) {
        cartCountElement.textContent = count;
        cartCountElement.style.display = count > 0 ? 'inline' : 'none';
    }
}

// Calcular total do carrinho
function calculateTotal() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    let subtotal = 0;
    
    cart.forEach(item => {
        subtotal += item.price * item.quantity;
    });
    
    const shipping = subtotal > 200 ? 0 : 15.90;
    const total = subtotal + shipping;
    
    // Atualizar valores na página do carrinho
    const subtotalElement = document.getElementById('subtotal');
    const shippingElement = document.getElementById('shipping');
    const totalElement = document.getElementById('total');
    
    if (subtotalElement) subtotalElement.textContent = formatPrice(subtotal);
    if (shippingElement) shippingElement.textContent = formatPrice(shipping);
    if (totalElement) totalElement.textContent = formatPrice(total);
    
    // Atualizar valores na página de checkout
    const checkoutSubtotal = document.getElementById('checkout-subtotal');
    const checkoutShipping = document.getElementById('checkout-shipping');
    const checkoutTotal = document.getElementById('checkout-total');
    
    if (checkoutSubtotal) checkoutSubtotal.textContent = formatPrice(subtotal);
    if (checkoutShipping) checkoutShipping.textContent = formatPrice(shipping);
    if (checkoutTotal) checkoutTotal.textContent = formatPrice(total);
    
    return { subtotal, shipping, total };
}

// Formatar preço
function formatPrice(price) {
    return 'R$ ' + price.toFixed(2).replace('.', ',');
}

// Mostrar alerta
function showAlert(message, type = 'info') {
    // Criar elemento de alerta
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    `;
    alertDiv.innerHTML = `
        <strong>${type === 'success' ? '✓' : '!'}</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Adicionar ao body
    document.body.appendChild(alertDiv);
    
    // Remover após 3 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 3000);
}

// Buscar produtos
function searchProducts() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const products = document.querySelectorAll('.product-card');
    let found = false;
    
    products.forEach(product => {
        const title = product.querySelector('.card-title').textContent.toLowerCase();
        const description = product.querySelector('.card-text')?.textContent.toLowerCase() || '';
        
        if (title.includes(searchTerm) || description.includes(searchTerm)) {
            product.style.display = 'block';
            found = true;
        } else {
            product.style.display = 'none';
        }
    });
    
    if (!found && searchTerm) {
        showAlert('Nenhum produto encontrado para sua busca.', 'warning');
    }
}

// Ordenar produtos
function sortProducts(sortBy) {
    const productContainer = document.getElementById('product-container');
    const products = Array.from(productContainer.querySelectorAll('.product-card'));
    
    products.sort((a, b) => {
        const priceA = parseFloat(a.getAttribute('data-price'));
        const priceB = parseFloat(b.getAttribute('data-price'));
        const nameA = a.querySelector('.card-title').textContent.toLowerCase();
        const nameB = b.querySelector('.card-title').textContent.toLowerCase();
        
        switch(sortBy) {
            case 'price-asc':
                return priceA - priceB;
            case 'price-desc':
                return priceB - priceA;
            case 'name-asc':
                return nameA.localeCompare(nameB);
            case 'name-desc':
                return nameB.localeCompare(nameA);
            default:
                return 0;
        }
    });
    
    // Reordenar no DOM
    products.forEach(product => productContainer.appendChild(product));
}

// Máscara para campos numéricos
function applyMasks() {
    // Máscara para CPF
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });
    }
    
    // Máscara para telefone
    const phoneInput = document.getElementById('telefone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
    }
    
    // Máscara para CEP
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
        
        // Buscar endereço pelo CEP
        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                buscarCEP(cep);
            }
        });
    }
}

// Buscar endereço pelo CEP
async function buscarCEP(cep) {
    try {
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const data = await response.json();
        
        if (!data.erro) {
            document.getElementById('rua').value = data.logradouro || '';
            document.getElementById('bairro').value = data.bairro || '';
            document.getElementById('cidade').value = data.localidade || '';
            document.getElementById('estado').value = data.uf || '';
        } else {
            showAlert('CEP não encontrado!', 'warning');
        }
    } catch (error) {
        showAlert('Erro ao buscar CEP', 'danger');
    }
}

// Inicializar funções quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    initCart();
    updateCartCount();
    
    // Adicionar evento aos botões "Adicionar ao Carrinho"
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = parseInt(this.getAttribute('data-id'));
            const productName = this.getAttribute('data-name');
            const price = parseFloat(this.getAttribute('data-price'));
            
            addToCart(productId, productName, price);
        });
    });
    
    // Buscar produtos
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                searchProducts();
            }
        });
    }
    
    // Ordenar produtos
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortProducts(this.value);
        });
    }
    
    // Calcular total do carrinho
    if (document.getElementById('subtotal') || document.getElementById('checkout-subtotal')) {
        calculateTotal();
    }
    
    // Aplicar máscaras
    applyMasks();
    
    // Adicionar efeito de loading aos botões de submit
    const submitButtons = document.querySelectorAll('button[type="submit"], .btn-primary[type="button"]');
    submitButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.type === 'submit' || this.classList.contains('btn-primary')) {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
                this.disabled = true;
                
                // Restaurar após 2 segundos (em produção, isso seria após o processamento real)
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);
            }
        });
    });
    
    // Adicionar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Função para carregar itens do carrinho na página do carrinho
function loadCartItems() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartItemsElement = document.getElementById('cart-items');
    
    if (!cartItemsElement) return;
    
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
        const itemTotal = item.price * item.quantity;
        html += `
            <div class="cart-item mb-3 p-3 border rounded">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <img src="https://via.placeholder.com/100x100/ff66b2/ffffff?text=${encodeURIComponent(item.name.substring(0, 10))}" 
                             class="img-fluid rounded" alt="${item.name}">
                    </div>
                    <div class="col-md-4">
                        <h6 class="mb-1">${item.name}</h6>
                        <p class="text-muted mb-0">Preço unitário: ${formatPrice(item.price)}</p>
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
                        <strong>${formatPrice(itemTotal)}</strong>
                    </div>
                    <div class="col-md-1 text-end">
                        <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})" 
                                data-bs-toggle="tooltip" title="Remover">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    cartItemsElement.innerHTML = html;
}

// Função para carregar resumo do pedido
function loadOrderSummary() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const orderSummary = document.getElementById('order-summary');
    
    if (!orderSummary) return;
    
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
                    <small class="text-muted">${item.quantity} x ${formatPrice(item.price)}</small>
                </div>
                <small>${formatPrice(itemTotal)}</small>
            </div>
        `;
    });
    
    orderSummary.innerHTML = html;
}