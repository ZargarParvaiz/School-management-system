<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../config/database.php';

// Check if the subject ID is provided via GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $subject_id = intval($_GET['id']);

    // Prepare the DELETE statement
    $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $subject_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Subject deleted successfully.";
        } else {
            $_SESSION['error'] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: " . $conn->error;
    }
} else {
    $_SESSION['error'] = "Invalid subject ID.";
}

// Redirect back to the subjects list
header("Location: subjects.php");
exit();
?>
