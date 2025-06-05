<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>School Management System - Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {
      background: linear-gradient(135deg, #74ebd5, #9face6);
      min-height: 100vh;
      margin: 0;
      padding: 0;
    }
    .card {
      border-radius: 15px;
      overflow: hidden;
      transition: transform 0.3s;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .btn-primary {
      transition: background 0.3s;
    }
    .btn-primary:hover {
      background: #0056b3;
    }
    .min-vh-100 {
      min-height: 100vh;
    }
    /* Slightly bigger headings for mobile aesthetic */
    h2.text-primary {
      font-size: 1.8rem;
      font-weight: 600;
      margin: 0; /* So logos align nicely */
    }
    h5.text-center {
      font-size: 1.2rem;
      font-weight: 500;
    }
    /* Flex container for the logos + heading */
    .logo-header {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 15px; /* space between logos and heading */
      margin-bottom: 1rem; /* spacing below the row */
    }
    /* Eye Icon styling */
    .input-group-text {
      background-color: #f8f9fa;
      border-left: 0; /* no left border so it blends with input */
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .input-group-text:hover {
      background-color: #e2e6ea;
    }
    .input-group .form-control {
      border-right: 0; /* remove right border to blend with the input-group-text */
    }
  </style>
</head>
<body>
<div class="container">
  <div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-12 col-sm-10 col-md-7 col-lg-5">
      <div class="card shadow-lg">
        <div class="card-body p-4">

          <!-- Logos on Both Sides of Heading -->
          <div class="logo-header">
            <!-- Left Logo -->
            <img src="assets/images/logo-left.png" alt="Left Logo" style="width: 60px; height: auto;">
            <!-- Heading -->
            <h2 class="text-primary">
              <i class="fas fa-graduation-cap"></i> SMS
            </h2>
            <!-- Right Logo -->
            <img src="assets/images/logo-right.png" alt="Right Logo" style="width: 60px; height: auto;">
          </div>

          <h5 class="text-center mb-4">Login</h5>

          <!-- Login Form -->
          <form action="auth/login.php" method="POST">
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <!-- Password + Stylish Eye Icon -->
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <div class="input-group">
                <input 
                  type="password" 
                  class="form-control" 
                  id="password" 
                  name="password" 
                  required
                >
                <span class="input-group-text" id="togglePassword">
                  <i class="fa fa-eye"></i>
                </span>
              </div>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Login</button>
            </div>
          </form>

          <hr>

          <!-- Check Results Form -->
          <h5 class="text-center mt-3">Check Your Results</h5>
          <form action="student_result.php" method="GET">
            <div class="mb-3">
              <label for="registration_number" class="form-label">Enter Registration Number</label>
              <input type="text" class="form-control" id="registration_number" name="registration_number" required>
            </div>
            <div class="mb-3">
              <label for="class_id" class="form-label">Select Class</label>
              <select class="form-control" id="class_id" name="class_id" required>
                <option value="">Select Class</option>
                <?php
                require_once 'config/database.php';
                // Fetch class ID, name, section for a clearer dropdown
                $query = "SELECT id, name, section FROM classes ORDER BY name";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                  $classLabel = $row['name'] . (!empty($row['section']) ? ' (Section ' . $row['section'] . ')' : '');
                  echo "<option value='" . $row['id'] . "'>" . $classLabel . "</option>";
                }
                ?>
              </select>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-success">
                <i class="fas fa-poll"></i> Get Result
              </button>
            </div>
          </form>

        </div> <!-- card-body -->
      </div> <!-- card -->
    </div> <!-- col-12 col-sm-10 col-md-7 col-lg-5 -->
  </div> <!-- row -->
</div> <!-- container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Toggle password visibility with a stylish eye icon
  const togglePassword = document.getElementById('togglePassword');
  const passwordField = document.getElementById('password');

  togglePassword.addEventListener('click', () => {
    const isPassword = (passwordField.type === 'password');
    passwordField.type = isPassword ? 'text' : 'password';

    // Swap icon
    togglePassword.innerHTML = isPassword 
      ? '<i class="fa fa-eye-slash"></i>'
      : '<i class="fa fa-eye"></i>';
  });
</script>
</body>
</html>
