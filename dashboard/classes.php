<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../config/database.php';

// Force highlight classes in sidebar
$current_page = 'classes.php';

// Optionally display success message if a class was deleted
$deletedMsg = "";
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $deletedMsg = "Class deleted successfully!";
}

// Fetch classes
$sql = "SELECT c.*, u.username as teacher_name, 
        (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id) as student_count 
        FROM classes c 
        LEFT JOIN users u ON c.teacher_id = u.id 
        ORDER BY c.name";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Classes - Student Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
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
      /* The single logo on the right */
      .topbar-logo {
        height: 45px; 
        margin-top: 20px;
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
            <a href="classes.php" 
               class="nav-link <?php echo ($current_page == 'classes.php') ? 'active' : ''; ?>">
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
        <!-- Topbar -->
        <nav class="navbar navbar-expand bg-white shadow mb-4" style="min-height: 60px;">
            <div class="container-fluid topbar-container">
                <h2 class="school-title mb-0">ARMY GOODWILL SCHOOL KRUSAN</h2>
                <img src="../assets/images/logo-left.png" alt="School Logo" class="topbar-logo">
            </div>
        </nav>

        <div class="container-fluid px-4">
            <?php if (!empty($deletedMsg)): ?>
              <div class="alert alert-success"><?php echo $deletedMsg; ?></div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Classes Management</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                    <i class="fas fa-plus"></i> Add New Class
                </button>
            </div>

            <!-- Classes Table -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="classesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Class Name</th>
                                    <th>Section</th>
                                    <th>Teacher</th>
                                    <th>Total Students</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['section']; ?></td>
                                    <td><?php echo $row['teacher_name']; ?></td>
                                    <td><?php echo $row['student_count']; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewClass(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary" onclick="editClass(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteClass(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- We'll reference a separate add_class.php in actions/ folder -->
            <form id="addClassForm" action="actions/add_class.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Class Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="section" class="form-label">Section</label>
                        <input type="text" class="form-control" id="section" name="section">
                    </div>
                    <div class="mb-3">
                        <label for="teacher_id" class="form-label">Teacher</label>
                        <select class="form-select" id="teacher_id" name="teacher_id" required>
                            <?php
                            $teachers = $conn->query("SELECT id, username FROM users WHERE role = 'teacher'");
                            while($teacher = $teachers->fetch_assoc()):
                            ?>
                            <option value="<?php echo $teacher['id']; ?>"><?php echo $teacher['username']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#classesTable').DataTable();
    });

    function viewClass(id) {
        // Go to actions/view_class.php
        window.location.href = 'actions/view_class.php?id=' + id;
    }

    function editClass(id) {
        // Go to actions/edit_class.php
        window.location.href = 'actions/edit_class.php?id=' + id;
    }

    function deleteClass(id) {
        if(confirm('Are you sure you want to delete this class?')) {
            // Go to actions/delete_class.php
            window.location.href = 'actions/delete_class.php?id=' + id;
        }
    }
</script>
</body>
</html>
