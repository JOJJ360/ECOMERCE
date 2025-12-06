-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS beauty_ecommerce;
USE beauty_ecommerce;

-- Tabela de usuários
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'cliente') DEFAULT 'cliente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de categorias
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT
);

-- Tabela de produtos
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT,
    nome VARCHAR(100) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL,
    imagem VARCHAR(255),
    descricao TEXT,
    destacado BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categories(id)
);

-- Tabela de pedidos
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('Pendente', 'Pago', 'Processando', 'Enviado', 'Entregue', 'Cancelado') DEFAULT 'Pendente',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_pagamento TIMESTAMP NULL,
    metodo_pagamento ENUM('cartao', 'pix', 'boleto'),
    endereco_entrega TEXT,
    FOREIGN KEY (cliente_id) REFERENCES users(id)
);

-- Tabela de itens do pedido
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    produto_id INT,
    quantidade INT NOT NULL,
    valor_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES orders(id),
    FOREIGN KEY (produto_id) REFERENCES products(id)
);

-- Inserir categorias
INSERT INTO categories (nome, descricao) VALUES
('Maquiagem', 'Produtos para maquiagem'),
('Cuidados com a Pele', 'Produtos para skincare'),
('Cabelos', 'Produtos para cuidados capilares'),
('Perfumaria', 'Perfumes e colônias'),
('Unhas', 'Esmaltes e cuidados para unhas'),
('Corpo e Banho', 'Produtos para o corpo'),
('Acessórios', 'Acessórios de beleza');

-- Inserir usuário administrador (senha: admin123)
INSERT INTO users (nome, email, senha, tipo) VALUES
('Administrador', 'admin@beauty.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Cliente Teste', 'cliente@teste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente');

-- Inserir 20 produtos de beleza feminina
INSERT INTO products (categoria_id, nome, preco, estoque, imagem, descricao, destacado) VALUES
-- Maquiagem
(1, 'Batom Líquido Matte', 49.90, 100, 'batom-matte.jpg', 'Batom líquido de longa duração com acabamento mate', true),
(1, 'Paleta de Sombras Profissionais', 129.90, 50, 'paleta-sombras.jpg', 'Paleta com 12 cores intensas e pigmentadas', true),
(1, 'Base Líquida Alta Cobertura', 89.90, 75, 'base-liquida.jpg', 'Base que uniformiza a pele sem pesar', false),
(1, 'Pó Compacto Translúcido', 59.90, 120, 'po-compacto.jpg', 'Pó para fixação com controle de oleosidade', false),
(1, 'Máscara para Cílios Volume Extreme', 45.90, 200, 'mascara-cilios.jpg', 'Proporciona volume máximo aos cílios', true),

-- Cuidados com a Pele
(2, 'Creme Hidratante Facial 24h', 79.90, 150, 'creme-hidratante.jpg', 'Hidratação profunda por 24 horas', true),
(2, 'Sérum Vitamina C', 129.90, 80, 'serum-vitc.jpg', 'Clareia manchas e uniformiza o tom da pele', true),
(2, 'Protetor Solar FPS 60', 69.90, 200, 'protetor-solar.jpg', 'Proteção UVA/UVB toque seco', false),
(2, 'Gel de Limpeza Facial', 39.90, 180, 'gel-limpeza.jpg', 'Remove impurezas sem ressecar', false),
(2, 'Água Micelar 400ml', 49.90, 250, 'agua-micelar.jpg', 'Limpeza suave e eficaz', true),

-- Cabelos
(3, 'Shampoo Hidratante Karité', 34.90, 300, 'shampoo.jpg', 'Para cabelos secos e danificados', false),
(3, 'Condicionador Reconstruidor', 39.90, 280, 'condicionador.jpg', 'Recupera fios quebradiços', false),
(3, 'Máscara de Tratamento Capilar', 89.90, 150, 'mascara-capilar.jpg', 'Tratamento intensivo sem enxágue', true),
(3, 'Óleo Capilar de Argan', 59.90, 120, 'oleo-argan.jpg', 'Controla frizz e dá brilho', true),
(3, 'Spray Termoprotetor', 42.90, 200, 'spray-termico.jpg', 'Protege do calor até 230°C', false),

-- Perfumaria
(4, 'Perfume Floral 100ml', 199.90, 100, 'perfume-floral.jpg', 'Fragrância floral e suave', true),
(4, 'Deo Colônia Body Spray', 49.90, 300, 'body-spray.jpg', 'Fragrância refrescante para o dia a dia', false),

-- Unhas
(5, 'Kit Esmaltes 5 Unidades', 79.90, 150, 'kit-esmaltes.jpg', 'Kit com cores da temporada', true),
(5, 'Fortalecedor de Unhas', 29.90, 250, 'fortalecedor-unhas.jpg', 'Fórmula que fortalece as unhas', false),

-- Corpo e Banho
(6, 'Óleo Corporal Hidratante', 44.90, 180, 'oleo-corporal.jpg', 'Hidratação intensa para o corpo', false);

-- Criar índices para melhor performance
CREATE INDEX idx_produtos_categoria ON products(categoria_id);
CREATE INDEX idx_pedidos_cliente ON orders(cliente_id);
CREATE INDEX idx_pedidos_status ON orders(status);