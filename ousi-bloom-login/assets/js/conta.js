// Conta de clientes: autenticação via sessão PHP e favoritos por usuário.
(() => {
  let csrf='', currentUser=null, mode='entrar';
  const el=id=>document.getElementById(id);
  const message=text=>{el('authError').textContent=text;el('authError').hidden=!text;};
  async function request(url,options={}) {
    const response=await fetch(url,{credentials:'same-origin',...options,headers:{'Content-Type':'application/json','X-CSRF-Token':csrf,...options.headers}});
    const data=await response.json();if(!response.ok)throw new Error(data.erro||'Não foi possível concluir.');return data;
  }
  function showUser(){
    el('authGuest').hidden=!!currentUser;el('authLogged').hidden=!currentUser;
    el('accountLabel').textContent=currentUser?currentUser.nome.split(' ')[0]:'Entrar / Cadastrar';
    if(currentUser){el('welcomeName').textContent='Olá, '+currentUser.nome.split(' ')[0]+' ♡';el('welcomeEmail').textContent=currentUser.email;}
  }
  async function loadFavorites(){
    if(!currentUser)return;
    try {const data=await request('api/favoritos.php');favorites.clear();data.ids.forEach(id=>favorites.add(id));render();}
    catch(err){console.warn('Favoritos indisponíveis:',err.message);}
  }
  function changeMode(next){mode=next;document.querySelectorAll('[data-auth-tab]').forEach(b=>b.classList.toggle('active',b.dataset.authTab===mode));el('authNameWrap').hidden=mode!=='cadastrar';el('authName').required=mode==='cadastrar';el('authPassword').autocomplete=mode==='cadastrar'?'new-password':'current-password';el('authSubmit').textContent=mode==='cadastrar'?'Criar minha conta':'Entrar na minha conta';message('');}
  el('accountBtn').addEventListener('click',()=>showModal('#accountModal'));
  document.querySelectorAll('[data-auth-tab]').forEach(b=>b.addEventListener('click',()=>changeMode(b.dataset.authTab)));
  el('authForm').addEventListener('submit',async event=>{
    event.preventDefault();message('');const button=el('authSubmit');button.disabled=true;
    try {const data=await request('api/auth.php?acao='+mode,{method:'POST',body:JSON.stringify({nome:el('authName').value,email:el('authEmail').value,senha:el('authPassword').value})});currentUser=data.usuario;csrf=data.csrf;el('authForm').reset();showUser();await loadFavorites();toast('Que bom ter você por aqui ♡');}
    catch(err){message(err.message);}finally{button.disabled=false;}
  });
  el('logoutBtn').addEventListener('click',async()=>{try{await request('api/auth.php?acao=sair',{method:'POST'});currentUser=null;favorites.clear();render();showUser();closeAll();toast('Você saiu da sua conta.');}catch(err){toast(err.message);}});
  document.addEventListener('ousi:favorito',async event=>{if(!currentUser)return;try{await request('api/favoritos.php',{method:'POST',body:JSON.stringify(event.detail)});}catch(err){toast('Não foi possível salvar o favorito.');}});
  // O checkout utiliza o mesmo token de sessão, mesmo para visitantes.
  window.ousiCsrf=()=>csrf;
  request('api/auth.php?acao=sessao').then(async data=>{csrf=data.csrf;currentUser=data.usuario;showUser();await loadFavorites();}).catch(()=>{showUser();console.warn('Servidor PHP indisponível: login requer PHP.');});
})();
