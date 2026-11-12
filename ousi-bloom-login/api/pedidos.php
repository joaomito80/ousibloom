<?php
declare(strict_types=1);
require dirname(__DIR__) . '/config/auth.php';
validarCsrf();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Allow: POST'); resposta(['erro'=>'Método não permitido'],405); }
$corpo=file_get_contents('php://input');
if (strlen($corpo) > 30000) resposta(['erro'=>'Pedido muito grande'],413);
$dados=json_decode($corpo,true);
if (!is_array($dados)) resposta(['erro'=>'JSON inválido'],400);
$nome=trim((string)($dados['nome']??'')); $email=trim((string)($dados['email']??'')); $itens=$dados['itens']??null;
if (mb_strlen($nome)<2 || mb_strlen($nome)>100 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>180 || !is_array($itens) || count($itens)<1 || count($itens)>40) resposta(['erro'=>'Dados inválidos'],422);
$catalogo=json_decode(file_get_contents(dirname(__DIR__).'/config/produtos.json'),true);
$porId=array_column($catalogo,null,'id'); $salvos=[]; $total=0;
foreach ($itens as $item) {
    if (!is_array($item) || !isset($item['id'],$item['size'],$item['qty'])) resposta(['erro'=>'Item inválido'],422);
    $id=filter_var($item['id'],FILTER_VALIDATE_INT); $q=filter_var($item['qty'],FILTER_VALIDATE_INT); $tam=(string)$item['size'];
    if (!$id || !isset($porId[$id]) || !$q || $q<1 || $q>20 || !in_array($tam,$porId[$id]['sizes'],true)) resposta(['erro'=>'Produto, tamanho ou quantidade inválidos'],422);
    $p=$porId[$id]; $total += $p['price']*$q;
    $salvos[]=['id'=>$id,'nome'=>$p['name'],'tamanho'=>$tam,'quantidade'=>$q,'preco_centavos'=>$p['price']];
}
try {
    $pdo=db(); $stmt=$pdo->prepare('INSERT INTO pedidos (cliente,email,itens,total_centavos,criado_em) VALUES (?,?,?,?,?)');
    $stmt->execute([$nome,$email,json_encode($salvos,JSON_UNESCAPED_UNICODE),$total,gmdate('c')]);
    resposta(['sucesso'=>true,'numero'=>(int)$pdo->lastInsertId(),'total_centavos'=>$total],201);
} catch (Throwable $e) { resposta(['erro'=>'Não foi possível registrar o pedido. Verifique o SQLite e as permissões do servidor.'],503); }
