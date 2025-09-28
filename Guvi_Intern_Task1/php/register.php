<?php
header('Content-Type: application/json');

// --- MySQL Connection ---
$mysqli = new mysqli("localhost", "root", "Nilaa@2004", "student");
if ($mysqli->connect_error) {
    die(json_encode(["status" => "error", "msg" => "MySQL Connection Failed"]));
}

// --- MongoDB Connection ---
require 'vendor/autoload.php';
$mongo = new MongoDB\Client("mongodb://localhost:27017");
$profiles = $mongo->college->profiles;

// --- Get Form Data ---
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';
$dob = $_POST['dob'] ?? '';
$phone = $_POST['phone'] ?? '';

//Password Validation
if ($password !== $confirmPassword) {
    echo json_encode(["status" => "error", "msg" => "Passwords do not match"]);
    exit;
}

//Hash Password
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

//Picture Upload 
$profilePicPath = '';
if (!empty($_FILES['profilePic']['name']) && $_FILES['profilePic']['error'] === 0) {
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    $profilePicPath = $uploadDir . basename($_FILES['profilePic']['name']);
    move_uploaded_file($_FILES['profilePic']['tmp_name'], $profilePicPath);
}

//MySQL
$stmt = $mysqli->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $passwordHash);

if ($stmt->execute()) {
    $userId = $stmt->insert_id;

    // --- Insert into MongoDB ---
    $profiles->insertOne([
        "userId" => $userId,
        "name" => $name,
        "email" => $email,
        "dob" => $dob,
        "contact" => $phone,
        "profilePic" => $profilePicPath
    ]);

    echo json_encode(["status" => "success", "msg" => "Registration successful"]);
} else {
    echo json_encode(["status" => "error", "msg" => "Database error or email already exists"]);
}
?>
