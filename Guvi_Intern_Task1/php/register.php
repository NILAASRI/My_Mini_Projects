<?php
header('Content-Type: application/json');

// --- MySQL Connection ---
$mysqli = new mysqli("localhost", "root", "Nilaa@2004", "student");
if ($mysqli->connect_error) {
    die(json_encode(["status" => "error", "msg" => "MySQL Connection Failed: ".$mysqli->connect_error]));
}

// --- MongoDB Connection ---
require 'vendor/autoload.php';
try {
    $mongo = new MongoDB\Client("mongodb://localhost:27017");
    $profiles = $mongo->new_profiles->profiles;
} catch (Exception $e) {
    die(json_encode(["status"=>"error","msg"=>"MongoDB Connection Failed: ".$e->getMessage()]));
}

// --- Get Form Data ---
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';
$dob = $_POST['dob'] ?? '';
$phone = $_POST['phone'] ?? '';
$age = $_POST['age'] ?? '';
$address = $_POST['address'] ?? '';
$gender = $_POST['gender'] ?? '';

// --- Password Validation ---
if ($password !== $confirmPassword) {
    echo json_encode(["status" => "error", "msg" => "Passwords do not match"]);
    exit;
}

// --- Hash Password ---
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// --- MySQL Insert ---
$stmt = $mysqli->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
if (!$stmt) {
    echo json_encode(["status"=>"error","msg"=>"MySQL Prepare Failed: ".$mysqli->error]);
    exit;
}
$stmt->bind_param("sss", $name, $email, $passwordHash);

if ($stmt->execute()) {
    $userId = $stmt->insert_id;

    // --- MongoDB Insert ---
    try {
        $profiles->insertOne([
            "userId" => $userId,
            "name" => $name,
            "email" => $email,
            "dob" => $dob,
            "contact" => $phone,
            "age" => $age,
            "address" => $address,
            "gender" => $gender
        ]);
    } catch (Exception $e) {
        echo json_encode(["status"=>"error","msg"=>"MongoDB Insert Failed: ".$e->getMessage()]);
        exit;
    }

    echo json_encode(["status" => "success", "msg" => "Registration successful"]);
} else {
    echo json_encode(["status" => "error", "msg" => "Database error or email already exists: ".$stmt->error]);
}
?>
