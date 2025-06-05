

<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
    $class_id   = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
    $exam_type  = isset($_POST['exam_type']) ? trim($_POST['exam_type']) : '';
    $exam_date  = isset($_POST['exam_date']) ? $_POST['exam_date'] : '';
    $marks      = isset($_POST['marks']) ? $_POST['marks'] : [];

    // Validate
    if ($student_id > 0 && $class_id > 0 && !empty($exam_type) && !empty($exam_date) && !empty($marks)) {
        // Insert marks for each subject
        foreach ($marks as $subject_id => $score) {
            $score = intval($score);

            // Prepare INSERT query
            $stmt = $conn->prepare("
                INSERT INTO results (student_id, subject_id, marks, exam_type, exam_date)
                VALUES (?, ?, ?, ?, ?)
            ");
            if ($stmt) {
                $stmt->bind_param("iiiss", $student_id, $subject_id, $score, $exam_type, $exam_date);
                if (!$stmt->execute()) {
                    $_SESSION['error'] = "Error saving marks for subject_id $subject_id: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $_SESSION['error'] = "Database Error (prepare): " . $conn->error;
            }
        }

        if (!isset($_SESSION['error'])) {
            $_SESSION['success'] = "Marks saved successfully for this exam!";
        }
    } else {
        $_SESSION['error'] = "Invalid data. Please ensure all fields are filled.";
    }

    // Redirect back to results page (step1) or you can specify step2
    header("Location: ../dashboard/results.php?step=step1");
    exit();
}
?>
