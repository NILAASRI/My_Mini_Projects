<?php
header('Content-Type: application/json');

// MySQL
$mysqli = new mysqli("localhost","root","","student");
if($mysqli->connect_error){
    die(json_encode(["status"=>"error","msg"=>"MySQL Error: ".$mysqli->connect_error]));
}

// MongoDB
require '../vendor/autoload.php';
use MongoDB\Client;
try {
    $mongo = new Client("mongodb://localhost:27017");
    $profiles = $mongo->new_profiles->profiles;
}catch(Exception $e){
    die(json_encode(["status"=>"error","msg"=>"MongoDB Error: ".$e->getMessage()]));
}

// Get POST Data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirmPassword'] ?? '';
$dob = $_POST['dob'] ?? '';
$phone = trim($_POST['phone'] ?? '');
$age = intval($_POST['age'] ?? 0);
$address = trim($_POST['address'] ?? '');
$gender = trim($_POST['gender'] ?? '');

// Validation
if(empty($name)||empty($email)||empty($password)){
    echo json_encode(["status"=>"error","msg"=>"Name, Email, Password required"]);
    exit;
}
if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    echo json_encode(["status"=>"error","msg"=>"Invalid email"]);
    exit;
}
if($password!==$confirm){
    echo json_encode(["status"=>"error","msg"=>"Passwords do not match"]);
    exit;
}

// Hash password
$passHash = password_hash($password,PASSWORD_BCRYPT);

// MySQL Insert
$stmt = $mysqli->prepare("INSERT INTO users (email,password) VALUES (?,?)");
$stmt->bind_param("ss",$email,$passHash);
if($stmt->execute()){
    $userId = $stmt->insert_id;
    // MongoDB Insert
    try{
        $profiles->insertOne([
            "userId"=>$userId,
            "name"=>$name,
            "email"=>$email,
            "dob"=>$dob,
            "contact"=>$phone,
            "age"=>$age,
            "address"=>$address,
            "gender"=>$gender,
            "created_at"=>new MongoDB\BSON\UTCDateTime()
        ]);
        echo json_encode(["status"=>"success","msg"=>"Registered successfully"]);
    }catch(Exception $e){
        $mysqli->query("DELETE FROM users WHERE id=$userId"); // rollback
        echo json_encode(["status"=>"error","msg"=>"MongoDB insert failed"]);
    }
}else{
    echo json_encode(["status"=>"error","msg"=>"Email might already exist"]);
}

$stmt->close();
$mysqli->close();
?>
