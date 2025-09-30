<?php
header('Content-Type: application/json');
$in = json_decode(file_get_contents('php://input'), true) ?? [];
$name = trim($in['name'] ?? '');
$email = trim($in['email'] ?? '');
$pass = (string)($in['password'] ?? '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
  http_response_code(400); echo json_encode(['error'=>'Invalid input']); exit;
}

$conn = new mysqli('127.0.0.1','root','', 'guvi_intern');
if ($conn->connect_error) { http_response_code(500); echo json_encode(['error'=>'MySQL error']); exit; }
$conn->set_charset('utf8mb4');

$hash = password_hash($pass, PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO users (name,email,password_hash) VALUES (?,?,?)");
$stmt->bind_param('sss', $name, $email, $hash);

if ($stmt->execute()) {
  echo json_encode(['message'=>'Registered successfully']);
} else {
  http_response_code(($conn->errno===1062)?409:500);
  echo json_encode(['error'=> ($conn->errno===1062?'Email already registered':'DB error') ]);
}
