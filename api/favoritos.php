<?php
declare(strict_types=1);
require dirname(__DIR__).'/config/auth.php';
$u=usuario();if(!$u) resposta(['erro'=>'Faça login para salvar favoritos.'],401);
try {
$pdo=db();
if($_SERVER['REQUEST_METHOD']==='GET'){$s=$pdo->prepare('SELECT produto_id FROM favoritos WHERE usuario_id=?');$s->execute([$u['id']]);resposta(['ids'=>array_map('intval',$s->fetchAll(PDO::FETCH_COLUMN))]);}
if($_SERVER['REQUEST_METHOD']!=='POST') resposta(['erro'=>'Método não permitido'],405);
validarCsrf();$d=jsonEntrada();$id=filter_var($d['id']??null,FILTER_VALIDATE_INT);
if(!$id||!in_array($id,array_column(json_decode(file_get_contents(dirname(__DIR__).'/config/produtos.json'),true),'id'),true)) resposta(['erro'=>'Produto inválido'],422);
if(!empty($d['favorito'])){$s=$pdo->prepare('INSERT OR IGNORE INTO favoritos(usuario_id,produto_id) VALUES (?,?)');}else{$s=$pdo->prepare('DELETE FROM favoritos WHERE usuario_id=? AND produto_id=?');}
$s->execute([$u['id'],$id]);resposta(['sucesso'=>true]);
}catch(Throwable $e){resposta(['erro'=>'Não foi possível salvar favorito'],503);}
