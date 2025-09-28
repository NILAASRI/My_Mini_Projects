<?php
header('Content-Type: application/json');
require 'vendor/autoload.php';

// Redis
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

// MySQL
$mysqli = new mysqli("localhost", "root", "Nilaa@2004", "student");

// MongoDB
$mongo = new MongoDB\Client("mongodb://localhost:27017");
$profiles = $mongo->college->profiles;

$sessionId = $_POST['sessionId'] ?? '';
$userId = $redis->get($sessionId);

if (!$userId) {
    echo json_encode(["status"=>"error","msg"=>"Session expired"]);
    exit;
}

// --- Fetch User Profile ---
if (isset($_POST['action']) && $_POST['action'] === "fetch") {
    // Get email from MySQL
    $stmt = $mysqli->prepare("SELECT id, email FROM users WHERE id=?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    // Get profile from MongoDB
    $profile = $profiles->findOne(["userId" => (int)$userId]);

    echo json_encode([
        "status"=>"success",
        "data"=>[
            "id"=>$result['id'],
            "email"=>$result['email'],
            "name"=>$profile['name'] ?? "",
            "dob"=>$profile['dob'] ?? "",
            "contact"=>$profile['contact'] ?? "",
        ]
    ]);
    exit;
}

// --- Update User Profile ---
if (isset($_POST['action']) && $_POST['action'] === "update") {
    $name = $_POST['name'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $contact = $_POST['contact'] ?? '';

    $profiles->updateOne(
        ["userId" => (int)$userId],
        ['$set' => [
            "name" => $name,
            "dob" => $dob,
            "contact" => $contact
        ]]
    );

    echo json_encode(["status"=>"success","msg"=>"Profile updated"]);
    exit;
}
?>
