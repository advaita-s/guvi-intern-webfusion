<?php
header('Content-Type: application/json');

// Read JSON input safely
$raw = file_get_contents('php://input');
$in = json_decode($raw, true);
file_put_contents(__DIR__."/debug_payload.txt", json_encode($in).PHP_EOL, FILE_APPEND);

// Debugging help (optional – comment out when not needed)
// file_put_contents(__DIR__."/debug_login.txt", $raw.PHP_EOL, FILE_APPEND);

$email = isset($in['email']) ? trim($in['email']) : '';
$pass  = isset($in['password']) ? (string)$in['password'] : '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $pass === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Connect MySQL
$conn = new mysqli('127.0.0.1','root','', 'guvi_intern');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error'=>'MySQL connection failed']);
    exit;
}

// Prepared statement
$stmt = $conn->prepare("SELECT id, name, password_hash FROM users WHERE email=?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result(); // make sure we can fetch reliably
$stmt->bind_result($uid, $name, $hash);

if (!$stmt->fetch()) {
    http_response_code(401);
    echo json_encode(['error'=>'No such user']);
    exit;
}

if (!password_verify($pass, $hash)) {
    http_response_code(401);
    echo json_encode(['error'=>'Invalid credentials']);
    exit;
}

// ✅ Redis session (optional — will fail only if Redis not installed)
try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $token = bin2hex(random_bytes(32));
    $redis->setEx("sess:$token", 86400, (string)$uid); // 1 day TTL
} catch (Exception $e) {
    // If Redis not running yet, fallback to token only
    $token = bin2hex(random_bytes(32));
}

// Success response
echo json_encode([
    'token' => $token,
    'user'  => [
        'id'    => (int)$uid,
        'name'  => $name,
        'email' => $email
    ]
]);
