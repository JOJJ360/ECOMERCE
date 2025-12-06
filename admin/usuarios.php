<?php
require_once '../includes/config.php';

// Verificar se é administrador
if (!isAdmin()) {
    header('Location: ../pages/login.php');
    exit();
}

// Buscar usuários
$usuarios = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Beauty Store</title>
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
        .user-type-badge {
            font-size: 0.8rem;
            padding: 3px 8px;
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
                    <a class="nav-link" href="pedidos.php">
                        <i class="fas fa-shopping-cart"></i> Pedidos
                    </a>
                    <a class="nav-link" href="categorias.php">
                        <i class="fas fa-tags"></i> Categorias
                    </a>
                    <a class="nav-link active" href="usuarios.php">
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
                <h2>Gerenciar Usuários</h2>
                
                <div class="card shadow">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Todos os Usuários</h5>
                        <span class="badge bg-primary"><?php echo count($usuarios); ?> usuários</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($usuarios)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Nenhum usuário cadastrado.</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Tipo</th>
                                        <th>Data de Cadastro</th>
                                        <th>Pedidos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($usuarios as $usuario): 
                                        // Contar pedidos do usuário
                                        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE cliente_id = ?");
                                        $stmt->execute([$usuario['id']]);
                                        $total_pedidos = $stmt->fetch()['total'];
                                    ?>
                                    <tr>
                                        <td><?php echo $usuario['id']; ?></td>
                                        <td>
                                            <div><?php echo $usuario['nome']; ?></div>
                                            <?php if ($usuario['id'] == $_SESSION['user_id']): ?>
                                            <small class="text-primary"><i class="fas fa-user-check"></i> Você</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $usuario['email']; ?></td>
                                        <td>
                                            <?php if ($usuario['tipo'] == 'admin'): ?>
                                            <span class="badge bg-danger user-type-badge">
                                                <i class="fas fa-crown"></i> Administrador
                                            </span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary user-type-badge">
                                                <i class="fas fa-user"></i> Cliente
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($usuario['created_at'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $total_pedidos > 0 ? 'success' : 'light text-dark'; ?>">
                                                <?php echo $total_pedidos; ?> pedido(s)
                                            </span>
                                        </td>
                                    </tr>
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