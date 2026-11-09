# Ousi Bloom — Loja com contas de clientes

Projeto acadêmico em HTML, CSS, JavaScript e PHP 8.1+, com SQLite.

## Executar

1. Instale PHP com extensões `pdo_sqlite`, `sqlite3` e `mbstring`.
2. Abra o terminal na pasta do projeto.
3. Execute `php -S localhost:8000 router.php`.
4. Acesse `http://localhost:8000`.

**Não abra o index.html diretamente:** login e pedidos dependem do servidor PHP.

## Estrutura

- `index.html`: página da loja e formulário de conta.
- `assets/css/style.css`: estilos responsivos e interface de login.
- `assets/js/conta.js`: login, cadastro, logout e sincronização de favoritos.
- `assets/js/loja.js`: catálogo, carrinho, filtros e checkout.
- `api/auth.php`: API de autenticação por sessão.
- `api/favoritos.php`: favoritos salvos por usuário.
- `api/pedidos.php`: pedidos demonstrativos.
- `config/auth.php`: configuração segura de sessão e proteção CSRF.
- `config/database.php`: tabelas SQLite.
- `storage/`: banco de dados criado automaticamente (não publicar esta pasta).

## Funcionalidades

- Cadastro de clientes com nome, e-mail e senha de pelo menos 8 caracteres.
- Senhas armazenadas com `password_hash` e conferidas com `password_verify`.
- Login e logout via sessão PHP com cookie HttpOnly e SameSite.
- Favoritos persistidos no banco por conta.
- Layout atualizado com modal de login responsivo.
- Pedidos de demonstração, sem pagamento real.

## Atenção antes de publicar

É um protótipo acadêmico, não uma loja pronta para produção. Para publicar, configurar HTTPS, limitação de tentativas de login, recuperação/verificação de e-mail, política de privacidade/LGPD, backup, monitoramento e revisão de segurança. A administração de pedidos continua por token de ambiente (`OUSI_ADMIN_TOKEN`), sem painel gráfico. Não use senhas reais de outros serviços.
