<?php
header('Content-Type: application/json');

// 1. MySQL connection
$mysqli = new mysqli("localhost", "root", "Nilaa@2004", "student");
if ($mysqli->connect_error) {
    die(json_encode(["status" => "error", "msg" => "MySQL Connection Failed"]));
}

// 2. MongoDB connection
require 'vendor/autoload.php';
$mongo = new MongoDB\Client("mongodb://localhost:27017");
$profiles = $mongo->college->profiles;

// 3. Get form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';
$dob = $_POST['dob'] ?? '';
$age = $_POST['age'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$gender = $_POST['gender'] ?? '';

// Validate passwords match
if ($password !== $confirmPassword) {
    echo json_encode(["status" => "error", "msg" => "Passwords do not match"]);
    exit;
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

//picture upload
$profilePicPath = '';
$uploadDir = 'uploads/';

// upload file
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Profile Picture
if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === 0) {
    $profilePicPath = $uploadDir . basename($_FILES['profilePic']['name']);
    move_uploaded_file($_FILES['profilePic']['tmp_name'], $profilePicPath);
}

// 4. Insert into MySQL
$stmt = $mysqli->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $passwordHash);// s - string

if ($stmt->execute()) {
    $userId = $stmt->insert_id;

    // Insert into MongoDB
    $profiles->insertOne([
        "userId" => $userId,
        "name" => $name,
        "email" => $email,
        "dob" => $dob,
        "age" => $age,
        "contact" => $phone,
        "address" => $address,
        "gender" => $gender,
        "profilePic" => $profilePicPath
    ]);

    echo json_encode(["status" => "success", "msg" => "Registration successful"]);
} else {
    echo json_encode(["status" => "error", "msg" => "Email already exists or database error"]);
}
?>
