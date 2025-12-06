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
        $categoria_id = $_POST['categoria_id'];
        $preco = $_POST['preco'];
        $estoque = $_POST['estoque'];
        $descricao = $_POST['descricao'];
        $destacado = isset($_POST['destacado']) ? 1 : 0;
        
        $stmt = $pdo->prepare("INSERT INTO products (nome, categoria_id, preco, estoque, descricao, destacado) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $categoria_id, $preco, $estoque, $descricao, $destacado]);
        $success = "Produto adicionado com sucesso!";
    }
    
    if (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $categoria_id = $_POST['categoria_id'];
        $preco = $_POST['preco'];
        $estoque = $_POST['estoque'];
        $descricao = $_POST['descricao'];
        $destacado = isset($_POST['destacado']) ? 1 : 0;
        
        $stmt = $pdo->prepare("UPDATE products SET nome = ?, categoria_id = ?, preco = ?, estoque = ?, 
                              descricao = ?, destacado = ? WHERE id = ?");
        $stmt->execute([$nome, $categoria_id, $preco, $estoque, $descricao, $destacado, $id]);
        $success = "Produto atualizado com sucesso!";
    }
    
    if (isset($_POST['excluir'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Produto excluído com sucesso!";
    }
}

// Buscar produtos
$stmt = $pdo->query("SELECT p.*, c.nome as categoria_nome 
                     FROM products p 
                     LEFT JOIN categories c ON p.categoria_id = c.id 
                     ORDER BY p.id DESC");
$produtos = $stmt->fetchAll();

// Buscar categorias
$categorias = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos - Beauty Store</title>
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
        .table-actions {
            white-space: nowrap;
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
                    <a class="nav-link active" href="produtos.php">
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
                <h2>Gerenciar Produtos</h2>
                
                <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Formulário para adicionar produto -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-plus"></i> Adicionar Novo Produto</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nome" class="form-label">Nome do Produto *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="categoria_id" class="form-label">Categoria *</label>
                                    <select class="form-select" id="categoria_id" name="categoria_id" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach($categorias as $categoria): ?>
                                        <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nome']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="preco" class="form-label">Preço (R$) *</label>
                                    <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="estoque" class="form-label">Estoque *</label>
                                    <input type="number" class="form-control" id="estoque" name="estoque" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Destacado</label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="destacado" name="destacado">
                                        <label class="form-check-label" for="destacado">
                                            Exibir na página inicial
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                                </div>
                            </div>
                            <button type="submit" name="adicionar" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Produto
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Lista de produtos -->
                <div class="card shadow">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Produtos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Categoria</th>
                                        <th>Preço</th>
                                        <th>Estoque</th>
                                        <th>Destacado</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($produtos as $produto): ?>
                                    <tr>
                                        <td><?php echo $produto['id']; ?></td>
                                        <td><?php echo $produto['nome']; ?></td>
                                        <td><?php echo $produto['categoria_nome']; ?></td>
                                        <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $produto['estoque'] > 10 ? 'success' : ($produto['estoque'] > 0 ? 'warning' : 'danger'); ?>">
                                                <?php echo $produto['estoque']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($produto['destacado']): ?>
                                            <i class="fas fa-star text-warning"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end table-actions">
                                            <!-- Formulário para editar -->
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
                                                <input type="hidden" name="nome" value="<?php echo $produto['nome']; ?>">
                                                <input type="hidden" name="categoria_id" value="<?php echo $produto['categoria_id']; ?>">
                                                <input type="hidden" name="preco" value="<?php echo $produto['preco']; ?>">
                                                <input type="hidden" name="estoque" value="<?php echo $produto['estoque']; ?>">
                                                <input type="hidden" name="descricao" value="<?php echo $produto['descricao']; ?>">
                                                <input type="hidden" name="destacado" value="<?php echo $produto['destacado']; ?>">
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal<?php echo $produto['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Formulário para excluir -->
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
                                                <button type="submit" name="excluir" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal de Edição -->
                                    <div class="modal fade" id="editModal<?php echo $produto['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form method="POST" action="">
                                                    <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Editar Produto</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="nome_edit_<?php echo $produto['id']; ?>" class="form-label">Nome *</label>
                                                                <input type="text" class="form-control" id="nome_edit_<?php echo $produto['id']; ?>" 
                                                                       name="nome" value="<?php echo $produto['nome']; ?>" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="categoria_id_edit_<?php echo $produto['id']; ?>" class="form-label">Categoria *</label>
                                                                <select class="form-select" id="categoria_id_edit_<?php echo $produto['id']; ?>" 
                                                                        name="categoria_id" required>
                                                                    <?php foreach($categorias as $categoria): ?>
                                                                    <option value="<?php echo $categoria['id']; ?>" 
                                                                            <?php echo $produto['categoria_id'] == $categoria['id'] ? 'selected' : ''; ?>>
                                                                        <?php echo $categoria['nome']; ?>
                                                                    </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label for="preco_edit_<?php echo $produto['id']; ?>" class="form-label">Preço *</label>
                                                                <input type="number" step="0.01" class="form-control" 
                                                                       id="preco_edit_<?php echo $produto['id']; ?>" name="preco" 
                                                                       value="<?php echo $produto['preco']; ?>" required>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label for="estoque_edit_<?php echo $produto['id']; ?>" class="form-label">Estoque *</label>
                                                                <input type="number" class="form-control" id="estoque_edit_<?php echo $produto['id']; ?>" 
                                                                       name="estoque" value="<?php echo $produto['estoque']; ?>" required>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label">Destacado</label>
                                                                <div class="form-check mt-2">
                                                                    <input class="form-check-input" type="checkbox" 
                                                                           id="destacado_edit_<?php echo $produto['id']; ?>" name="destacado"
                                                                           <?php echo $produto['destacado'] ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="destacado_edit_<?php echo $produto['id']; ?>">
                                                                        Exibir na página inicial
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label for="descricao_edit_<?php echo $produto['id']; ?>" class="form-label">Descrição</label>
                                                                <textarea class="form-control" id="descricao_edit_<?php echo $produto['id']; ?>" 
                                                                          name="descricao" rows="3"><?php echo $produto['descricao']; ?></textarea>
                                                            </div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>