<?php
header('Content-Type: application/json');

// --- Auth via Redis token ---
$token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? '';
if ($token === '') { http_response_code(401); echo json_encode(['error'=>'Missing token']); exit; }

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$uid = $redis->get("sess:$token");
if (!$uid) { http_response_code(401); echo json_encode(['error'=>'Invalid/expired token']); exit; }
$uid = (int)$uid;

// --- Mongo (mongodb extension, no composer) ---
$manager = new MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");
$db = "guvi_intern";
$coll = "profiles";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  // Get basic info from MySQL (prepared)
  $conn = new mysqli('127.0.0.1','root','', 'guvi_intern');
  if ($conn->connect_error) { http_response_code(500); echo json_encode(['error'=>'MySQL error']); exit; }
  $stmt = $conn->prepare("SELECT name,email FROM users WHERE id=?");
  $stmt->bind_param('i', $uid); $stmt->execute(); $stmt->bind_result($name, $email); $stmt->fetch();

  // Fetch profile from Mongo
  $query = new MongoDB\Driver\Query(['user_id'=>$uid], ['limit'=>1]);
  $cursor = $manager->executeQuery("$db.$coll", $query);
  $doc = current($cursor->toArray()) ?: null;

  echo json_encode([
    'user'=>['id'=>$uid,'name'=>$name,'email'=>$email],
    'profile'=>[
      'age'=>$doc->age ?? null,
      'dob'=>$doc->dob ?? null,
      'contact'=>$doc->contact ?? null
    ]
  ]);
  exit;
}

// POST = upsert profile
$in = json_decode(file_get_contents('php://input'), true) ?? [];
$age = isset($in['age']) ? (int)$in['age'] : null;
$dob = $in['dob'] ?? null;
$contact = $in['contact'] ?? null;

$bulk = new MongoDB\Driver\BulkWrite();
$bulk->update(
  ['user_id'=>$uid],
  ['$set'=>['user_id'=>$uid,'age'=>$age,'dob'=>$dob,'contact'=>$contact,'updated_at'=>new MongoDB\BSON\UTCDateTime()]],
  ['upsert'=>true]
);
$manager->executeBulkWrite("$db.$coll", $bulk);

echo json_encode(['message'=>'Profile updated']);
