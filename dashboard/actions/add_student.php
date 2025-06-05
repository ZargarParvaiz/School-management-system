<?php
session_start();
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_number = mysqli_real_escape_string($conn, $_POST['registration_number']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $class_id = mysqli_real_escape_string($conn, $_POST['class_id']);

    // Check if registration number already exists
    $check_sql = "SELECT id FROM students WHERE registration_number = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $registration_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Registration number already exists!";
        header("Location: ../students.php");
        exit();
    }

    // Insert new student
    $sql = "INSERT INTO students (registration_number, first_name, last_name, email, phone, class_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $registration_number, $first_name, $last_name, $email, $phone, $class_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Student added successfully!";
    } else {
        $_SESSION['error'] = "Error adding student: " . $conn->error;
    }

    header("Location: ../students.php");
    exit();
}
?>
