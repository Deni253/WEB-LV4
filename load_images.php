<?php
header('Content-Type: application/json');

try {
    $pdo = new PDO("pgsql:host=hopper.proxy.rlwy.net;port=23867;dbname=railway", "postgres", "aZTRmXITkwuUkhJDdaSPQrIfVuowrdzc");
    
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query('
        SELECT 
            i."Id" AS id,
            i."Filename" AS filename,
            i."Path" AS path,
            COALESCE(AVG(r."Rating"), 0) AS avg_rating
        FROM "Image" i
        LEFT JOIN "Image_Rating" r ON i."Id" = r."ImageId"
        GROUP BY i."Id"
        ORDER BY i."Uploaded_at" DESC
    ');

    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($images);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}