<?php

require_once __DIR__ . '/config.php';

function get_db(): PDO {
    // Gunakan static variable agar koneksi database digunakan kembali 
    // jika fungsi dipanggil beberapa kali dalam satu request yang sama.
    static $pdo = null;

    if ($pdo === null) {
        try {
            // Gunakan utf8mb4 agar mendukung emoji (seperti stiker) dengan sempurna di MySQL
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Hentikan eksekusi dan kembalikan JSON error jika database mati/kredensial salah
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'error' => 'Database connection failed'
            ]);
            exit;
        }
    }

    return $pdo;
}
