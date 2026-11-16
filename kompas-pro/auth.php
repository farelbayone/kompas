<?php
require __DIR__ . '/src/bootstrap.php';
if (current_user()) { header('Location: index.php'); exit; }
$mode = ($_POST['mode'] ?? 'login') === 'register' ? 'register' : 'login'; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = trim($_POST['username'] ?? ''); $p = (string)($_POST['password'] ?? '');
    $err = $mode === 'register' ? register_user($n, $p) : null;
    if (!$err) $err = login_user($n, $p);
    if (!$err) { header('Location: index.php'); exit; }
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Masuk – Kompas</title>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css"><link rel="stylesheet" href="assets/css/auth.css"></head><body>
<div class="wrap">
<aside class="brand"><div><h1>Kompas</h1><p class="lead">Satu tempat untuk uang, tugas, kebiasaan, dan catatanmu.</p>
<ul><li><b>Keuangan</b><span>Catat pemasukan dan pengeluaran, lihat grafik per bulan.</span></li>
<li><b>Tugas</b><span>Papan kanban dengan prioritas dan tenggat.</span></li>
<li><b>Kebiasaan</b><span>Pantau 7 hari terakhir dan jaga streak.</span></li>
<li><b>Catatan</b><span>Tulis, sematkan, dan cari dengan cepat.</span></li></ul></div>
<small>Datamu tersimpan di akunmu sendiri.</small></aside>
<section class="panel"><div class="box">
<div class="tabs" role="tablist"><button type="button" data-m="login" aria-selected="true">Masuk</button><button type="button" data-m="register" aria-selected="false">Daftar</button></div>
<h2 id="h">Selamat datang kembali</h2><p class="mut" id="sub" style="margin:0">Masuk untuk membuka dashboard kamu.</p>
<form class="af" id="f" method="post"><input type="hidden" name="mode" id="mode" value="<?= e($mode) ?>">
<label>Username<input name="username" id="u" required autocomplete="username" autofocus value="<?= e($_POST['username'] ?? '') ?>"></label>
<label>Password<span class="pw"><input name="password" id="pw" type="password" required autocomplete="current-password"><button type="button" id="show">Tampilkan</button></span></label>
<p class="hint" id="hint" hidden>Username 3-20 karakter (huruf, angka, underscore). Password minimal 4 karakter.</p>
<p class="err" id="e" role="alert"><?= e($err) ?></p><button id="b">Masuk</button></form>
</div></section></div>
<script>
const $=s=>document.querySelector(s);
function setMode(m){const l=m==='login';$('#mode').value=m;document.querySelectorAll('.tabs button').forEach(b=>b.setAttribute('aria-selected',b.dataset.m===m));
$('#h').textContent=l?'Selamat datang kembali':'Buat akun baru';$('#sub').textContent=l?'Masuk untuk membuka dashboard kamu.':'Gratis. Cukup username dan password.';
$('#b').textContent=l?'Masuk':'Daftar';$('#pw').autocomplete=l?'current-password':'new-password';$('#hint').hidden=l}
document.querySelectorAll('.tabs button').forEach(b=>b.onclick=()=>{setMode(b.dataset.m);$('#e').textContent=''});
$('#show').onclick=()=>{const p=$('#pw'),h=p.type==='password';p.type=h?'text':'password';$('#show').textContent=h?'Sembunyikan':'Tampilkan'};
setMode(<?= json_encode($mode) ?>);
</script></body></html>
