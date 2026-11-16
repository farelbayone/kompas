<?php
if (PHP_SAPI !== 'cli') session_start();

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Koneksi MySQL. Database dan tabel dibuat otomatis kalau belum ada.
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $c = require __DIR__ . '/config.php';
    $pdo = new PDO("mysql:host={$c['host']};port={$c['port']};charset=utf8mb4", $c['user'], $c['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    try { $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$c['name']}` CHARACTER SET utf8mb4"); } catch (PDOException $x) {}
    $pdo->exec("USE `{$c['name']}`");
    if (!$pdo->query("SHOW TABLES LIKE 'users'")->fetch()) {
        $pdo->exec(preg_replace('/^(CREATE DATABASE|USE)[^;]*;/mi', '', file_get_contents(__DIR__ . '/../database.sql')));
    }
    return $pdo;
}
function db_error(Throwable $x): string {
    return 'Database bermasalah: ' . $x->getMessage() . ' (pastikan MySQL di Laragon sudah Start dan cek src/config.php)';
}
function current_user(): ?array {
    return isset($_SESSION['uid']) ? ['id' => (int)$_SESSION['uid'], 'name' => $_SESSION['user']] : null;
}

// Mengembalikan pesan error, atau null kalau berhasil.
function register_user(string $name, string $pass): ?string {
    if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $name)) return 'Username 3-20 karakter: huruf, angka, atau underscore.';
    if (strlen($pass) < 4) return 'Password minimal 4 karakter.';
    try {
        $s = db()->prepare('SELECT 1 FROM users WHERE username = ?'); $s->execute([$name]);
        if ($s->fetch()) return 'Username sudah dipakai.';
        db()->prepare('INSERT INTO users(username, password_hash) VALUES(?, ?)')->execute([$name, password_hash($pass, PASSWORD_DEFAULT)]);
    } catch (Throwable $x) { return db_error($x); }
    return null;
}
function login_user(string $name, string $pass): ?string {
    try {
        $s = db()->prepare('SELECT id, username, password_hash FROM users WHERE username = ?'); $s->execute([$name]);
        $u = $s->fetch();
    } catch (Throwable $x) { return db_error($x); }
    if (!$u || !password_verify($pass, $u['password_hash'])) return 'Username atau password salah.';
    $_SESSION['uid'] = (int)$u['id']; $_SESSION['user'] = $u['username'];
    return null;
}

// Data dashboard <-> tabel MySQL. Bentuk array sama dengan yang dipakai JavaScript.
function load_state(int $uid): ?array {
    $pdo = db();
    $q = function (string $sql) use ($pdo, $uid) { $s = $pdo->prepare($sql); $s->execute([$uid]); return $s->fetchAll(); };
    $s = $pdo->prepare('SELECT has_data FROM users WHERE id = ?'); $s->execute([$uid]);
    if (!$s->fetchColumn()) return null;
    $habits = $q('SELECT id, name FROM habits WHERE user_id = ? ORDER BY id');
    $logs = [];
    foreach ($q('SELECT habit_id, log_date FROM habit_logs WHERE user_id = ?') as $l) $logs[$l['habit_id']][] = $l['log_date'];
    return [
        'tx' => array_map(fn($r) => ['id' => (int)$r['id'], 't' => $r['type'], 'a' => (int)$r['amount'], 'c' => $r['category'], 'd' => $r['trx_date'], 'n' => $r['note'] ?? ''],
            $q('SELECT * FROM transactions WHERE user_id = ? ORDER BY trx_date, id')),
        'tasks' => array_map(fn($r) => ['id' => (int)$r['id'], 'ti' => $r['title'], 's' => $r['status'], 'p' => $r['priority'], 'due' => $r['due_date'] ?? ''],
            $q('SELECT * FROM tasks WHERE user_id = ? ORDER BY id')),
        'habits' => array_map(fn($h) => ['id' => (int)$h['id'], 'name' => $h['name'], 'done' => $logs[$h['id']] ?? []], $habits),
        'notes' => array_map(fn($r) => ['id' => (int)$r['id'], 'ti' => $r['title'], 'b' => $r['body'] ?? '', 'pin' => (int)$r['pinned']],
            $q('SELECT * FROM notes WHERE user_id = ? ORDER BY id')),
    ];
}
function save_state(int $uid, array $s): void {
    $pdo = db();
    $date = fn($v) => preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$v) ? $v : null;
    $cut = fn($v, $n) => mb_substr((string)$v, 0, $n);
    $pdo->beginTransaction();
    try {
        foreach (['habit_logs', 'habits', 'transactions', 'tasks', 'notes'] as $t) $pdo->prepare("DELETE FROM $t WHERE user_id = ?")->execute([$uid]);
        $st = $pdo->prepare('INSERT INTO transactions(user_id,id,type,amount,category,trx_date,note) VALUES(?,?,?,?,?,?,?)');
        foreach ($s['tx'] ?? [] as $x) if ($d = $date($x['d'] ?? null))
            $st->execute([$uid, (int)$x['id'], ($x['t'] ?? '') === 'in' ? 'in' : 'out', max(0, (int)($x['a'] ?? 0)), $cut($x['c'] ?? '', 50), $d, $cut($x['n'] ?? '', 255)]);
        $st = $pdo->prepare('INSERT INTO tasks(user_id,id,title,status,priority,due_date) VALUES(?,?,?,?,?,?)');
        foreach ($s['tasks'] ?? [] as $x)
            $st->execute([$uid, (int)$x['id'], $cut($x['ti'] ?? '', 200), in_array($x['s'] ?? '', ['todo', 'doing', 'done'], true) ? $x['s'] : 'todo', in_array($x['p'] ?? '', ['h', 'm', 'l'], true) ? $x['p'] : 'm', $date($x['due'] ?? null)]);
        $st = $pdo->prepare('INSERT INTO habits(user_id,id,name) VALUES(?,?,?)');
        $lg = $pdo->prepare('INSERT IGNORE INTO habit_logs(user_id,habit_id,log_date) VALUES(?,?,?)');
        foreach ($s['habits'] ?? [] as $h) {
            $st->execute([$uid, (int)$h['id'], $cut($h['name'] ?? '', 100)]);
            foreach ($h['done'] ?? [] as $d) if ($d = $date($d)) $lg->execute([$uid, (int)$h['id'], $d]);
        }
        $st = $pdo->prepare('INSERT INTO notes(user_id,id,title,body,pinned) VALUES(?,?,?,?,?)');
        foreach ($s['notes'] ?? [] as $x) $st->execute([$uid, (int)$x['id'], $cut($x['ti'] ?? '', 150), (string)($x['b'] ?? ''), empty($x['pin']) ? 0 : 1]);
        $pdo->prepare('UPDATE users SET has_data = 1 WHERE id = ?')->execute([$uid]);
        $pdo->commit();
    } catch (Throwable $x) { $pdo->rollBack(); throw $x; }
}
