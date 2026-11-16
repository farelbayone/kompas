function render(keep){const r=(location.hash.slice(1)||'dash');const f=V[r]?r:'dash';const ae=document.activeElement,pos=ae&&ae.selectionStart;
$('#v').innerHTML=V[f]();document.querySelectorAll('nav a').forEach(a=>a.classList.toggle('on',a.hash==='#'+f));CH.forEach(c=>c.destroy());CH=[];
if(keep){const i=$('input[type=search]');if(i){i.focus();i.setSelectionRange(pos,pos)}}if(f==='dash')charts()}
function charts(){if(!window.Chart)return;const cs=getComputedStyle(document.documentElement),g=n=>cs.getPropertyValue(n).trim();Chart.defaults.color=g('--mut');Chart.defaults.font.family='Manrope,system-ui,sans-serif';
const ms=[5,4,3,2,1,0].map(i=>{const d=new Date();d.setDate(1);d.setMonth(d.getMonth()-i);return iso(d).slice(0,7)});
CH.push(new Chart($('#c1'),{type:'bar',data:{labels:ms.map(m=>new Date(m+'-01T00:00').toLocaleDateString('id-ID',{month:'short'})),datasets:[{label:'Masuk',data:ms.map(m=>sum(S.tx.filter(x=>x.d.startsWith(m)),'in')),backgroundColor:g('--acc')},{label:'Keluar',data:ms.map(m=>sum(S.tx.filter(x=>x.d.startsWith(m)),'out')),backgroundColor:g('--acc2')}]}}));
const o=S.tx.filter(x=>x.t==='out'&&x.d.startsWith(today().slice(0,7))),by={};o.forEach(x=>by[x.c]=(by[x.c]||0)+x.a);
CH.push(new Chart($('#c2'),{type:'doughnut',data:{labels:Object.keys(by),datasets:[{data:Object.values(by),borderColor:g('--card'),backgroundColor:['#3b5bdb','#e8590c','#2b8a3e','#ae3ec9','#f08c00','#1098ad','#868e96']}]}}))}
