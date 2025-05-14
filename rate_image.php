<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'You must be logged in.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['imageId'], $input['rating'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data.']);
    exit;
}

$imageId = $input['imageId'];
$rating = (int)$input['rating'];
$userId = $_SESSION['user_id'];

if ($rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Rating must be between 1 and 5.']);
    exit;
}

$host = 'postgres-production-da53.up.railway.app';
$port = '5432';
$dbname = 'railway';
$user = 'postgres';
$pass = 'aZTRmXITkwuUkhJDdaSPQrIfVuowrdzc';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Upsert (update if exists, insert if not)
    $stmt = $pdo->prepare('
        INSERT INTO "Image_Rating" ("UserId", "ImageId", "Rating", "Rated_at")
        VALUES (:userId, :imageId, :rating, NOW())
        ON CONFLICT ("UserId", "ImageId") 
        DO UPDATE SET "Rating" = :rating, "Rated_at" = NOW()
    ');
    $stmt->execute([
        ':userId' => $userId,
        ':imageId' => $imageId,
        ':rating' => $rating
    ]);

    echo json_encode(['message' => 'Rating saved.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}