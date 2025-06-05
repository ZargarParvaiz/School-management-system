<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../config/database.php';

// 1) Determine selected class from GET
$selected_class = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

// 2) If user submitted the form to save all students
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_all'])) {
    // We'll get the class_id from hidden input
    $class_id = intval($_POST['class_id']);

    // 'block_results' and 'block_message' come in as arrays indexed by student_id
    $blockResultsArray = isset($_POST['block_results']) ? $_POST['block_results'] : array();
    $messagesArray     = isset($_POST['block_message']) ? $_POST['block_message'] : array();

    // For each student in that class, update block_results and block_message
    // We'll fetch them again to ensure we only update valid student IDs
    $students_sql = "
        SELECT id
        FROM students
        WHERE class_id = ?
        ORDER BY first_name
    ";
    $stmt_stu = $conn->prepare($students_sql);
    $stmt_stu->bind_param("i", $class_id);
    $stmt_stu->execute();
    $students_res = $stmt_stu->get_result();

    while ($stu = $students_res->fetch_assoc()) {
        $sid = $stu['id'];
        // If the checkbox was checked => block_results=1, else 0
        $isBlocked = isset($blockResultsArray[$sid]) ? 1 : 0;
        // The message might not exist if no input was given, so default to empty
        $msg = isset($messagesArray[$sid]) ? trim($messagesArray[$sid]) : '';

        // Update in DB
        $upd = $conn->prepare("
            UPDATE students
            SET block_results = ?, block_message = ?
            WHERE id = ?
        ");
        $upd->bind_param("isi", $isBlocked, $msg, $sid);
        $upd->execute();
    }

    // Redirect back to the same class filter
    header("Location: payment.php?class_id=".$class_id);
    exit();
}

// 3) Fetch all classes for the dropdown
$classes_sql = "SELECT id, name, section FROM classes ORDER BY name";
$classes_res = $conn->query($classes_sql);

// 4) If a class is selected, fetch students
$students_res = null;
if ($selected_class > 0) {
    $students_sql = "
        SELECT id, first_name, last_name, block_results, block_message
        FROM students
        WHERE class_id = ?
        ORDER BY first_name
    ";
    $stmt_stu = $conn->prepare($students_sql);
    $stmt_stu->bind_param("i", $selected_class);
    $stmt_stu->execute();
    $students_res = $stmt_stu->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment/Block Results - Student Management System</title>
    <!-- Font Awesome + Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your existing theme CSS -->
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
      /* Optional styling for the form-switch */
      .form-check-input[type='checkbox'] {
        cursor: pointer;
      }
      .form-check-label {
        cursor: pointer;
        margin-left: 8px;
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
        <a href="subjects.php" class="nav-link">
          <i class="fas fa-fw fa-book"></i>
          <span>Subjects</span>
        </a>
         <a href="payment.php" class="nav-link active">
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
            <h1 class="h3 mb-4 text-gray-800">Payment / Block Results</h1>
            <p>Select a class below to toggle students’ result access or leave a message. Then click <strong>Save All</strong>.</p>

            <!-- Class Selection Form -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-auto">
                    <label for="class_id" class="col-form-label fw-bold">Select Class:</label>
                </div>
                <div class="col-auto">
                    <select name="class_id" id="class_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Choose Class --</option>
                        <?php while($cls = $classes_res->fetch_assoc()): ?>
                            <?php 
                              $label = $cls['name'];
                              if (!empty($cls['section'])) {
                                  $label .= ' (Section ' . $cls['section'] . ')';
                              }
                            ?>
                            <option value="<?php echo $cls['id']; ?>"
                                <?php echo ($selected_class == $cls['id']) ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>

            <?php if ($selected_class > 0 && $students_res): ?>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <?php if ($students_res->num_rows === 0): ?>
                        <p>No students found in this class.</p>
                    <?php else: ?>
                    <!-- Single form for the entire table -->
                    <form action="" method="POST">
                        <input type="hidden" name="class_id" value="<?php echo $selected_class; ?>">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Block Results?</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($stu = $students_res->fetch_assoc()): ?>
                                <tr>
                                    <?php
                                      $defaultMsg = !empty($stu['block_message']) 
                                                    ? $stu['block_message']
                                                    : "Please clear your dues";
                                    ?>
                                    <td>
                                        <?php echo $stu['first_name'] . ' ' . $stu['last_name']; ?>
                                        <!-- We'll store student_id in hidden array field -->
                                        <input type="hidden" name="student_ids[]" value="<?php echo $stu['id']; ?>">
                                    </td>
                                    <td>
                                        <!-- form-switch style -->
                                        <div class="form-check form-switch">
                                            <!-- If checked => name=\"block_results[student_id]\", we check in backend -->
                                            <input class="form-check-input" type="checkbox"
                                                   name="block_results[<?php echo $stu['id']; ?>]"
                                                   id="block_<?php echo $stu['id']; ?>"
                                                   <?php echo ($stu['block_results'] == 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="block_<?php echo $stu['id']; ?>">
                                                <?php echo ($stu['block_results'] == 1) ? 'Blocked' : 'Unblocked'; ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" 
                                               name="block_message[<?php echo $stu['id']; ?>]"
                                               class="form-control"
                                               value="<?php echo htmlspecialchars($defaultMsg); ?>">
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                        <!-- Single Save All button -->
                        <button type="submit" name="save_all" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save All
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
                <p>Please select a class to manage payments/blocking.</p>
            <?php endif; ?>

        </div> <!-- container-fluid px-4 -->
    </div> <!-- flex-grow-1 -->
</div> <!-- d-flex -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
