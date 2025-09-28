<?php
header('Content-Type: application/json');

// MySQL connection
$mysqli = new mysqli("localhost", "root", "Nilaa@2004", "student");

// Redis connection
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $mysqli->prepare("SELECT id, password FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result && password_verify($password, $result['password'])) {
    $sessionId = bin2hex(random_bytes(16));

    $redis->set($sessionId, $result['id']);
    $redis->expire($sessionId, 3600); // 1 hour expiry

    echo json_encode(["status"=>"success", "sessionId"=>$sessionId]);
} else {
    echo json_encode(["status"=>"error", "msg"=>"Invalid login"]);
}
?>
