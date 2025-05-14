<?php
session_start();
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Only POST allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
if (!isset($input['movieId'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing movie ID']);
    exit;
}


try {
    $pdo = new PDO("pgsql:host=hopper.proxy.rlwy.net;port=23867;dbname=railway", "postgres", "aZTRmXITkwuUkhJDdaSPQrIfVuowrdzc");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $stmt = $pdo->prepare('SELECT "Rating", "Title" FROM "Movie" WHERE "Id" = :id');
    $stmt->execute([':id' => $input['movieId']]);
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$movie) {
        http_response_code(404);
        echo json_encode(['error' => 'Movie not found']);
        exit;
    }

    
    if ($movie['Rating'] < 5.0) {
        echo json_encode([
            'warning' => 'This movie has a low rating (' . $movie['Rating'] . ') – are you sure you want to add it to your wishlist?',
            'movieTitle' => $movie['Title']
        ]);
        exit;
    }

    
    $stmt = $pdo->prepare('
        INSERT INTO "User_Wishlist" ("UserId", "MovieId")
        VALUES (:userId, :movieId)
        ON CONFLICT DO NOTHING
    ');
    $stmt->execute([
        ':userId' => $_SESSION['user_id'],
        ':movieId' => $input['movieId']
    ]);

    echo json_encode(['message' => 'Added to your wishlist']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}