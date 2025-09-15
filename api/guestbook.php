<?php
header('Content-Type: application/json; charset=utf-8');
// CORS (ubah origin jika perlu)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit;
}

$dataFile = __DIR__ . '/guestbook.json';

// baca JSON body jika dikirim
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true) ?: [];

// GET -> return semua entry
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $items = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
  echo json_encode($items);
  exit;
}

// POST -> tambah entry
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // terima form-url-encoded atau JSON
  $nama = trim($_POST['nama'] ?? $input['nama'] ?? '');
  $konfirmasi = trim($_POST['konfirmasi'] ?? $input['konfirmasi'] ?? '');
  $pesan = trim($_POST['pesan'] ?? $input['pesan'] ?? '');

  if ($nama === '' || $pesan === '') {
    http_response_code(400);
    echo json_encode(['error' => 'nama dan pesan diperlukan']);
    exit;
  }

  $entry = [
    'nama' => $nama,
    'konfirmasi' => $konfirmasi,
    'pesan' => $pesan,
    'waktu' => round(microtime(true) * 1000)
  ];

  $items = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
  array_unshift($items, $entry);
  file_put_contents($dataFile, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

  echo json_encode($entry);
  exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);