<?php
require_once '../includes/config.php';

// Verificar se é administrador
if (!isAdmin()) {
    header('Location: ../pages/login.php');
    exit();
}

// Atualizar status do pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_status'])) {
    $pedido_id = $_POST['pedido_id'];
    $novo_status = $_POST['novo_status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$novo_status, $pedido_id]);
    
    $success = "Status do pedido atualizado com sucesso!";
}

// Buscar pedidos
$stmt = $pdo->query("SELECT o.*, u.nome as cliente_nome, u.email as cliente_email 
                     FROM orders o 
                     JOIN users u ON o.cliente_id = u.id 
                     ORDER BY o.data_criacao DESC");
$pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pedidos - Beauty Store</title>
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
        .status-badge {
            cursor: pointer;
        }
        .order-details {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
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
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="produtos.php">
                        <i class="fas fa-box"></i> Produtos
                    </a>
                    <a class="nav-link active" href="pedidos.php">
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
                <h2>Gerenciar Pedidos</h2>
                
                <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card shadow">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Todos os Pedidos</h5>
                        <span class="badge bg-primary"><?php echo count($pedidos); ?> pedidos</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pedidos)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Nenhum pedido encontrado.</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Pedido #</th>
                                        <th>Cliente</th>
                                        <th>Data</th>
                                        <th>Total</th>
                                        <th>Pagamento</th>
                                        <th>Status</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pedidos as $pedido): 
                                        $numero_pedido = 'BS' . str_pad($pedido['id'], 6, '0', STR_PAD_LEFT);
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
                                        <td><?php echo $numero_pedido; ?></td>
                                        <td>
                                            <div><?php echo $pedido['cliente_nome']; ?></div>
                                            <small class="text-muted"><?php echo $pedido['cliente_email']; ?></small>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_criacao'])); ?></td>
                                        <td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                                        <td><?php echo ucfirst($pedido['metodo_pagamento']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $status_colors[$pedido['status']] ?? 'secondary'; ?> status-badge"
                                                  data-bs-toggle="modal" 
                                                  data-bs-target="#statusModal<?php echo $pedido['id']; ?>">
                                                <?php echo $pedido['status']; ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#details<?php echo $pedido['id']; ?>">
                                                <i class="fas fa-eye"></i> Detalhes
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Detalhes do pedido -->
                                    <tr class="collapse" id="details<?php echo $pedido['id']; ?>">
                                        <td colspan="7">
                                            <div class="order-details">
                                                <h6>Detalhes do Pedido <?php echo $numero_pedido; ?></h6>
                                                
                                                <!-- Itens do pedido -->
                                                <?php
                                                $stmt = $pdo->prepare("SELECT oi.*, p.nome as produto_nome 
                                                                      FROM order_items oi 
                                                                      JOIN products p ON oi.produto_id = p.id 
                                                                      WHERE oi.pedido_id = ?");
                                                $stmt->execute([$pedido['id']]);
                                                $itens = $stmt->fetchAll();
                                                ?>
                                                
                                                <?php if (!empty($itens)): ?>
                                                <div class="table-responsive mt-3">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Produto</th>
                                                                <th>Quantidade</th>
                                                                <th>Valor Unitário</th>
                                                                <th>Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($itens as $item): ?>
                                                            <tr>
                                                                <td><?php echo $item['produto_nome']; ?></td>
                                                                <td><?php echo $item['quantidade']; ?></td>
                                                                <td>R$ <?php echo number_format($item['valor_unitario'], 2, ',', '.'); ?></td>
                                                                <td>R$ <?php echo number_format($item['quantidade'] * $item['valor_unitario'], 2, ',', '.'); ?></td>
                                                            </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <!-- Endereço de entrega -->
                                                <?php if (!empty($pedido['endereco_entrega'])): 
                                                    $endereco = json_decode($pedido['endereco_entrega'], true);
                                                ?>
                                                <div class="mt-3">
                                                    <h6>Endereço de Entrega:</h6>
                                                    <p class="mb-0">
                                                        <?php echo $endereco['rua'] ?? ''; ?>, 
                                                        <?php echo $endereco['numero'] ?? ''; ?><br>
                                                        <?php if (!empty($endereco['complemento'])): ?>
                                                        Complemento: <?php echo $endereco['complemento']; ?><br>
                                                        <?php endif; ?>
                                                        <?php echo $endereco['bairro'] ?? ''; ?> - 
                                                        <?php echo $endereco['cidade'] ?? ''; ?>/<?php echo $endereco['estado'] ?? ''; ?><br>
                                                        CEP: <?php echo $endereco['cep'] ?? ''; ?>
                                                    </p>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal para alterar status -->
                                    <div class="modal fade" id="statusModal<?php echo $pedido['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="">
                                                    <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Alterar Status do Pedido</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="novo_status_<?php echo $pedido['id']; ?>" class="form-label">Novo Status</label>
                                                            <select class="form-select" id="novo_status_<?php echo $pedido['id']; ?>" 
                                                                    name="novo_status" required>
                                                                <option value="Pendente" <?php echo $pedido['status'] == 'Pendente' ? 'selected' : ''; ?>>Pendente</option>
                                                                <option value="Pago" <?php echo $pedido['status'] == 'Pago' ? 'selected' : ''; ?>>Pago</option>
                                                                <option value="Processando" <?php echo $pedido['status'] == 'Processando' ? 'selected' : ''; ?>>Processando</option>
                                                                <option value="Enviado" <?php echo $pedido['status'] == 'Enviado' ? 'selected' : ''; ?>>Enviado</option>
                                                                <option value="Entregue" <?php echo $pedido['status'] == 'Entregue' ? 'selected' : ''; ?>>Entregue</option>
                                                                <option value="Cancelado" <?php echo $pedido['status'] == 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" name="atualizar_status" class="btn btn-primary">Salvar</button>
                                                    </div>
                                                </form>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>