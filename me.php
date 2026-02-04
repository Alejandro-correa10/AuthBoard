<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

$headers = function_exists('getallheaders') ? getallheaders() : [];
$auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
  json_response(['ok' => false, 'error' => 'No autorizado'], 401);
}

$token = trim($m[1]);
$now = time();

try {
  $pdo = db();

  // Busca la sesión y el usuario
  $stmt = $pdo->prepare("
    SELECT
      s.user_id, s.issued_at, s.expires_at,
      u.nombre, u.apellido, u.email
    FROM sessions s
    JOIN users u ON u.id = s.user_id
    WHERE s.token = ?
    LIMIT 1
  ");
  $stmt->execute([$token]);
  $row = $stmt->fetch();

  if (!$row) {
    json_response(['ok' => false, 'error' => 'Token inválido'], 401);
  }

  if ((int)$row['expires_at'] < $now) {
    // si expiró, la borramos
    $del = $pdo->prepare("DELETE FROM sessions WHERE token = ?");
    $del->execute([$token]);
    json_response(['ok' => false, 'error' => 'Token expirado'], 401);
  }

  $seconds_left = (int)$row['expires_at'] - $now;

  json_response([
    'ok' => true,
    'user' => [
      'id' => $row['user_id'],
      'nombre' => $row['nombre'],
      'apellido' => $row['apellido'],
      'email' => $row['email'],
    ],
    'session' => [
      'issued_at' => (int)$row['issued_at'],
      'expires_at' => (int)$row['expires_at'],
      'seconds_left' => $seconds_left
    ]
  ]);

} catch (PDOException $e) {
  json_response(['ok' => false, 'error' => 'Error del servidor'], 500);
}
