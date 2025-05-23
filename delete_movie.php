<?php
header("Content-Type: application/json");

try {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id']) || empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Movie ID is required.']);
        exit;
    }


    $pdo = new PDO("pgsql:host=hopper.proxy.rlwy.net;port=23867;dbname=railway", "postgres", "aZTRmXITkwuUkhJDdaSPQrIfVuowrdzc");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('DELETE FROM "Movie" WHERE "Id" = :id');
    $stmt->execute([':id' => $input['id']]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['message' => 'Movie deleted successfully.']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Movie not found.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}