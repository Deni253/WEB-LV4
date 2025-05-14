<?php

$host = 'postgres.railway.internal';
$port = '5432';
$dbname = 'railway';
$user = 'postgres';
$pass = 'aZTRmXITkwuUkhJDdaSPQrIfVuowrdzc';

try{
    // Create a new PDO instance
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

 // Get the raw POST data
 $input = file_get_contents('php://input');
 $data = json_decode($input, true);

// Validate the input data
if (!isset($data['username'], $data['password'], $data['email'], $data['name'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid input data']);
    exit;
}

$id = isset($data['id']) && !empty($data['id']) ? $data['id'] : bin2hex(random_bytes(16));

    // Prepare and execute the SQL query
    $sql = 'INSERT INTO "User" ("Id", "Username", "Password", "Email", "Name", "Created_at") 
            VALUES (:id, :username, :password, :email, :name, :created_at)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':username' => $data['username'],
        ':password' => password_hash($data['password'], PASSWORD_DEFAULT), // Hash the password
        ':email' => $data['email'],
        ':name' => $data['name'],
        ':created_at' => date('Y-m-d H:i:s') // Current timestamp
    ]);
    // Send a success response
    http_response_code(200); // OK
    echo json_encode(['message' => 'User registered successfully', 'name' => $data['name']]);
} catch (PDOException $e) {
    // Handle database errors
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Handle other errors
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}

?>