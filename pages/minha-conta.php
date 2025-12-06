<?php
require_once '../includes/config.php';

// Verificar se está logado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Buscar pedidos do usuário
$user_id = $_SESSION['user_id'];
$orders = getUserOrders($user_id);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - Beauty Store</title>
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

    <!-- User Account -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-header bg-rosa-principal text-white">
                            <h5 class="mb-0"><i class="fas fa-user-circle"></i> Minha Conta</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user fa-4x text-muted"></i>
                            </div>
                            <h4><?php echo $_SESSION['user_nome']; ?></h4>
                            <p class="text-muted"><?php echo $_SESSION['user_email']; ?></p>
                            <hr>
                            <div class="list-group list-group-flush">
                                <a href="minha-conta.php" class="list-group-item list-group-item-action active">
                                    <i class="fas fa-shopping-bag"></i> Meus Pedidos
                                </a>
                                <a href="logout.php" class="list-group-item list-group-item-action">
                                    <i class="fas fa-sign-out-alt"></i> Sair
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-history"></i> Histórico de Pedidos</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($orders)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Você ainda não fez nenhum pedido</p>
                                    <a href="../index.php" class="btn btn-primary">
                                        <i class="fas fa-shopping-bag"></i> Começar a Comprar
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Pedido #</th>
                                                <th>Data</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): 
                                                $order_number = 'BS' . str_pad($order['id'], 6, '0', STR_PAD_LEFT);
                                                $status_class = [
                                                    'Pendente' => 'warning',
                                                    'Pago' => 'success',
                                                    'Processando' => 'info',
                                                    'Enviado' => 'primary',
                                                    'Entregue' => 'dark',
                                                    'Cancelado' => 'danger'
                                                ][$order['status']] ?? 'secondary';
                                            ?>
                                            <tr>
                                                <td><?php echo $order_number; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($order['data_criacao'])); ?></td>
                                                <td><?php echo formatPrice($order['total']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $status_class; ?>">
                                                        <?php echo $order['status']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#orderModal<?php echo $order['id']; ?>">
                                                        <i class="fas fa-eye"></i> Detalhes
                                                    </button>
                                                </td>
                                            </tr>
                                            
                                            <!-- Modal -->
                                            <div class="modal fade" id="orderModal<?php echo $order['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Pedido <?php echo $order_number; ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6>Informações do Pedido</h6>
                                                                    <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($order['data_criacao'])); ?></p>
                                                                    <p><strong>Total:</strong> <?php echo formatPrice($order['total']); ?></p>
                                                                    <p><strong>Status:</strong> 
                                                                        <span class="badge bg-<?php echo $status_class; ?>">
                                                                            <?php echo $order['status']; ?>
                                                                        </span>
                                                                    </p>
                                                                    <p><strong>Pagamento:</strong> <?php echo ucfirst($order['metodo_pagamento']); ?></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h6>Itens do Pedido</h6>
                                                                    <?php
                                                                    $items = getOrderItems($order['id']);
                                                                    foreach ($items as $item):
                                                                    ?>
                                                                    <div class="d-flex mb-2">
                                                                        <div class="flex-shrink-0">
                                                                            <img src="https://via.placeholder.com/50x50/ff66b2/ffffff?text=P" 
                                                                                 class="rounded" alt="<?php echo $item['produto_nome']; ?>">
                                                                        </div>
                                                                        <div class="flex-grow-1 ms-3">
                                                                            <h6 class="mt-0"><?php echo $item['produto_nome']; ?></h6>
                                                                            <small><?php echo $item['quantidade']; ?> x R$ <?php echo number_format($item['valor_unitario'], 2, ',', '.'); ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>