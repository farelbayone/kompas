let _t;const save=()=>{clearTimeout(_t);_t=setTimeout(()=>fetch('sync.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(S)}).catch(()=>{}),400)},up=()=>{save();render()};
const card=(l,v)=>`<div class="c"><small>${l}</small><b>${v}</b></div>`,empty=t=>`<p class="mut">${t}</p>`;
const PR={h:'Tinggi',m:'Sedang',l:'Rendah'},ST=[['todo','Akan dikerjakan'],['doing','Sedang dikerjakan'],['done','Selesai']];
function streak(h){let n=0,d=new Date();if(!h.done.includes(iso(d)))d.setDate(d.getDate()-1);while(h.done.includes(iso(d))){n++;d.setDate(d.getDate()-1)}return n}
