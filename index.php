<?php
require_once 'includes/config.php';

// Buscar produtos
$stmt = $pdo->query("SELECT p.*, c.nome as categoria_nome 
                     FROM products p 
                     LEFT JOIN categories c ON p.categoria_id = c.id
                     ORDER BY p.destacado DESC, p.created_at DESC");
$produtos = $stmt->fetchAll();

// Buscar produtos em destaque
$stmt_destaques = $pdo->query("SELECT * FROM products WHERE destacado = 1 LIMIT 4");
$destaques = $stmt_destaques->fetchAll();

// Buscar categorias
$stmt_categorias = $pdo->query("SELECT * FROM categories");
$categorias = $stmt_categorias->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Produtos de Beleza Feminina</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-spa"></i> Beauty Store
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">Home</a>
                        </li>
                        <?php foreach($categorias as $categoria): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?categoria=<?php echo $categoria['id']; ?>">
                                <?php echo $categoria['nome']; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="d-flex">
                        <form class="d-flex me-3">
                            <input class="form-control me-2" type="search" placeholder="Buscar produtos..." id="search-input">
                            <button class="btn btn-outline-light" type="button" onclick="searchProducts()">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-sort"></i> Ordenar
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="sortProducts('price-asc')">Menor Preço</a></li>
                                <li><a class="dropdown-item" href="#" onclick="sortProducts('price-desc')">Maior Preço</a></li>
                                <li><a class="dropdown-item" href="#" onclick="sortProducts('name-asc')">A-Z</a></li>
                                <li><a class="dropdown-item" href="#" onclick="sortProducts('name-desc')">Z-A</a></li>
                            </ul>
                        </div>
                        
                        <a href="pages/carrinho.php" class="btn btn-outline-light me-3 position-relative">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                                0
                            </span>
                        </a>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user"></i> <?php echo $_SESSION['user_nome']; ?>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="pages/minha-conta.php"><i class="fas fa-user-circle"></i> Minha Conta</a></li>
                                    <li><a class="dropdown-item" href="pages/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="pages/login.php" class="btn btn-light">
                                <i class="fas fa-sign-in-alt"></i> Entrar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Banner Principal -->
    <section class="bg-rosa-claro text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold">Beleza que Transforma</h1>
                    <p class="lead">Encontre os melhores produtos de beleza feminina com qualidade e entrega rápida.</p>
                    <a href="#produtos" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-bag"></i> Comprar Agora
                    </a>
                </div>
                <div class="col-md-6">
                    <img src="https://via.placeholder.com/600x400/ff66b2/ffffff?text=Beauty+Store" 
                         alt="Produtos de Beleza" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Produtos em Destaque -->
    <section class="featured-products py-5">
        <div class="container">
            <h2 class="text-center mb-5"><i class="fas fa-star"></i> Produtos em Destaque</h2>
            <div class="row">
                <?php foreach ($destaques as $produto): ?>
                <div class="col-md-3 mb-4 product-card" data-price="<?php echo $produto['preco']; ?>">
                    <div class="card h-100 shadow">
                        <img src="https://via.placeholder.com/300x300/ff66b2/ffffff?text=<?php echo urlencode(substr($produto['nome'], 0, 20)); ?>" 
                             class="card-img-top" alt="<?php echo $produto['nome']; ?>">
                        <div class="card-body">
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">Destaque</span>
                            <h5 class="card-title"><?php echo $produto['nome']; ?></h5>
                            <p class="card-text"><?php echo substr($produto['descricao'], 0, 60); ?>...</p>
                            <p class="price"><?php echo 'R$ ' . number_format($produto['preco'], 2, ',', '.'); ?></p>
                            <button class="btn btn-primary add-to-cart" 
                                    data-id="<?php echo $produto['id']; ?>"
                                    data-name="<?php echo $produto['nome']; ?>"
                                    data-price="<?php echo $produto['preco']; ?>">
                                <i class="fas fa-cart-plus"></i> Adicionar
                            </button>
                            <a href="pages/produto-detalhe.php?id=<?php echo $produto['id']; ?>" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Todos os Produtos -->
    <section id="produtos" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5"><i class="fas fa-shopping-bag"></i> Nossos Produtos</h2>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <select class="form-select" id="sort-select">
                        <option value="">Ordenar por</option>
                        <option value="price-asc">Menor Preço</option>
                        <option value="price-desc">Maior Preço</option>
                        <option value="name-asc">Nome A-Z</option>
                        <option value="name-desc">Nome Z-A</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <select class="form-select" id="category-filter">
                        <option value="">Todas as Categorias</option>
                        <?php foreach($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>">
                            <?php echo $categoria['nome']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="row" id="product-container">
                <?php foreach ($produtos as $produto): ?>
                <div class="col-md-3 mb-4 product-card" 
                     data-price="<?php echo $produto['preco']; ?>"
                     data-category="<?php echo $produto['categoria_id']; ?>">
                    <div class="card h-100 shadow">
                        <img src="https://via.placeholder.com/300x300/ff66b2/ffffff?text=<?php echo urlencode(substr($produto['nome'], 0, 20)); ?>" 
                             class="card-img-top" alt="<?php echo $produto['nome']; ?>">
                        <div class="card-body">
                            <span class="badge bg-primary mb-2"><?php echo $produto['categoria_nome']; ?></span>
                            <h5 class="card-title"><?php echo $produto['nome']; ?></h5>
                            <p class="card-text"><?php echo substr($produto['descricao'], 0, 60); ?>...</p>
                            <p class="price"><?php echo 'R$ ' . number_format($produto['preco'], 2, ',', '.'); ?></p>
                            <p class="text-muted"><small>Estoque: <?php echo $produto['estoque']; ?> unidades</small></p>
                            <button class="btn btn-primary add-to-cart" 
                                    data-id="<?php echo $produto['id']; ?>"
                                    data-name="<?php echo $produto['nome']; ?>"
                                    data-price="<?php echo $produto['preco']; ?>">
                                <i class="fas fa-cart-plus"></i> Adicionar
                            </button>
                            <a href="pages/produto-detalhe.php?id=<?php echo $produto['id']; ?>" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> Detalhes
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-spa"></i> Beauty Store</h5>
                    <p>Sua loja online de produtos de beleza feminina de qualidade.</p>
                </div>
                <div class="col-md-4">
                    <h5>Links Rápidos</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="pages/carrinho.php"><i class="fas fa-shopping-cart"></i> Carrinho</a></li>
                        <li><a href="pages/login.php"><i class="fas fa-user"></i> Minha Conta</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contato</h5>
                    <p><i class="fas fa-envelope"></i> contato@beautystore.com</p>
                    <p><i class="fas fa-phone"></i> (11) 99999-9999</p>
                </div>
            </div>
            <hr class="mt-4 mb-4">
            <div class="text-center">
                <p>&copy; 2024 Beauty Store. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    
    <script>
    // Filtrar por categoria
    document.getElementById('category-filter').addEventListener('change', function() {
        const selectedCategory = this.value;
        const products = document.querySelectorAll('.product-card');
        
        products.forEach(product => {
            if (!selectedCategory || product.getAttribute('data-category') === selectedCategory) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>