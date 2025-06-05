<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../config/database.php';

// 1) Step logic: 'step1' (choose class), 'step2' (choose student), 'enter_marks' (final)
$step = isset($_GET['step']) ? $_GET['step'] : 'step1';

// We'll track selected class & student
$selected_class   = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$selected_student = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

// Fetch all classes for the first dropdown
$classes_sql    = "SELECT id, name, section FROM classes ORDER BY name";
$classes_result = $conn->query($classes_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Results - Student Management System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <a href="results.php" class="nav-link active">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Results</span>
                </a>
                <a href="subjects.php" class="nav-link">
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
      <h1 class="h3 mb-4 text-gray-800">Results Management</h1>

      <?php
      // Show any session messages
      if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
        unset($_SESSION['success']);
      } elseif (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
      }
      ?>

      <!-- Step 1: Choose Class -->
      <?php if ($step === 'step1' || !$step): ?>
        <div class="card shadow mb-4">
          <div class="card-body">
            <form method="GET">
              <input type="hidden" name="step" value="step2">
              <div class="mb-3">
                <label for="class_id" class="form-label">Select Class</label>
                <select class="form-select" id="class_id" name="class_id" required>
                  <option value="">Choose Class...</option>
                  <?php
                  $classes_result->data_seek(0); // reset pointer if needed
                  while($class = $classes_result->fetch_assoc()):
                  ?>
                    <option value="<?php echo $class['id']; ?>">
                      <?php echo $class['name'].' - '.$class['section']; ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <button type="submit" class="btn btn-primary">Load Students</button>
            </form>
          </div>
        </div>
      <?php endif; ?>

      <!-- Step 2: Choose Student -->
      <?php if ($step === 'step2' && $selected_class > 0): ?>
        <?php
          // Fetch students of that class
          $students_sql = "SELECT id, first_name, last_name FROM students WHERE class_id = $selected_class ORDER BY first_name";
          $students_res = $conn->query($students_sql);
        ?>
        <div class="card shadow mb-4">
          <div class="card-body">
            <form method="GET">
              <input type="hidden" name="step" value="enter_marks">
              <input type="hidden" name="class_id" value="<?php echo $selected_class; ?>">
              <div class="mb-3">
                <label for="student_id" class="form-label">Select Student</label>
                <select class="form-select" id="student_id" name="student_id" required>
                  <option value="">Choose Student...</option>
                  <?php while($st = $students_res->fetch_assoc()): ?>
                    <option value="<?php echo $st['id']; ?>">
                      <?php echo $st['first_name'].' '.$st['last_name']; ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <button type="submit" class="btn btn-primary">Next</button>
            </form>
          </div>
        </div>
      <?php elseif ($step === 'step2' && !$selected_class): ?>
        <div class="alert alert-danger">No class selected. Please go back and choose a class.</div>
      <?php endif; ?>

      <!-- Step 3: Enter Marks for All Subjects -->
      <?php if ($step === 'enter_marks' && $selected_class > 0 && $selected_student > 0): ?>
        <?php
        // fetch student name
        $st_info = $conn->query("SELECT first_name, last_name FROM students WHERE id = $selected_student")->fetch_assoc();
        $studentName = $st_info['first_name'] . ' ' . $st_info['last_name'];

        // fetch subjects for that class
        $subjects_sql = "SELECT id, name FROM subjects WHERE class_id = $selected_class ORDER BY id ASC";
        $subjects_res = $conn->query($subjects_sql);
        ?>
        <div class="card shadow mb-4">
          <div class="card-body">
            <h4>Enter Marks for <?php echo $studentName; ?></h4>
            <form action="actions/save_marks.php" method="POST">
              <input type="hidden" name="student_id" value="<?php echo $selected_student; ?>">
              <input type="hidden" name="class_id" value="<?php echo $selected_class; ?>">

              <div class="row mb-3">
                <div class="col-md-4">
                  <label for="exam_type" class="form-label">Exam Type</label>
                  <select class="form-select" id="exam_type" name="exam_type" required>
                    <option value="">Select Exam Type...</option>
                    <option value="Midterm">Midterm</option>
                    <option value="Final">Final</option>
                    <option value="Quiz">Quiz</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="exam_date" class="form-label">Exam Date</label>
                  <input type="date" class="form-control" id="exam_date" name="exam_date" required>
                </div>
              </div>

              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Subject</th>
                    <th>Marks (out of 100)</th>
                  </tr>
                </thead>
                <tbody>
                <?php while($sub = $subjects_res->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($sub['name']); ?></td>
                    <td>
                      <input type="number" class="form-control" name="marks[<?php echo $sub['id']; ?>]"
                             min="0" max="100" required>
                    </td>
                  </tr>
                <?php endwhile; ?>
                </tbody>
              </table>
              <button type="submit" class="btn btn-success">Save All Marks</button>
            </form>
          </div>
        </div>
      <?php endif; ?>
    </div> <!-- container-fluid -->
  </div> <!-- flex-grow-1 -->
</div> <!-- d-flex -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
