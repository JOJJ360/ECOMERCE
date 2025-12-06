<?php
require_once '../includes/config.php';

// Verificar se é administrador
if (!isAdmin()) {
    header('Location: ../pages/login.php');
    exit();
}

// Estatísticas
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$total_produtos = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$total_pedidos = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE tipo = 'cliente'");
$total_clientes = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(total) as total FROM orders WHERE status = 'Pago'");
$total_vendas = $stmt->fetch()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Beauty Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: white;
            padding: 15px 20px;
            margin: 5px 0;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.2);
        }
        .stat-card {
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            color: white;
            transition: transform 0.3s;
            border: none;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .stat-card i {
            font-size: 2.5rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4 text-white">
                    <h4><i class="fas fa-spa"></i> Beauty Store</h4>
                    <p class="mb-0">Painel Administrativo</p>
                </div>
                <nav class="nav flex-column p-3">
                    <a class="nav-link active" href="index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="produtos.php">
                        <i class="fas fa-box"></i> Produtos
                    </a>
                    <a class="nav-link" href="pedidos.php">
                        <i class="fas fa-shopping-cart"></i> Pedidos
                    </a>
                    <a class="nav-link" href="categorias.php">
                        <i class="fas fa-tags"></i> Categorias
                    </a>
                    <a class="nav-link" href="usuarios.php">
                        <i class="fas fa-users"></i> Usuários
                    </a>
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-store"></i> Ver Loja
                    </a>
                    <a class="nav-link" href="../pages/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
                    <span class="text-muted"><?php echo date('d/m/Y'); ?></span>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="stat-card stat-card-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3><?php echo $total_produtos; ?></h3>
                                    <p>Produtos</p>
                                </div>
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card stat-card-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3><?php echo $total_pedidos; ?></h3>
                                    <p>Pedidos</p>
                                </div>
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card stat-card-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3><?php echo $total_clientes; ?></h3>
                                    <p>Clientes</p>
                                </div>
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card stat-card-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3>R$ <?php echo number_format($total_vendas, 2, ',', '.'); ?></h3>
                                    <p>Vendas Totais</p>
                                </div>
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Últimos Pedidos</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->query("SELECT o.*, u.nome as cliente 
                                                    FROM orders o 
                                                    JOIN users u ON o.cliente_id = u.id 
                                                    ORDER BY o.data_criacao DESC LIMIT 5");
                                $pedidos = $stmt->fetchAll();
                                
                                if ($pedidos):
                                ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pedidos as $pedido): 
                                                $status_colors = [
                                                    'Pendente' => 'warning',
                                                    'Pago' => 'success',
                                                    'Processando' => 'info',
                                                    'Enviado' => 'primary',
                                                    'Entregue' => 'dark',
                                                    'Cancelado' => 'danger'
                                                ];
                                            ?>
                                            <tr>
                                                <td>#<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                                <td><?php echo $pedido['cliente']; ?></td>
                                                <td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $status_colors[$pedido['status']] ?? 'secondary'; ?>">
                                                        <?php echo $pedido['status']; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <p class="text-muted">Nenhum pedido encontrado.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Produtos com Baixo Estoque</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->query("SELECT * FROM products WHERE estoque < 10 ORDER BY estoque ASC LIMIT 5");
                                $produtos = $stmt->fetchAll();
                                
                                if ($produtos):
                                ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Produto</th>
                                                <th>Estoque</th>
                                                <th>Preço</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($produtos as $produto): ?>
                                            <tr>
                                                <td><?php echo $produto['nome']; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $produto['estoque'] < 5 ? 'danger' : 'warning'; ?>">
                                                        <?php echo $produto['estoque']; ?>
                                                    </span>
                                                </td>
                                                <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <p class="text-muted">Todos os produtos com estoque adequado.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>