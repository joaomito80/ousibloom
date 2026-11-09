<?php
declare(strict_types=1);
require dirname(__DIR__).'/config/auth.php';
header('Cache-Control: no-store');
$acao=$_GET['acao']??'sessao';
if ($acao==='sessao' && $_SERVER['REQUEST_METHOD']==='GET') resposta(['usuario'=>usuario(),'csrf'=>$_SESSION['csrf']]);
if ($_SERVER['REQUEST_METHOD']!=='POST') resposta(['erro'=>'Método não permitido'],405);
validarCsrf();
if ($acao==='sair') {unset($_SESSION['usuario']);session_regenerate_id(true);resposta(['sucesso'=>true]);}
$d=jsonEntrada();$email=mb_strtolower(trim((string)($d['email']??'')));$senha=(string)($d['senha']??'');
if (!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>180||strlen($senha)<8||strlen($senha)>128) resposta(['erro'=>'Confira e-mail e senha (mínimo de 8 caracteres).'],422);
try {
  $pdo=db();
  if($acao==='cadastrar') {
    $nome=trim((string)($d['nome']??''));
    if(mb_strlen($nome)<2||mb_strlen($nome)>100) resposta(['erro'=>'Informe seu nome (2 a 100 caracteres).'],422);
    $stmt=$pdo->prepare('INSERT INTO usuarios(nome,email,senha_hash,criado_em) VALUES (?,?,?,?)');
    try{$stmt->execute([$nome,$email,password_hash($senha,PASSWORD_DEFAULT),gmdate('c')]);}
    catch(PDOException $e){if($e->getCode()==='23000') resposta(['erro'=>'Este e-mail já está cadastrado.'],409);throw $e;}
    $id=(int)$pdo->lastInsertId();
  } elseif($acao==='entrar') {
    $stmt=$pdo->prepare('SELECT id,nome,email,senha_hash FROM usuarios WHERE email=?');$stmt->execute([$email]);$u=$stmt->fetch(PDO::FETCH_ASSOC);
    if(!$u||!password_verify($senha,$u['senha_hash'])) resposta(['erro'=>'E-mail ou senha incorretos.'],401);
    $id=(int)$u['id'];$nome=$u['nome'];
  } else resposta(['erro'=>'Ação desconhecida'],404);
  session_regenerate_id(true);
  $_SESSION['usuario']=['id'=>$id,'nome'=>$nome,'email'=>$email];
  resposta(['sucesso'=>true,'usuario'=>usuario(),'csrf'=>$_SESSION['csrf']]);
} catch(Throwable $e){resposta(['erro'=>'Serviço indisponível. Verifique o banco SQLite.'],503);}
