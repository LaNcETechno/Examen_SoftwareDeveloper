<?php
// db.php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "webshop";

// Maak verbinding
$conn = new mysqli($host, $user, $pass, $dbname);

// Check verbinding
if ($conn->connect_error) {
    die("Database connectie mislukt: " . $conn->connect_error);
}

// Zorg dat we UTF-8 gebruiken
$conn->set_charset("utf8mb4");
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);