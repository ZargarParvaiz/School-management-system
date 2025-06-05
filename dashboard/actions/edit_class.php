<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../../config/database.php';

$current_page = 'classes.php';

if (!isset($_GET['id'])) {
    die("No class ID provided.");
}
$class_id = intval($_GET['id']);

// If form submitted, update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $section = $_POST['section'];
    $teacher_id = intval($_POST['teacher_id']);

    $upd_sql = "UPDATE classes 
                SET name=?, section=?, teacher_id=?
                WHERE id=? LIMIT 1";
    $stmt_upd = $conn->prepare($upd_sql);
    $stmt_upd->bind_param("ssii", $name, $section, $teacher_id, $class_id);
    $stmt_upd->execute();

    header("Location: ../classes.php");
    exit();
}

// Fetch existing data
$sql = "SELECT * FROM classes WHERE id=? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows !== 1) {
    die("Class not found.");
}
$classData = $res->fetch_assoc();

// Fetch teachers
$teachers = $conn->query("SELECT id, username FROM users WHERE role='teacher'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Class</title>
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
        <a href="../index.php" class="nav-link">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
        <a href="../students.php" class="nav-link">
          <i class="fas fa-fw fa-user-graduate"></i>
          <span>Students</span>
        </a>
        <a href="../teachers.php" class="nav-link">
          <i class="fas fa-fw fa-chalkboard-teacher"></i>
          <span>Teachers</span>
        </a>
        <a href="../classes.php"
           class="nav-link <?php echo ($current_page == 'classes.php') ? 'active' : ''; ?>">
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
        <h1 class="h3 mb-4 text-gray-800">Edit Class</h1>
        
        <div class="card p-3 edit-card shadow">
          <form action="" method="POST">
            <div class="mb-3">
              <label for="name" class="form-label">Class Name</label>
              <input type="text" name="name" id="name"
                     class="form-control"
                     value="<?php echo htmlspecialchars($classData['name']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="section" class="form-label">Section</label>
              <input type="text" name="section" id="section"
                     class="form-control"
                     value="<?php echo htmlspecialchars($classData['section']); ?>">
            </div>
            <div class="mb-3">
              <label for="teacher_id" class="form-label">Teacher</label>
              <select name="teacher_id" id="teacher_id" class="form-select" required>
                <?php while($t = $teachers->fetch_assoc()): ?>
                  <option value="<?php echo $t['id']; ?>"
                    <?php if ($t['id'] == $classData['teacher_id']) echo 'selected'; ?>>
                    <?php echo $t['username']; ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update Class
            </button>
            <a href="../classes.php" class="btn btn-secondary">
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
