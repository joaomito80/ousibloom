<?php
declare(strict_types=1);
require_once __DIR__.'/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode','1');
    ini_set('session.use_only_cookies','1');
    session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','path'=>'/']);
    session_start();
}
if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));
function usuario(): ?array {return $_SESSION['usuario']??null;}
function validarCsrf(): void {if (!hash_equals($_SESSION['csrf']??'',$_SERVER['HTTP_X_CSRF_TOKEN']??'')) resposta(['erro'=>'Sessão expirada. Atualize a página.'],419);}
function jsonEntrada(): array { $raw=file_get_contents('php://input'); if(strlen($raw)>15000) resposta(['erro'=>'Dados muito grandes'],413); $d=json_decode($raw,true); if(!is_array($d)) resposta(['erro'=>'Dados inválidos'],400); return $d; }
