(async()=>{
  try{const r=await(await fetch('sync.php')).json();S=r.state&&r.state.tx?r.state:seed();if(!r.state)save()}catch(e){S=seed()}
  $('#who').textContent='Halo, '+USER;
  addEventListener('hashchange',()=>render());render();
})();
