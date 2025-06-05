<?php
$host = "sql112.infinityfree.com";
$username = "if0_38284087";
$password = "Parvaizzargar";
$database = "if0_38284087_student_management";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
