<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';

$student_name = "";
$results_array = array();
$error_message = "";
$registration_number_display = "";
$class_display = "";

// Accept POST or GET for `registration_number` and `class_id`
$registration_number = isset($_REQUEST['registration_number']) ? trim($_REQUEST['registration_number']) : '';
$class_id = isset($_REQUEST['class_id']) ? intval($_REQUEST['class_id']) : 0;

// If missing inputs, show error
if (empty($registration_number) || $class_id <= 0) {
    $error_message = "Please enter a registration number and select a class.";
} else {
    // 1) Find the student, also fetch block_results & block_message
    $stmt = $conn->prepare("
        SELECT id, first_name, last_name, block_results, block_message
        FROM students
        WHERE registration_number = ? 
          AND class_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("si", $registration_number, $class_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $student = $res->fetch_assoc();
        $student_id = $student['id'];
        $student_name = $student['first_name'] . " " . $student['last_name'];
        $registration_number_display = $registration_number;

        // If this student is blocked, show the block message and skip results
        if ($student['block_results'] == 1) {
            // If teacher left a custom message, use it. Otherwise show a default block message.
            $blockMsg = !empty($student['block_message']) 
                        ? $student['block_message'] 
                        : "Your results are currently blocked. Please contact administration.";
            
            $error_message = "Results Blocked: " . $blockMsg;
        } else {
            // 2) Fetch class name & section
            $stmt_class = $conn->prepare("SELECT name, section FROM classes WHERE id = ? LIMIT 1");
            $stmt_class->bind_param("i", $class_id);
            $stmt_class->execute();
            $class_res = $stmt_class->get_result();
            if ($class_res->num_rows === 1) {
                $class_info = $class_res->fetch_assoc();
                $class_display = $class_info['name'];
                if (!empty($class_info['section'])) {
                    $class_display .= " (Section " . $class_info['section'] . ")";
                }
            }

            // 3) Fetch that student's results
            $stmt_res = $conn->prepare("
                SELECT sub.name AS subject, r.marks, r.grade, r.exam_type, r.exam_date
                FROM results r
                JOIN subjects sub ON r.subject_id = sub.id
                WHERE r.student_id = ?
            ");
            $stmt_res->bind_param("i", $student_id);
            $stmt_res->execute();
            $res_data = $stmt_res->get_result();

            while ($row = $res_data->fetch_assoc()) {
                $results_array[] = [
                    'subject'    => $row['subject'],
                    'marks'      => $row['marks'],
                    'grade'      => $row['grade'],
                    'exam_type'  => $row['exam_type'],
                    'exam_date'  => $row['exam_date']
                ];
            }
        }
    } else {
        $error_message = "Student not found in this class. Please contact your teacher.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Result</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- jsPDF + AutoTable (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            width: 60%;
            margin: 40px auto;
            padding: 20px;
            border-radius: 10px;
            animation: fadeIn 1s ease-in-out;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .error {
            color: red;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            color: white;
            background: #28a745;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background: #218838;
        }
        .btn-back {
            background: #dc3545;
        }
        .btn-back:hover {
            background: #c82333;
        }
        .heading {
            margin-top: 0;
        }
        /* 
         * Show logos & signatures on the HTML page
         */
        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .result-header .logo {
            width: 70px;
            height: auto;
        }
        .school-name {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 0;
        }
        /* Teacher & Principal signatures on the page */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        .signature-box img {
            width: 150px;
            height: auto;
            margin-bottom: 5px;
        }
        .signature-box p {
            margin: 0;
            font-weight: bold;
            margin-top: 5px;
        }
        /* Simple table styling for on-page results display */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="heading">Student Result</h2>

    <!-- If there's an error, display it -->
    <?php if (!empty($error_message)) { ?>
        <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
    <?php } else if (!empty($student_name)) { ?>

        <!-- Display Logos & School Name in Header -->
        <div class="result-header">
            <img src="assets/images/logo-left.png" alt="Left Logo" class="logo">
            <h2 class="school-name">Army Goodwill School Krusan</h2>
            <img src="assets/images/logo-right.png" alt="Right Logo" class="logo">
        </div>

        <!-- Student Info -->
        <h3>Student Name: <?php echo htmlspecialchars($student_name); ?></h3>
        <h4>Registration No.: <?php echo htmlspecialchars($registration_number_display); ?></h4>
        <?php if (!empty($class_display)) { ?>
            <h4>Class: <?php echo htmlspecialchars($class_display); ?></h4>
        <?php } ?>

        <!-- If we have results, show them in an HTML table -->
        <?php if (count($results_array) > 0) { ?>
            <table>
                <tr>
                    <th>Subject</th>
                    <th>Marks</th>
                    <th>Grade</th>
                    <th>Exam Type</th>
                    <th>Exam Date</th>
                </tr>
                <?php foreach ($results_array as $r) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['subject']); ?></td>
                        <td><?php echo htmlspecialchars($r['marks']); ?></td>
                        <td><?php echo htmlspecialchars($r['grade']); ?></td>
                        <td><?php echo htmlspecialchars($r['exam_type']); ?></td>
                        <td><?php echo htmlspecialchars($r['exam_date']); ?></td>
                    </tr>
                <?php } ?>
            </table>

            <!-- Display Signatures in HTML -->
            <div class="signatures">
                <div class="signature-box">
                    <img src="assets/images/principal-sign.png" alt="Teacher Signature">
                    <p>I/C Teacher</p>
                </div>
                <div class="signature-box">
                    <img src="assets/images/principal-sign.png" alt="Principal Signature">
                    <p>School Principal</p>
                </div>
            </div>

            <!-- Button to Generate Crisp PDF -->
            <button class="btn" onclick="generatePDF()">Generate PDF</button>
        <?php } else { ?>
            <p>No results found for this student.</p>
        <?php } ?>

    <?php } ?>

    <a href="index.php" class="btn btn-back">Go Back</a>
</div>

<!-- Pass the data to JavaScript for PDF generation -->
<script>
  var studentName    = <?php echo json_encode($student_name); ?>;
  var registrationNo = <?php echo json_encode($registration_number_display); ?>;
  var className      = <?php echo json_encode($class_display); ?>;
  var resultsData    = <?php echo json_encode($results_array); ?>;
</script>

<script>
  async function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');

    // We'll create <img> objects for logos & signatures
    let leftLogo = new Image();
    leftLogo.src = 'assets/images/logo-left.png';

    let rightLogo = new Image();
    rightLogo.src = 'assets/images/logo-right.png';

    let teacherSign = new Image();
    teacherSign.src = 'assets/images/principal-sign.png'; 
    // If you have a separate teacher sign, replace path

    let principalSign = new Image();
    principalSign.src = 'assets/images/principal-sign.png';

    // Wait for images to load
    leftLogo.onload = () => {
      rightLogo.onload = () => {
        teacherSign.onload = () => {
          principalSign.onload = () => {

            // 1) Place top logos
            doc.addImage(leftLogo, 'PNG', 15, 10, 20, 20);  
            doc.addImage(rightLogo, 'PNG', 170, 10, 20, 20);

            // 2) Centered School Name
            doc.setFontSize(16);
            doc.text('Army Goodwill School Krusan', 105, 20, { align: 'center' });

            // 3) Student Details
            doc.setFontSize(12);
            let yPos = 40;
            doc.text('Student Name: ' + (studentName || ''), 14, yPos); 
            yPos += 8;
            doc.text('Registration No.: ' + (registrationNo || ''), 14, yPos); 
            yPos += 8;
            if (className) {
              doc.text('Class: ' + className, 14, yPos);
              yPos += 8;
            }

            // If no results
            if (!resultsData || resultsData.length === 0) {
              doc.text('No results found.', 14, yPos + 10);
              doc.save('student_result.pdf');
              return;
            }

            // 4) Use AutoTable for Crisp Table
            const columns = [
              { header: 'Subject', dataKey: 'subject' },
              { header: 'Marks', dataKey: 'marks' },
              { header: 'Grade', dataKey: 'grade' },
              { header: 'Exam Type', dataKey: 'exam_type' },
              { header: 'Exam Date', dataKey: 'exam_date' }
            ];
            const bodyData = resultsData.map(r => [
              r.subject, r.marks, r.grade, r.exam_type, r.exam_date
            ]);

            doc.autoTable({
              head: [ columns.map(col => col.header) ],
              body: bodyData,
              startY: yPos + 5,
              margin: { left: 14, right: 14 },
              theme: 'grid',         // draws solid lines
              styles: {
                fontSize: 11,
                cellPadding: 3,
                lineWidth: 0.2,       // thickness of border
                lineColor: [0, 0, 0]  // black border
              }
            });

            // 5) Signatures at bottom
            let finalY = doc.lastAutoTable.finalY + 30;

            // Teacher signature
            doc.addImage(teacherSign, 'PNG', 35, finalY - 15, 30, 15);
            doc.text('Class Teacher', 40, finalY);

            // Principal signature
            doc.addImage(principalSign, 'PNG', 125, finalY - 15, 30, 15);
            doc.text('School Principal', 130, finalY);

            // 6) Save
            doc.save('student_result.pdf');
          }
        }
      }
    }
  }
</script>
</body>
</html>
