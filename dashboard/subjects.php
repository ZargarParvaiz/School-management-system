<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../config/database.php';
// Handle "Add Subject" form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form inputs
    $class_id     = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
    $subject_name = isset($_POST['subject_name']) ? trim($_POST['subject_name']) : '';
    $subject_code = isset($_POST['subject_code']) ? trim($_POST['subject_code']) : '';

    // Validate inputs
    if ($class_id > 0 && !empty($subject_name) && !empty($subject_code)) {
        // Insert new subject
        $stmt = $conn->prepare("INSERT INTO subjects (class_id, name, code) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iss", $class_id, $subject_name, $subject_code);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Subject added to class successfully.";
            } else {
                $_SESSION['error'] = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Database error: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "All fields (class, subject name, subject code) are required.";
    }

    // Redirect back to the same page
    header("Location: subjects.php");
    exit();
}

// Fetch all classes (we'll display them in an accordion)
$classes_query = "SELECT id, name FROM classes ORDER BY id ASC";
$classes_result = $conn->query($classes_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subjects - Student Management System</title>
    <!-- Font Awesome + Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
      <style>
    /* Optional: style for the top heading */
    .school-title {
      font-size: 1.4rem;
      font-weight: 700;
      margin: 0;
      color: #555;
    }
    /* Make text center on small devices, spaced on large devices */
    .topbar-container {
      /* On small screens, center content. On md+ screens, spread out. */
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;         /* default center for mobile */
    }
    @media (min-width: 768px) {
      .topbar-container {
        justify-content: space-between; /* separate text and logo on larger screens */
      }
    }
    /* The single logo on the right */
    .topbar-logo {
      height: 45px; 
      margin-top: 20px; /* minor spacing from top if needed */
    }
  </style>


    <style>
    /* OPTIONAL: Some additional styling for the accordion */
    .accordion-button {
        font-weight: 600;
    }
    .accordion-button:not(.collapsed) {
        background-color: #f1f1f1;
    }
    </style>
</head>
<body>
<div class="d-flex">
<!-- Sidebar -->
        <div class="sidebar" style="width: 250px;">
            <div class="sidebar-brand d-flex align-items-center justify-content-center">
                <i class="fas fa-graduation-cap fa-2x"></i>
                <span class="ms-2">SMS Dashboard</span>
            </div>
            <hr class="sidebar-divider bg-light">
            <div class="nav flex-column">
                <a href="index.php" class="nav-link">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="students.php" class="nav-link">
                    <i class="fas fa-fw fa-user-graduate"></i>
                    <span>Students</span>
                </a>
                <a href="teachers.php" class="nav-link">
                    <i class="fas fa-fw fa-chalkboard-teacher"></i>
                    <span>Teachers</span>
                </a>
                <a href="classes.php" class="nav-link">
                    <i class="fas fa-fw fa-chalkboard"></i>
                    <span>Classes</span>
                </a>
                <a href="attendance.php" class="nav-link">
                    <i class="fas fa-fw fa-calendar-check"></i>
                    <span>Attendance</span>
                </a>
                <a href="results.php" class="nav-link">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Results</span>
                </a>
                <a href="subjects.php" class="nav-link active">
                 <i class="fas fa-fw fa-book"></i>
                 <span>Subjects</span>
                </a>
                <a href="payment.php" class="nav-link">
                <i class="fas fa-fw fa-money-bill-wave"></i>
                <span>Payments</span>
            </a> 
                <a href="../auth/logout.php" class="nav-link">
                    <i class="fas fa-fw fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

    <!-- Main Content -->
<div class="flex-grow-1">
      <!-- Single row topbar with center text on mobile, right logo on larger screens -->
      <nav class="navbar navbar-expand bg-white shadow mb-4" style="min-height: 60px;">
        <div class="container-fluid topbar-container">
          <!-- School Name in center on mobile, left on bigger screens -->
          <h2 class="school-title mb-0">ARMY GOODWILL SCHOOL KRUSAN</h2>
          <!-- Single Logo on the right on bigger screens -->
          <img src="../assets/images/logo-left.png" alt="School Logo" class="topbar-logo">
        </div>
      </nav>

        <div class="container-fluid px-4">
            <h2 class="text-center mb-4">Subjects by Class</h2>

            <!-- Display success/error messages -->
            <?php if (isset($_SESSION['success'])) { ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php } elseif (isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php } ?>

            <div class="accordion" id="classAccordion">
                <?php
                $i = 0;
                while ($class = $classes_result->fetch_assoc()) {
                    $i++;
                    $classId = $class['id'];
                    $className = $class['name'];

                    // Fetch subjects for this class
                    $subjects_sql = "SELECT id, name, code FROM subjects WHERE class_id = $classId ORDER BY id DESC";
                    $subjects_res = $conn->query($subjects_sql);
                ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?php echo $i; ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse<?php echo $i; ?>" aria-expanded="false"
                                aria-controls="collapse<?php echo $i; ?>">
                            <?php echo htmlspecialchars($className); ?>
                        </button>
                    </h2>
                    <div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse"
                         aria-labelledby="heading<?php echo $i; ?>" data-bs-parent="#classAccordion">
                        <div class="accordion-body">
                            <!-- Table of subjects for this class -->
                            <div class="mb-3 text-end">
                                <!-- Button to trigger the Add Subject modal for this class -->
                                <button class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addSubjectModal<?php echo $classId; ?>">
                                    <i class="fas fa-plus"></i> Add Subject to <?php echo htmlspecialchars($className); ?>
                                </button>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Subject Name</th>
                                    <th>Code</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while ($sub = $subjects_res->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $sub['id']; ?></td>
                                        <td><?php echo htmlspecialchars($sub['name']); ?></td>
                                        <td><?php echo htmlspecialchars($sub['code']); ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal for adding subject to this specific class -->
                <div class="modal fade" id="addSubjectModal<?php echo $classId; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="subjects.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Subject to <?php echo htmlspecialchars($className); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="class_id" value="<?php echo $classId; ?>">
                                    <div class="mb-3">
                                        <label for="subject_name" class="form-label">Subject Name</label>
                                        <input type="text" class="form-control" name="subject_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="subject_code" class="form-label">Subject Code</label>
                                        <input type="text" class="form-control" name="subject_code" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Add Subject</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } // end while loop for classes ?>
            </div> <!-- accordion -->
        </div> <!-- container-fluid px-4 -->
    </div> <!-- flex-grow-1 -->
</div> <!-- d-flex -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
