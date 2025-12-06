<?php
require_once '../includes/config.php';

// Verificar se o ID do produto foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$product_id = intval($_GET['id']);
$product = getProductById($product_id);

// Verificar se o produto existe
if (!$product) {
    header('Location: ../index.php');
    exit();
}

// Buscar categoria do produto
$stmt = $pdo->prepare("SELECT nome FROM categories WHERE id = ?");
$stmt->execute([$product['categoria_id']]);
$categoria = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['nome']; ?> - Beauty Store</title>
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
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </nav>
    </header>

    <!-- Product Detail -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow">
                        <img src="https://via.placeholder.com/600x600/ff66b2/ffffff?text=<?php echo urlencode(substr($product['nome'], 0, 30)); ?>" 
                             class="card-img-top" alt="<?php echo $product['nome']; ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow border-0">
                        <div class="card-body">
                            <span class="badge bg-primary mb-3"><?php echo $categoria ? $categoria['nome'] : 'Sem categoria'; ?></span>
                            
                            <h1 class="mb-3"><?php echo $product['nome']; ?></h1>
                            <p class="lead price mb-4"><?php echo formatPrice($product['preco']); ?></p>
                            
                            <div class="mb-4">
                                <h5>Descrição</h5>
                                <p><?php echo $product['descricao']; ?></p>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6><i class="fas fa-box"></i> Estoque</h6>
                                            <p class="mb-0"><?php echo $product['estoque']; ?> unidades disponíveis</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6><i class="fas fa-truck"></i> Entrega</h6>
                                            <p class="mb-0">Entrega em 5-7 dias úteis</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg add-to-cart" 
                                        data-id="<?php echo $product['id']; ?>"
                                        data-name="<?php echo $product['nome']; ?>"
                                        data-price="<?php echo $product['preco']; ?>">
                                    <i class="fas fa-cart-plus"></i> Adicionar ao Carrinho
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <section class="py-5 bg-light">
        <div class="container">
            <h3 class="mb-4">Produtos Relacionados</h3>
            <div class="row">
                <?php
                $stmt = $pdo->prepare("SELECT * FROM products WHERE categoria_id = ? AND id != ? LIMIT 4");
                $stmt->execute([$product['categoria_id'], $product['id']]);
                $related_products = $stmt->fetchAll();
                
                foreach ($related_products as $related):
                ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow">
                        <img src="https://via.placeholder.com/300x300/ff66b2/ffffff?text=<?php echo urlencode(substr($related['nome'], 0, 20)); ?>" 
                             class="card-img-top" alt="<?php echo $related['nome']; ?>">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo $related['nome']; ?></h6>
                            <p class="price"><?php echo formatPrice($related['preco']); ?></p>
                            <button class="btn btn-primary btn-sm add-to-cart" 
                                    data-id="<?php echo $related['id']; ?>"
                                    data-name="<?php echo $related['nome']; ?>"
                                    data-price="<?php echo $related['preco']; ?>">
                                <i class="fas fa-cart-plus"></i> Adicionar
                            </button>
                            <a href="produto-detalhe.php?id=<?php echo $related['id']; ?>" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>