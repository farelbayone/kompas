const f=e=>{e.preventDefault();return Object.fromEntries(new FormData(e.target))};
function addTx(e){const d=f(e);S.tx.push({id:uid(),t:d.t,a:+d.a,c:d.c,d:d.d,n:d.n});FM=d.d.slice(0,7);up()}
function addTask(e){const d=f(e);S.tasks.push({id:uid(),ti:d.ti,s:'todo',p:d.p,due:d.due});up()}
function addHabit(e){const d=f(e);S.habits.push({id:uid(),name:d.n,done:[]});up()}
function addNote(e){const d=f(e);S.notes.push({id:uid(),ti:d.ti,b:d.b,pin:0});up()}
function del(k,id){S[k]=S[k].filter(x=>x.id!==id);up()}
function mv(id,dir){const t=S.tasks.find(x=>x.id===id),o=['todo','doing','done'],i=o.indexOf(t.s)+dir;if(o[i]){t.s=o[i];up()}}
function tog(id,d){const h=S.habits.find(x=>x.id===id);h.done=h.done.includes(d)?h.done.filter(x=>x!==d):[...h.done,d];up()}
function pin(id){const n=S.notes.find(x=>x.id===id);n.pin=n.pin?0:1;up()}
function theme(){const r=document.documentElement,dark=r.dataset.theme?r.dataset.theme==='dark':matchMedia('(prefers-color-scheme:dark)').matches;r.dataset.theme=dark?'light':'dark';render()}
function exp(){const a=document.createElement('a');a.href=URL.createObjectURL(new Blob([JSON.stringify(S,null,2)],{type:'application/json'}));a.download='kompas-data.json';a.click()}
function imp(file){if(!file)return;const r=new FileReader();r.onload=()=>{try{const d=JSON.parse(r.result);if(!d.tx||!d.tasks||!d.habits||!d.notes)throw 0;S=d;up();alert('Data berhasil diimpor.')}catch(e){alert('File tidak valid. Gunakan file hasil ekspor Kompas.')}};r.readAsText(file)}
