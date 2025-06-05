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

// If form submitted, update the student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_number = $_POST['registration_number'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $class_id = intval($_POST['class_id']);

    $upd_sql = "UPDATE students 
                SET registration_number=?, first_name=?, last_name=?, email=?, phone=?, class_id=?
                WHERE id=? LIMIT 1";
    $stmt_upd = $conn->prepare($upd_sql);
    $stmt_upd->bind_param("ssssssi", 
        $registration_number, $first_name, $last_name, $email, $phone, $class_id, $student_id
    );
    $stmt_upd->execute();

    // Redirect back
    header("Location: ../students.php");
    exit();
}

// Otherwise, fetch existing data to prefill form
$sql = "SELECT * FROM students WHERE id=? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows !== 1) {
    die("Student not found.");
}
$student = $res->fetch_assoc();

// Fetch classes for dropdown
$classes = $conn->query("SELECT id, name FROM classes");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
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
      /* optional styling for the form card */
      .edit-card {
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
        <!-- highlight students -->
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
        <h1 class="h3 mb-4 text-gray-800">Edit Student</h1>
        
        <div class="card p-3 edit-card shadow">
          <form action="" method="POST">
            <div class="mb-3">
              <label for="registration_number" class="form-label">Registration Number</label>
              <input type="text" name="registration_number" id="registration_number"
                     class="form-control"
                     value="<?php echo htmlspecialchars($student['registration_number']); ?>" required>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name" id="first_name"
                       class="form-control"
                       value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name" id="last_name"
                       class="form-control"
                       value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
              </div>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" name="email" id="email"
                     class="form-control"
                     value="<?php echo htmlspecialchars($student['email']); ?>">
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Phone</label>
              <input type="tel" name="phone" id="phone"
                     class="form-control"
                     value="<?php echo htmlspecialchars($student['phone']); ?>">
            </div>
            <div class="mb-3">
              <label for="class_id" class="form-label">Class</label>
              <select name="class_id" id="class_id" class="form-select" required>
                <?php while($class = $classes->fetch_assoc()): ?>
                  <option value="<?php echo $class['id']; ?>"
                    <?php if ($class['id'] == $student['class_id']) echo 'selected'; ?>>
                    <?php echo $class['name']; ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update Student
            </button>
            <a href="../students.php" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> Cancel
            </a>
          </form>
        </div>
      </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
