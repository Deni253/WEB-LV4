<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Only POST allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['movieId'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing movie ID']);
    exit;
}


try {
    $pdo = new PDO("pgsql:host=hopper.proxy.rlwy.net;port=23867;dbname=railway", "postgres", "aZTRmXITkwuUkhJDdaSPQrIfVuowrdzc");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('
        DELETE FROM "User_Wishlist"
        WHERE "UserId" = :userId AND "MovieId" = :movieId
    ');

    $stmt->execute([
        ':userId' => $_SESSION['user_id'],
        ':movieId' => $input['movieId']
    ]);

    echo json_encode(['message' => 'Movie removed from wishlist']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}