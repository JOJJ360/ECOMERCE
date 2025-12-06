<?php
require_once '../includes/config.php';

// Gerar número de pedido aleatório
$numero_pedido = 'BS' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado - Beauty Store</title>
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
            </div>
        </nav>
    </header>

    <!-- Success Message -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <div class="card shadow border-0">
                        <div class="card-body py-5">
                            <div class="mb-4">
                                <div class="success-icon mb-3">
                                    <i class="fas fa-check-circle fa-5x text-success"></i>
                                </div>
                                <h1 class="text-success">Pedido Confirmado!</h1>
                                <p class="lead">Obrigado por comprar na Beauty Store</p>
                            </div>
                            
                            <div class="alert alert-success mb-4">
                                <h4 class="alert-heading">
                                    <i class="fas fa-receipt"></i> Número do Pedido: <?php echo $numero_pedido; ?>
                                </h4>
                                <p>Seu pedido foi recebido com sucesso e está sendo processado.</p>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5><i class="fas fa-shipping-fast"></i> Entrega</h5>
                                            <p class="mb-0">Previsão de entrega: 5-7 dias úteis</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5><i class="fas fa-envelope"></i> Confirmação</h5>
                                            <p class="mb-0">Enviaremos um email com os detalhes</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-center">
                                <a href="../index.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-home"></i> Voltar à Loja
                                </a>
                                <a href="minha-conta.php" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-user"></i> Meus Pedidos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>