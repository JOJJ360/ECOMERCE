===========================================
E-COMMERCE BEAUTY STORE - PRODUTOS DE BELEZA
===========================================

DESCRIÇÃO:
Sistema completo de e-commerce para produtos de beleza feminina desenvolvido
com PHP, MySQL, Bootstrap 5, HTML5 e CSS3.

CARACTERÍSTICAS:
✅ 20 produtos de beleza feminina pré-cadastrados
✅ 7 categorias de produtos
✅ Sistema de login/registro
✅ Carrinho de compras com sessões
✅ Checkout completo (3 etapas)
✅ Painel administrativo CRUD
✅ Design responsivo com Bootstrap 5
✅ Tema rosa feminino personalizado
✅ Sistema de pedidos com status
✅ Gestão de estoque

INSTALAÇÃO:
1. Extraia os arquivos na pasta htdocs do XAMPP/WAMP
   Ex: C:\xampp\htdocs\beauty_ecommerce\

2. Configure o banco de dados:
   - Acesse phpMyAdmin (http://localhost/phpmyadmin)
   - Crie um novo banco chamado 'beauty_ecommerce'
   - Importe o arquivo database.sql
   OU
   - Execute manualmente o script SQL no phpMyAdmin

3. Configure as credenciais do banco:
   - Edite o arquivo includes/config.php
   - Verifique se as constantes DB_USER e DB_PASS estão corretas

4. Acesse o sistema:
   - Loja: http://localhost/beauty_ecommerce/
   - Painel Admin: http://localhost/beauty_ecommerce/admin/
   
   Credenciais padrão:
   Admin: admin@beauty.com / admin123
   Cliente: cliente@teste.com / admin123

ESTRUTURA DO PROJETO:
/
├── admin/                    # Painel administrativo
├── assets/                   # Arquivos estáticos
│   ├── css/                 # Folhas de estilo
│   ├── js/                  # Scripts JavaScript
│   └── images/              # Imagens do site
├── includes/                 # Includes do PHP
├── pages/                   # Páginas do site
├── index.php               # Página inicial
├── database.sql            # Script do banco de dados
├── .htaccess               # Configurações do Apache
└── README.txt             # Este arquivo

BANCO DE DADOS:
- users: Usuários do sistema (admin/cliente)
- categories: Categorias de produtos
- products: Produtos do catálogo
- orders: Pedidos realizados
- order_items: Itens dos pedidos

FUNCIONALIDADES DO ADMIN:
- Dashboard com estatísticas
- CRUD completo de produtos
- Gerenciamento de pedidos
- CRUD de categorias
- Listagem de usuários

OBSERVAÇÕES IMPORTANTES:
1. Todas as imagens são placeholders
2. Pagamentos são simulados para fins educacionais
3. Sistema desenvolvido para ambiente local
4. Use como base para projetos reais
5. Testado em XAMPP com PHP 7.4+

CONTATO:
Para dúvidas ou suporte, entre em contato:
Email: suporte@beautystore.com

LICENÇA:
Este projeto é para fins educacionais.