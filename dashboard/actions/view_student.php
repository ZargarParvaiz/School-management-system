<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../../config/database.php';

// Force highlight "students" in the sidebar
$current_page = 'students.php';

if (!isset($_GET['id'])) {
    die("No student ID provided.");
}

$student_id = intval($_GET['id']);

// Fetch student info
$sql = "SELECT s.*, c.name AS class_name 
        FROM students s
        LEFT JOIN classes c ON s.class_id = c.id
        WHERE s.id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Student not found.");
}

$student = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">

    <style>
      .school-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0;
        color: #555;
      }
      .topbar-container {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
      }
      @media (min-width: 768px) {
        .topbar-container {
          justify-content: space-between;
        }
      }
      .topbar-logo {
        height: 45px; 
        margin-top: 20px;
      }
      /* Optional card styling for the view details */
      .view-card {
        max-width: 600px;
        margin: 0 auto;
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
        <!-- We highlight 'students.php' to match the theme -->
        <a href="../index.php" 
           class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
        <a href="../students.php" 
           class="nav-link <?php echo ($current_page == 'students.php') ? 'active' : ''; ?>">
          <i class="fas fa-fw fa-user-graduate"></i>
          <span>Students</span>
        </a>
        <a href="../teachers.php" class="nav-link">
          <i class="fas fa-fw fa-chalkboard-teacher"></i>
          <span>Teachers</span>
        </a>
        <a href="../classes.php" class="nav-link">
          <i class="fas fa-fw fa-chalkboard"></i>
          <span>Classes</span>
        </a>
        <a href="../attendance.php" class="nav-link">
          <i class="fas fa-fw fa-calendar-check"></i>
          <span>Attendance</span>
        </a>
        <a href="../results.php" class="nav-link">
          <i class="fas fa-fw fa-chart-bar"></i>
          <span>Results</span>
        </a>
        <a href="../subjects.php" class="nav-link">
          <i class="fas fa-fw fa-book"></i>
          <span>Subjects</span>
        </a>
        <a href="../payment.php" class="nav-link">
          <i class="fas fa-fw fa-money-bill-wave"></i>
          <span>Payments</span>
        </a>
        <a href="../../auth/logout.php" class="nav-link">
          <i class="fas fa-fw fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1">
      <!-- Topbar -->
      <nav class="navbar navbar-expand bg-white shadow mb-4" style="min-height: 60px;">
        <div class="container-fluid topbar-container">
          <h2 class="school-title mb-0">ARMY GOODWILL SCHOOL KRUSAN</h2>
          <img src="../../assets/images/logo-left.png" alt="School Logo" class="topbar-logo">
        </div>
      </nav>

      <div class="container-fluid px-4">
        <h1 class="h3 mb-4 text-gray-800">View Student</h1>
        
        <div class="card p-3 view-card shadow">
          <p><strong>Registration Number:</strong> <?php echo htmlspecialchars($student['registration_number']); ?></p>
          <p><strong>Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
          <p><strong>Class:</strong> <?php echo htmlspecialchars($student['class_name']); ?></p>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
          <p><strong>Phone:</strong> <?php echo htmlspecialchars($student['phone']); ?></p>
          <a href="../students.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
          </a>
        </div>
      </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
