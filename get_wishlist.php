<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
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

    $stmt = $pdo->prepare('
    SELECT m."Id", m."Title", m."Year", m."Duration", m."Genre", m."Country", m."Rating"
    FROM "User_Wishlist" uw
    JOIN "Movie" m ON uw."MovieId" = m."Id"
    WHERE uw."UserId" = :userId
    ORDER BY uw."Added_at" DESC
    ');
    $stmt->execute([':userId' => $_SESSION['user_id']]);

    $wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($wishlist);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}