<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
 
function out($ok, $message, $extra = [], $code = 200) {
    http_response_code($code);
    echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $extra));
    exit;
}
 
$DEBUG = true; // ubah ke false setelah semuanya berjalan
 
// Pengaturan koneksi: sesuaikan dengan MySQL Anda
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';        
$DB_NAME = 'volunova';
$DB_PORT = 3306;      
 
try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    $conn->set_charset('utf8mb4');
} catch (Throwable $e) {
    out(false, 'Tidak dapat terhubung ke database.' . ($DEBUG ? ' [' . $e->getMessage() . ']' : ''), [], 500);
}
 
function input() {
    return json_decode(file_get_contents('php://input'), true) ?: [];
}