<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

$body = json_decode(file_get_contents('php://input'), true);

$nombre   = trim($body['nombre'] ?? '');
$apellido = trim($body['apellido'] ?? '');
$email    = strtolower(trim($body['email'] ?? ''));
$password = (string)($body['password'] ?? '');

if (!$nombre || !$apellido || !$email || !$password) {
  json_response(['ok' => false, 'error' => 'Todos los campos son obligatorios'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  json_response(['ok' => false, 'error' => 'Email inválido'], 400);
}

try {
  $pdo = db();

  $id   = generate_token(8);
  $hash = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $pdo->prepare(
    "INSERT INTO users (id, nombre, apellido, email, password_hash)
     VALUES (?, ?, ?, ?, ?)"
  );

  $stmt->execute([$id, $nombre, $apellido, $email, $hash]);

  json_response(['ok' => true]);

} catch (PDOException $e) {
  // Email duplicado
  if (($e->errorInfo[1] ?? null) === 1062) {
    json_response(['ok' => false, 'error' => 'Ese email ya está registrado'], 409);
  }

  json_response(['ok' => false, 'error' => 'Error del servidor'], 500);
}
