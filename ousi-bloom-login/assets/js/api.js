// Comunicação com o backend PHP. Requer servidor PHP, não funciona via file://.
async function registrarPedido(dados) {
  let resposta;
  try {
    resposta = await fetch('api/pedidos.php', {
      method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-Token':window.ousiCsrf?.()||''},
      body: JSON.stringify(dados)
    });
  } catch (_) { throw new Error('Abra o site pelo servidor PHP para registrar pedidos.'); }
  const json = await resposta.json().catch(() => ({}));
  if (!resposta.ok) throw new Error(json.erro || 'Erro ao registrar pedido.');
  return json;
}
