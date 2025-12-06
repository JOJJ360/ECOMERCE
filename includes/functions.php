<?php
require_once 'config.php';

// Função para registrar usuário
function registerUser($nome, $email, $senha) {
    global $pdo;
    $hashed_password = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha) VALUES (?, ?, ?)");
    return $stmt->execute([$nome, $email, $hashed_password]);
}

// Função para criar pedido
function createOrder($cliente_id, $total, $metodo_pagamento, $endereco) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO orders (cliente_id, total, metodo_pagamento, endereco_entrega) VALUES (?, ?, ?, ?)");
    $stmt->execute([$cliente_id, $total, $metodo_pagamento, $endereco]);
    return $pdo->lastInsertId();
}

// Função para obter a URL da imagem do produto
function getProductImage($product_id, $product_name) {
    $image_path = "assets/images/{$product_id}.png";
    
    // Verificar se a imagem existe
    if (file_exists($image_path)) {
        return $image_path;
    } else {
        // Se não existir, usar placeholder com o nome do produto
        return "https://via.placeholder.com/300x300/ff66b2/ffffff?text=" . urlencode(substr($product_name, 0, 20));
    }
}

// Função para adicionar item ao pedido
function addOrderItem($pedido_id, $produto_id, $quantidade, $valor_unitario) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO order_items (pedido_id, produto_id, quantidade, valor_unitario) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$pedido_id, $produto_id, $quantidade, $valor_unitario]);
}

// Função para atualizar estoque
function updateStock($product_id, $quantity) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE products SET estoque = estoque - ? WHERE id = ?");
    return $stmt->execute([$quantity, $product_id]);
}

// Função para buscar pedidos do usuário
function getUserOrders($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE cliente_id = ? ORDER BY data_criacao DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Função para buscar itens do pedido
function getOrderItems($order_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT oi.*, p.nome as produto_nome, p.imagem 
                          FROM order_items oi 
                          JOIN products p ON oi.produto_id = p.id 
                          WHERE oi.pedido_id = ?");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll();
}
?>