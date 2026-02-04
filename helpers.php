<?php
// api/helpers.php

function read_json_file(string $path): array {
  if (!file_exists($path)) return [];
  $raw = file_get_contents($path);
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function write_json_file(string $path, array $data): void {
  file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function json_response($data, int $code = 200): void {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function generate_token(int $bytes = 32): string {
  return bin2hex(random_bytes($bytes)); // 64 chars hex si bytes=32
}

function now_ms(): int {
  return (int) floor(microtime(true) * 1000);
}
