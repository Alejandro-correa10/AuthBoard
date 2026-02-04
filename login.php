<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) {
  json_response(['ok' => false, 'error' => 'JSON inválido'], 400);
}

$email = strtolower(trim($body['email'] ?? ''));
$password = (string)($body['password'] ?? '');

if (!$email || !$password) {
  json_response(['ok' => false, 'error' => 'Faltan datos'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  json_response(['ok' => false, 'error' => 'Email inválido'], 400);
}

try {
  $pdo = db();

  // Limpia sesiones vencidas
  $pdo->exec("DELETE FROM sessions WHERE expires_at < UNIX_TIMESTAMP()");

  // Buscar usuario por email (y que esté activo)
  $stmt = $pdo->prepare("SELECT id, nombre, apellido, email, password_hash, status FROM users WHERE email = ? LIMIT 1");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if (!$user || (int)$user['status'] !== 1 || !password_verify($password, $user['password_hash'])) {
    json_response(['ok' => false, 'error' => 'Credenciales inválidas'], 401);
  }

  // (Opcional) una sola sesión por usuario
  $del = $pdo->prepare("DELETE FROM sessions WHERE user_id = ?");
  $del->execute([$user['id']]);

  $token = generate_token(32); // 64 chars
  $issuedAt = time();
  $expiresAt = $issuedAt + (15 * 60); // 15 min

  $ins = $pdo->prepare("INSERT INTO sessions (token, user_id, issued_at, expires_at) VALUES (?, ?, ?, ?)");
  $ins->execute([$token, $user['id'], $issuedAt, $expiresAt]);

  json_response([
    'ok' => true,
    'token' => $token,
    'expires_at' => $expiresAt,
    // extra (opcional) para mostrar en front
    'user' => [
      'id' => $user['id'],
      'nombre' => $user['nombre'],
      'apellido' => $user['apellido'],
      'email' => $user['email'],
    ]
  ]);

} catch (PDOException $e) {
  json_response(['ok' => false, 'error' => 'Error del servidor'], 500);
}
