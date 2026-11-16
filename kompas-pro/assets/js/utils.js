const K='kompas-v1';let S,FM,NQ='',CH=[];
const $=s=>document.querySelector(s),iso=d=>d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
const today=()=>iso(new Date()),ago=n=>{const x=new Date();x.setDate(x.getDate()-n);return iso(x)};FM=today().slice(0,7);
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const rp=n=>'Rp '+Math.round(n).toLocaleString('id-ID'),uid=()=>Date.now()+Math.floor(Math.random()*1e3);
const sum=(a,t)=>a.filter(x=>x.t===t).reduce((s,x)=>s+x.a,0);
const CAT=['Makan','Transport','Belanja','Tagihan','Hiburan','Gaji','Lainnya'];
