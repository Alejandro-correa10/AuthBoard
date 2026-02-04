<?php
require_once __DIR__ . '/helpers.php';

$sessionsPath = __DIR__ . '/sessions.json';

$body = json_decode(file_get_contents('php://input'), true);
$token = (string)($body['token'] ?? '');

if (!$token) json_response(['ok' => false, 'error' => 'Falta token'], 400);

$sessions = read_json_file($sessionsPath);
$before = count($sessions);

$sessions = array_values(array_filter($sessions, fn($s) => ($s['token'] ?? '') !== $token));

write_json_file($sessionsPath, $sessions);

json_response(['ok' => true, 'deleted' => $before - count($sessions)]);
