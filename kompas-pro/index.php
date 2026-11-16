<?php require __DIR__.'/src/bootstrap.php'; if(!current_user()){header('Location: auth.php');exit;} ?>
<!doctype html><html lang="id"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Kompas – Pusat Kendali Harian</title>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head><body>
<nav><h1>Kompas</h1><p id="who">Pusat kendali harian</p>
<a href="#dash">Ringkasan</a><a href="#fin">Keuangan</a><a href="#task">Tugas</a><a href="#habit">Kebiasaan</a><a href="#note">Catatan</a><a href="#set">Pengaturan</a><a href="logout.php">Keluar</a></nav>
<main id="v"></main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>const USER=<?= json_encode(current_user()['name']) ?>;</script>
<script src="assets/js/utils.js"></script>
<script src="assets/js/seed.js"></script>
<script src="assets/js/helpers.js"></script>
<script src="assets/js/views.js"></script>
<script src="assets/js/render.js"></script>
<script src="assets/js/actions.js"></script>
<script src="assets/js/main.js"></script>
</body></html>
