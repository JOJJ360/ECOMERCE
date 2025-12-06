<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'beauty_ecommerce');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configurações do site
define('SITE_NAME', 'Beauty Store');
define('SITE_URL', 'http://localhost/beauty_ecommerce');

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Função para verificar login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Função para verificar se é admin
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Função para formatar preço
function formatPrice($price) {
    return 'R$ ' . number_format($price, 2, ',', '.');
}

// Função para buscar produto por ID
function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Função para buscar usuário por email
function getUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

// Função para obter imagem do produto
function getProductImage($product_id, $product_name = '') {
    // Caminhos possíveis para imagens
    $image_paths = [
        "assets/images/produtos/{$product_id}.jpg",
        "assets/images/produtos/{$product_id}.png",
        "assets/images/produtos/produto-{$product_id}.jpg",
        "assets/images/produtos/produto-{$product_id}.png",
    ];
    
    foreach ($image_paths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }
    
    // Se não encontrar imagem, retorna placeholder
    return "https://via.placeholder.com/300x300/ff66b2/ffffff?text=" . urlencode(substr($product_name, 0, 20));
}
?>