<?php
require_once __DIR__ . '/helpers.php';

$sessionsPath = __DIR__ . '/sessions.json';

$body = json_decode(file_get_contents('php://input'), true);
$token = (string)($body['token'] ?? '');

if (!$token) json_response(['ok' => false, 'error' => 'Falta token'], 400);

$sessions = read_json_file($sessionsPath);
$now = time();

$found = null;
foreach ($sessions as $idx => $s) {
  if (($s['token'] ?? '') === $token) {
    $found = ['idx' => $idx, 's' => $s];
    break;
  }
}

if (!$found) json_response(['ok' => false, 'error' => 'Token inválido'], 401);
if (($found['s']['expires_at'] ?? 0) < $now) {
  // si ya expiró, lo borramos
  array_splice($sessions, $found['idx'], 1);
  write_json_file($sessionsPath, $sessions);
  json_response(['ok' => false, 'error' => 'Token expirado'], 401);
}

$newToken = generate_token(32);
$issuedAt = $now;
$expiresAt = $now + (15 * 60);

$sessions[$found['idx']] = [
  'token' => $newToken,
  'user_id' => $found['s']['user_id'],
  'issued_at' => $issuedAt,
  'expires_at' => $expiresAt,
];

write_json_file($sessionsPath, $sessions);

json_response([
  'ok' => true,
  'token' => $newToken,
  'expires_at' => $expiresAt
]);
