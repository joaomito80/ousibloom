<?php
declare(strict_types=1);
require dirname(__DIR__) . '/config/database.php';
$token=getenv('OUSI_ADMIN_TOKEN');
if (!$token || strlen($token)<16) { http_response_code(503); exit('Configure OUSI_ADMIN_TOKEN (mínimo 16 caracteres) no servidor.'); }
$fornecido=$_SERVER['HTTP_X_ADMIN_TOKEN']??'';
if (!hash_equals($token,$fornecido)) { header('WWW-Authenticate: Basic realm="Ousi Admin"'); http_response_code(401); exit('Acesso negado. Envie X-Admin-Token no cabeçalho HTTP.'); }
try { resposta(['pedidos'=>db()->query('SELECT * FROM pedidos ORDER BY id DESC LIMIT 100')->fetchAll(PDO::FETCH_ASSOC)]); }
catch(Throwable $e) { resposta(['erro'=>'Banco indisponível'],503); }
