<?php
// Jembatan kecil antara dashboard dan MySQL: GET = baca data, POST = simpan data.
require __DIR__ . '/src/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
$u = current_user();
if (!$u) { http_response_code(401); echo '{"error":"login"}'; exit; }
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $s = json_decode(file_get_contents('php://input'), true);
        if (!is_array($s)) { http_response_code(422); echo '{"error":"invalid"}'; exit; }
        save_state($u['id'], $s);
        echo '{"ok":true}';
    } else {
        echo json_encode(['state' => load_state($u['id'])], JSON_UNESCAPED_UNICODE);
    }
} catch (Throwable $x) { http_response_code(500); echo json_encode(['error' => db_error($x)]); }
