<?php
session_start();
session_regenerate_id(true);
header("Content-Type: application/json");

try {
    $pdo = new PDO("pgsql:host=hopper.proxy.rlwy.net;port=23867;dbname=railway", "postgres", "aZTRmXITkwuUkhJDdaSPQrIfVuowrdzc");
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['username'], $input['password']) || empty($input['username']) || empty($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password are required']);
        exit;
    }

    $username = trim($input['username']); 
    $password = $input['password'];

    $stmt = $pdo->prepare('SELECT "Id", "Username", "Password", "Name", "Role" FROM "User" WHERE "Username" = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid username or password']);
        exit;
    }

    if (password_verify($password, $user['Password'])) {
        $_SESSION['user_id'] = $user['Id'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['name'] = $user['Name'];
        $_SESSION['role'] = $user['Role'];

        http_response_code(200);
        echo json_encode([
            'message' => 'Login successful',
            'username' => $user['Username'],
            'name' => $user['Name'],
            'role' => $user['Role']
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid username or password']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>