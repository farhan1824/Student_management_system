<?php
session_start();
if (!isset($_SESSION['student_roll'])) {
  header("Location: student_login.php");
  exit();
}

require_once '../db/db.php';

$studentRoll = $_SESSION['student_roll'];
$studentName = 'Student';
$profilePic = 'default.jpg';
$studentId = 0;
$today = date('Y-m-d');
// Fetch student info
$sql = "SELECT id, name, profile_pic FROM students WHERE roll = '$studentRoll'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $studentId = $row['id'];
  $studentName = $row['name'];
  $profilePic = $row['profile_pic'] ?? 'default.jpg';
}

// Total classes conducted across all subjects
$allClassSql = "
    SELECT COUNT(*) AS total_classes
    FROM teach_attendance ta
    WHERE ta.subject_id IN (
        SELECT subject_id FROM student_subjects WHERE student_id = $studentId
    )
";
$classRes = $conn->query($allClassSql);
$totalTeacherClasses = $classRes->fetch_assoc()['total_classes'] ?? 0;

// Total student attendance
$presentSql = "SELECT COUNT(*) AS attended FROM student_attendance WHERE student_id = $studentId";
$presentRes = $conn->query($presentSql);
$totalAttended = $presentRes->fetch_assoc()['attended'] ?? 0;

$presentPercent = ($totalTeacherClasses > 0) ? round(($totalAttended / $totalTeacherClasses) * 100) : 0;

// Subject-wise attendance
$subjectSql = "
    SELECT 
        su.id AS subject_id,
        su.name AS subject_name,
        (
            SELECT COUNT(*) 
            FROM teach_attendance ta 
            WHERE ta.subject_id = su.id
        ) AS total_classes,
        (
            SELECT COUNT(*) 
            FROM student_attendance sa 
            WHERE sa.subject_id = su.id AND sa.student_id = $studentId
        ) AS attended_classes
    FROM student_subjects ss
    JOIN subjects su ON su.id = ss.subject_id
    WHERE ss.student_id = $studentId
";
$subjects = $conn->query($subjectSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="../styles.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f4f8;
      margin: 0;
      padding: 20px;
    }

    /* Card Section */
    .card {
      max-width: 850px;
      margin: auto;
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      padding: 30px;
    }

    .profile {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 20px;
    }

    .profile img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #007bff;
    }

    .profile h2 {
      margin: 0;
      font-size: 24px;
      color: #333;
    }

    .profile p {
      margin: 4px 0 0;
      color: #555;
    }

    .progress-circle {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      font-weight: bold;
      color: #333;
      margin: 20px auto;
    }



    .subjects {
      margin-top: 20px;
    }

    .subjects h3 {
      text-align: center;
      margin-bottom: 16px;
      font-size: 20px;
      color: #333;
    }

    .subject-row {
      background: #f9f9f9;
      margin-bottom: 10px;
      padding: 10px 15px;
      border-left: 5px solid #007bff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 8px;
    }

    .subject-row .bar {
      flex: 1;
      margin: 0 15px;
      background: #e0e0e0;
      height: 10px;
      border-radius: 5px;
      overflow: hidden;
    }

    .subject-row .bar .fill {
      height: 100%;
      background: #007bff;
      border-radius: 5px 0 0 5px;
    }

    .request-btn {
      display: block;
      margin: 25px auto 0;
      padding: 12px 28px;
      background: linear-gradient(to right, #ff416c, #ff4b2b);
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }

    .request-btn:hover {
      background: linear-gradient(to right, #e03e57, #e1441d);
    }

    .logout-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      text-decoration: none;
      background: #dc3545;
      color: white;
      padding: 8px 16px;
      border-radius: 6px;
    }

    /* Modal Overlay */
    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
    }

    /* Modal Content */
    .modal-content {
      background: #fff;
      margin: 6% auto;
      padding: 30px 40px;
      border-radius: 12px;
      max-width: 450px;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
      position: relative;
      color: #222;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .close-btn {
      color: #888;
      position: absolute;
      top: 18px;
      right: 20px;
      font-size: 28px;
      font-weight: 700;
      cursor: pointer;
      transition: color 0.2s ease;
    }

    .close-btn:hover {
      color: #ff4b2b;
    }

    .modal-content h3 {
      margin-top: 0;
      margin-bottom: 20px;
      font-size: 24px;
      color: #007bff;
      text-align: center;
    }

    .modal-content label {
      display: block;
      margin: 12px 0 6px;
      font-weight: 600;
      color: #333;
    }

    .modal-content input[type="date"],
    .modal-content select,
    .modal-content textarea {
      width: 100%;
      padding: 10px 14px;
      border-radius: 6px;
      border: 1.8px solid #ccc;
      font-size: 16px;
      color: #444;
      box-sizing: border-box;
      transition: border-color 0.3s ease;
    }

    .modal-content input[type="date"]:focus,
    .modal-content select:focus,
    .modal-content textarea:focus {
      border-color: #007bff;
      outline: none;
      box-shadow: 0 0 6px #007bffaa;
    }

    .modal-content textarea {
      resize: vertical;
      min-height: 80px;
    }

    .modal-content button {
      margin-top: 24px;
      width: 100%;
      padding: 12px;
      border: none;
      background: linear-gradient(90deg, #ff416c, #ff4b2b);
      color: white;
      font-weight: 700;
      font-size: 18px;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .modal-content button:hover {
      background: linear-gradient(90deg, #e03e57, #e1441d);
    }

    #dateField {
      margin-top: 6px;
    }

    /* SweetAlert2 fix for stacking order */
    .swal2-container {
      z-index: 10010 !important;
    }

    @media (max-width: 500px) {
      .modal-content {
        margin: 15% 15px;
        padding: 20px;
        width: auto;
      }
    }
  </style>

</head>

<body>
  <!-- Modal Popup for Recheck -->


  <!-- Popup Modal -->
  <div id="recheckModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeRecheckPopup()">&times;</span>
      <h3>Request Recheck</h3>

      <label for="subjectSelect">Subject:</label>
      <select id="subjectSelect"></select>

      <label for="reasonType">Reason Type:</label>
      <select id="reasonType" onchange="toggleDateField()">
        <option value="attendance">Attendance</option>
        <option value="result">Result</option>
      </select>

      <div id="dateField">
        <label for="recheckDate">Date:</label>
        <input
          type="date"
          id="recheckDate"
          name="date"
          max="<?php echo $today; ?>" />

        <!-- <input type="date" id="recheckDate" /> -->
      </div>

      <label for="recheckReason">Reason:</label>
      <textarea id="recheckReason" rows="4" placeholder="Enter your reason..."></textarea>

      <button onclick="submitRecheck()">Submit Request</button>
    </div>
  </div>

  <div class="card glass-card">
    <div class="profile-section">
      <img src="../<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" />
      <div class="profile-info">
        <h2>Welcome, <?php echo htmlspecialchars($studentName); ?></h2>
        <p><strong>Roll:</strong> <?php echo htmlspecialchars($studentRoll); ?></p>
      </div>
    </div>

    <div class="progress-circle" style="background: conic-gradient(#28a745 <?php echo $presentPercent; ?>%, #eee 0%)">
      <?php echo $presentPercent; ?>%
    </div>


    <div class="subjects">
      <h3>Your Subjects & Attendance</h3>
      <?php while ($sub = $subjects->fetch_assoc()):
        $present = (int)$sub['attended_classes'];
        $total = (int)$sub['total_classes'];
        $percentage = ($total > 0) ? round(($present / $total) * 100) : 0;
      ?>
        <div class="subject-row">
          <div class="subject-name"><?php echo htmlspecialchars($sub['subject_name']); ?></div>
          <div class="bar">
            <div class="fill" style="width: <?php echo $percentage; ?>%"></div>
          </div>
          <div class="percent"><?php echo $percentage; ?>%</div>
        </div>
      <?php endwhile; ?>
    </div>

    <button class="request-btn" onclick="requestRecheck(<?php echo $studentId; ?>)">
      📩 Request Recheck</button>
  </div>


  <a href="../logout.php" class="logout-btn">Logout</a>



</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  let studentId = <?php echo json_encode($studentId); ?>;

  function requestRecheck(studentId) {
    // Show modal
    document.getElementById('recheckModal').style.display = 'block';

    // Clear previous options
    const select = document.getElementById('subjectSelect');
    select.innerHTML = '<option value="">Loading...</option>';

    fetch('../QueryModel/ajax_call.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'get_student_subjects'
        })
      })
      .then(res => res.json())
      .then(subjects => {
        select.innerHTML = '';
        if (subjects.length === 0) {
          const opt = document.createElement('option');
          opt.textContent = "No subjects found";
          opt.disabled = true;
          select.appendChild(opt);
        } else {
          subjects.forEach(sub => {
            const opt = document.createElement('option');
            opt.value = sub.name;
            opt.textContent = sub.name;
            select.appendChild(opt);
          });
        }
      })
      .catch(err => {
        console.error("Failed to load subjects:", err);
        select.innerHTML = '<option value="">Error loading subjects</option>';
      });
  }

  function closeRecheckPopup() {
    document.getElementById('recheckModal').style.display = 'none';
  }

  function toggleDateField() {
    const reasonType = document.getElementById('reasonType').value;
    document.getElementById('dateField').style.display = reasonType === 'attendance' ? 'block' : 'none';
  }

  function submitRecheck() {
    const subjectName = document.getElementById('subjectSelect').value;
    const reasonType = document.getElementById('reasonType').value;
    const reason = document.getElementById('recheckReason').value.trim();
    const date = document.getElementById('recheckDate').value;

    if (!subjectName || !reasonType || !reason || (reasonType === 'attendance' && !date)) {
      alert('Please fill all required fields.');
      return;
    }

    const formData = new URLSearchParams({
      action: 'request_recheck',
      student_id: studentId,
      subject_name: subjectName,
      reason_type: reasonType,
      reason: reason
    });

    if (reasonType === 'attendance') {
      formData.append('date', date);
    }

    fetch('../QueryModel/ajax_call.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData
      })
      .then(res => res.text())
      .then(data => {
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: data,
          confirmButtonColor: '#28a745',
          timer: 2500,
          timerProgressBar: true,
          willClose: () => {
            closeRecheckPopup(); // close your custom popup after Swal closes
          }
        });
      })
      .catch(err => {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Error occurred. Please try again.',
          confirmButtonColor: '#dc3545',
        });
        console.error(err);
      });

  }

  // function requestRecheck(studentId) {
  //   const subjectName = prompt("Enter Subject Name for Recheck:");
  //   const reasonType = prompt("Enter reason type: attendance or result").toLowerCase();
  //   if (!["attendance", "result"].includes(reasonType)) {
  //     alert("Invalid reason type.");
  //     return;
  //   }

  //   let date = '';
  //   if (reasonType === 'attendance') {
  //     date = prompt("Enter Date (YYYY-MM-DD):");
  //     if (!date) {
  //       alert("Date is required for attendance correction.");
  //       return;
  //     }
  //   }

  //   const reason = prompt("Enter your reason for recheck:");
  //   if (!subjectName || !reason) return;

  //   const formData = new URLSearchParams({
  //     action: 'request_recheck',
  //     student_id: studentId,
  //     subject_name: subjectName,
  //     reason_type: reasonType,
  //     reason: reason
  //   });

  //   if (date) {
  //     formData.append('date', date);
  //   }

  //   fetch('../QueryModel/ajax_call.php', {
  //     method: 'POST',
  //     headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  //     body: formData
  //   })
  //     .then(res => res.text())
  //     .then(alert)
  //     .catch(err => {
  //       alert("Error occurred.");
  //       console.error(err);
  //     });
  // }
</script>