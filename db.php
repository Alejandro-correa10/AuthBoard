<?php
declare(strict_types=1);

/**
 * Conexión única a MySQL usando PDO
 * Uso: $pdo = db();
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    // 🔧 CONFIGURACIÓN LOCAL SI USAS XAMPP
    $host   = 'localhost';
    $dbname = 'auth_app';
    $user   = 'root';
    $pass   = 'Correa@@';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        exit('Error de conexión a la base de datos');
    }

    return $pdo;
}
