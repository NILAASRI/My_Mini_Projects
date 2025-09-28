<?php
header('Content-Type: application/json');
require 'vendor/autoload.php';

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$mysqli = new mysqli("localhost", "root", "Nilaa@2004", "student");

$mongo = new MongoDB\Client("mongodb://localhost:27017");
$profiles = $mongo->new_profiles->profiles;

// Get session and user ID
$sessionId = $_POST['sessionId'] ?? '';
$userId = $redis->get($sessionId);

if (!$userId) {
    echo json_encode(["status" => "error", "msg" => "Session expired"]);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === "fetch") {
    // Fetch from MySQL
    $stmt = $mysqli->prepare("SELECT id, email FROM users WHERE id=?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Fetch from MongoDB
    $profile = $profiles->findOne(["userId" => (int)$userId]);

    echo json_encode([
        "status" => "success",
        "data" => [
            "id" => $user['id'],
            "email" => $user['email'],
            "name" => $profile['name'] ?? "",
            "dob" => $profile['dob'] ?? "",
            "contact" => $profile['contact'] ?? ""
        ]
    ]);
    exit;
}

if ($action === "update") {
    $profiles->updateOne(
        ["userId" => (int)$userId],
        ['$set' => [
            "name" => $_POST['name'] ?? '',
            "dob" => $_POST['dob'] ?? '',
            "contact" => $_POST['contact'] ?? ''
        ]]
    );

    echo json_encode(["status" => "success", "msg" => "Profile updated"]);
    exit;
}

echo json_encode(["status" => "error", "msg" => "Invalid action"]);
?>
