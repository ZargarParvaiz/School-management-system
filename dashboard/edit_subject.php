<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../config/database.php';

// Check if the subject ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid subject ID.";
    header("Location: subjects.php");
    exit();
}

$subject_id = intval($_GET['id']);

// Fetch the current subject details
$stmt = $conn->prepare("SELECT id, name, code, class_id FROM subjects WHERE id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();
$subject = $result->fetch_assoc();
$stmt->close();

if (!$subject) {
    $_SESSION['error'] = "Subject not found.";
    header("Location: subjects.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
    $subject_name = isset($_POST['subject_name']) ? trim($_POST['subject_name']) : '';
    $subject_code = isset($_POST['subject_code']) ? trim($_POST['subject_code']) : '';

    if ($class_id > 0 && !empty($subject_name) && !empty($subject_code)) {
        $stmt = $conn->prepare("UPDATE subjects SET class_id = ?, name = ?, code = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("issi", $class_id, $subject_name, $subject_code, $subject_id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Subject updated successfully.";
            } else {
                $_SESSION['error'] = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Database error: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "All fields are required.";
    }

    header("Location: subjects.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Subject</h2>
        
        <?php if (isset($_SESSION['success'])) { ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php } elseif (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php } ?>

        <form action="edit_subject.php?id=<?php echo $subject_id; ?>" method="POST">
            <div class="mb-3">
                <label for="class_id" class="form-label">Class</label>
                <select class="form-select" name="class_id" required>
                    <?php 
                    $classes_result = $conn->query("SELECT id, name FROM classes");
                    while ($class = $classes_result->fetch_assoc()) { ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo ($class['id'] == $subject['class_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="subject_name" class="form-label">Subject Name</label>
                <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?php echo htmlspecialchars($subject['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="subject_code" class="form-label">Subject Code</label>
                <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?php echo htmlspecialchars($subject['code']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="subjects.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
