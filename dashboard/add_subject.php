<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
    $subject_id = isset($_POST['subject_id']) ? intval($_POST['subject_id']) : 0;
    
    if ($class_id > 0 && $subject_id > 0) {
        $stmt = $conn->prepare("INSERT INTO syllabus (class_id, subject_id) VALUES (?, ?) 
                                ON DUPLICATE KEY UPDATE subject_id = VALUES(subject_id)");
        if ($stmt) {
            $stmt->bind_param("ii", $class_id, $subject_id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Syllabus updated successfully.";
            } else {
                $_SESSION['error'] = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Database error: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Invalid class or subject selection.";
    }
    
    header("Location: syllabus.php");
    exit();
}
?>
