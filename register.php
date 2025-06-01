<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");
try{
    
    $pdo = new PDO("pgsql:host=hopper.proxy.rlwy.net;port=23867;dbname=railway", "postgres", "aZTRmXITkwuUkhJDdaSPQrIfVuowrdzc");
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

 
 $input = file_get_contents('php://input');
 $data = json_decode($input, true);


if (!isset($data['username'], $data['password'], $data['email'], $data['name'])) {
    http_response_code(400); 
    echo json_encode(['error' => 'Invalid input data']);
    exit;
}

$id = isset($data['id']) && !empty($data['id']) ? $data['id'] : bin2hex(random_bytes(16));

    
    $sql = 'INSERT INTO "User" ("Id", "Username", "Password", "Email", "Name", "Created_at") 
            VALUES (:id, :username, :password, :email, :name, :created_at)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':username' => $data['username'],
        ':password' => password_hash($data['password'], PASSWORD_DEFAULT), 
        ':email' => $data['email'],
        ':name' => $data['name'],
        ':created_at' => date('Y-m-d H:i:s') 
    ]);
    
    http_response_code(200); 
    echo json_encode(['message' => 'User registered successfully', 'name' => $data['name']]);
} catch (PDOException $e) {
    
    http_response_code(500); 
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    
    http_response_code(500); 
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}

?>