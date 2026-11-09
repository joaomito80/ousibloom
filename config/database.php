<?php
declare(strict_types=1);
function db(): PDO {
    $path = dirname(__DIR__) . '/storage/loja.sqlite';
    $pdo = new PDO('sqlite:' . $path, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec('CREATE TABLE IF NOT EXISTS pedidos (id INTEGER PRIMARY KEY AUTOINCREMENT, cliente TEXT NOT NULL, email TEXT NOT NULL, itens TEXT NOT NULL, total_centavos INTEGER NOT NULL, criado_em TEXT NOT NULL)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS usuarios (id INTEGER PRIMARY KEY AUTOINCREMENT, nome TEXT NOT NULL, email TEXT NOT NULL UNIQUE, senha_hash TEXT NOT NULL, criado_em TEXT NOT NULL)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS favoritos (usuario_id INTEGER NOT NULL, produto_id INTEGER NOT NULL, PRIMARY KEY(usuario_id,produto_id))');
    return $pdo;
}
function resposta(array $dados, int $status=200): never {
    http_response_code($status); header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store'); echo json_encode($dados, JSON_UNESCAPED_UNICODE); exit;
}
