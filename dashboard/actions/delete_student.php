<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    die("No student ID provided.");
}

$student_id = intval($_GET['id']);

// Delete the student
$del_sql = "DELETE FROM students WHERE id=? LIMIT 1";
$stmt = $conn->prepare($del_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();

// Redirect back with a success flag
header("Location: ../students.php?deleted=1");
exit();
