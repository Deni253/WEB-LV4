<?php
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Only POST allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$required = ['title', 'year', 'duration', 'genre', 'country', 'rating'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing field: $field"]);
        exit;
    }
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
        INSERT INTO "Movie" ("Title", "Year", "Duration", "Genre", "Country", "Rating")
        VALUES (:title, :year, :duration, :genre, :country, :rating)
    ');

    $stmt->execute([
        ':title' => $data['title'],
        ':year' => (int)$data['year'],
        ':duration' => (int)$data['duration'],
        ':genre' => $data['genre'],
        ':country' => $data['country'],
        ':rating' => (float)$data['rating']
    ]);

    http_response_code(201);
    echo json_encode(["message" => "Movie added successfully"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}