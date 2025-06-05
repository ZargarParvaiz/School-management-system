<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $section = $_POST['section'];
    $teacher_id = intval($_POST['teacher_id']);

    // Insert new class
    $sql = "INSERT INTO classes (name, section, teacher_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $section, $teacher_id);
    $stmt->execute();

    // Redirect back
    header("Location: ../classes.php");
    exit();
}
