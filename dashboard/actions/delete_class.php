<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    die("No class ID provided.");
}
$class_id = intval($_GET['id']);

// Delete the class
$del_sql = "DELETE FROM classes WHERE id=? LIMIT 1";
$stmt = $conn->prepare($del_sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();

// Redirect back with success flag
header("Location: ../classes.php?deleted=1");
exit();
