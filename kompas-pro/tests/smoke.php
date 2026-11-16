<?php
// Butuh MySQL yang berjalan (lihat src/config.php).
require __DIR__ . '/../src/bootstrap.php';
$_SESSION = [];
function ok(bool $c, string $m): void { if (!$c) { fwrite(STDERR, "FAIL $m\n"); exit(1); } echo "ok  $m\n"; }
$n = 't' . substr(uniqid(), -8);
ok(register_user($n, 'secret') === null, 'register succeeds');
ok(register_user($n, 'other') !== null, 'duplicate username rejected');
ok(register_user('a b', 'secret') !== null, 'invalid username rejected');
ok(login_user($n, 'wrong') !== null, 'wrong password rejected');
ok(login_user($n, 'secret') === null && current_user()['name'] === $n, 'login succeeds');
$uid = current_user()['id'];
ok(load_state($uid) === null, 'new user has no saved state');
$state = ['tx' => [['id' => 1, 't' => 'in', 'a' => 5000, 'c' => 'Gaji', 'd' => '2026-11-01', 'n' => 'x']],
  'tasks' => [['id' => 1, 'ti' => 'Tes', 's' => 'doing', 'p' => 'h', 'due' => '']],
  'habits' => [['id' => 1, 'name' => 'Baca', 'done' => ['2026-11-01', '2026-11-02']]], 'notes' => [['id' => 1, 'ti' => 'N', 'b' => 'isi', 'pin' => 1]]];
save_state($uid, $state); save_state($uid, $state);
$back = load_state($uid);
ok($back['tx'][0]['a'] === 5000 && count($back['habits'][0]['done']) === 2 && $back['notes'][0]['pin'] === 1, 'state saved and loaded from MySQL');
db()->prepare('DELETE FROM users WHERE id = ?')->execute([$uid]);
