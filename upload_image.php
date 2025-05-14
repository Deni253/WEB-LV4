<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'You must be logged in to upload.']);
    exit;
}

$uploadDir = __DIR__ . '/slike/';
$relativePathPrefix = 'slike/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded or upload error.']);
    exit;
}

$file = $_FILES['image'];
$allowedTypes = ['image/jpeg', 'image/png'];
if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Only JPEG and PNG images are allowed.']);
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['error' => 'File exceeds 5MB limit.']);
    exit;
}

$filename = basename($file['name']);
$filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $filename);
$targetPath = $uploadDir . $filename;
$relativePath = $relativePathPrefix . $filename;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to move uploaded file.']);
    exit;
}


$host = 'postgres.railway.internal';
$port = '5432';
$dbname = 'railway';
$user = 'postgres';
$pass = 'aZTRmXITkwuUkhJDdaSPQrIfVuowrdzc';


try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('INSERT INTO "Image" ("Filename", "Path", "Source") VALUES (:filename, :path, :source)');
    $stmt->execute([
        ':filename' => $filename,
        ':path' => $relativePath,
        ':source' => 'local'
    ]);

    echo json_encode(['message' => 'Image uploaded successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}