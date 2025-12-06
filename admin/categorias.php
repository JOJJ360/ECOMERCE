<?php
require_once '../includes/config.php';

// Verificar se é administrador
if (!isAdmin()) {
    header('Location: ../pages/login.php');
    exit();
}

// Ações CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adicionar'])) {
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'] ?? '';
        
        $stmt = $pdo->prepare("INSERT INTO categories (nome, descricao) VALUES (?, ?)");
        $stmt->execute([$nome, $descricao]);
        $success = "Categoria adicionada com sucesso!";
    }
    
    if (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'] ?? '';
        
        $stmt = $pdo->prepare("UPDATE categories SET nome = ?, descricao = ? WHERE id = ?");
        $stmt->execute([$nome, $descricao, $id]);
        $success = "Categoria atualizada com sucesso!";
    }
    
    if (isset($_POST['excluir'])) {
        $id = $_POST['id'];
        
        // Verificar se a categoria tem produtos
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE categoria_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['total'] > 0) {
            $error = "Não é possível excluir esta categoria pois existem produtos vinculados a ela.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Categoria excluída com sucesso!";
        }
    }
}

// Buscar categorias
$categorias = $pdo->query("SELECT * FROM categories ORDER BY nome")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias - Beauty Store</title>
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
                    <a class="nav-link active" href="categorias.php">
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
                <h2>Gerenciar Categorias</h2>
                
                <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Formulário para adicionar categoria -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-plus"></i> Adicionar Nova Categoria</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nome" class="form-label">Nome da Categoria *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <input type="text" class="form-control" id="descricao" name="descricao">
                                </div>
                            </div>
                            <button type="submit" name="adicionar" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Categoria
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Lista de categorias -->
                <div class="card shadow">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Categorias</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($categorias)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Nenhuma categoria cadastrada.</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Descrição</th>
                                        <th>Produtos</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($categorias as $categoria): 
                                        // Contar produtos na categoria
                                        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE categoria_id = ?");
                                        $stmt->execute([$categoria['id']]);
                                        $total_produtos = $stmt->fetch()['total'];
                                    ?>
                                    <tr>
                                        <td><?php echo $categoria['id']; ?></td>
                                        <td>
                                            <strong><?php echo $categoria['nome']; ?></strong>
                                        </td>
                                        <td><?php echo $categoria['descricao'] ?: '<span class="text-muted">Sem descrição</span>'; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $total_produtos > 0 ? 'primary' : 'secondary'; ?>">
                                                <?php echo $total_produtos; ?> produto(s)
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <!-- Formulário para editar -->
                                            <button class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal<?php echo $categoria['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <!-- Formulário para excluir -->
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                                                <button type="submit" name="excluir" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Tem certeza que deseja excluir esta categoria?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal de Edição -->
                                    <div class="modal fade" id="editModal<?php echo $categoria['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="">
                                                    <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Editar Categoria</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="nome_edit_<?php echo $categoria['id']; ?>" class="form-label">Nome *</label>
                                                            <input type="text" class="form-control" id="nome_edit_<?php echo $categoria['id']; ?>" 
                                                                   name="nome" value="<?php echo $categoria['nome']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="descricao_edit_<?php echo $categoria['id']; ?>" class="form-label">Descrição</label>
                                                            <input type="text" class="form-control" id="descricao_edit_<?php echo $categoria['id']; ?>" 
                                                                   name="descricao" value="<?php echo $categoria['descricao']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" name="editar" class="btn btn-primary">Salvar Alterações</button>
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