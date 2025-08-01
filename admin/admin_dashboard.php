<?php
require_once '../db/db.php'; // your DB connection
session_start();
if (!isset($_SESSION['admin_username'])) {
  header("Location: admin_login.php");
  exit();
}

$teachsql = "
SELECT 
  t.Name AS teacher_name,
  c.id AS request_id,
  c.reason,
  c.reason_type,
  c.status,
  c.requested_at
FROM correction_requests c
JOIN teach_details t ON c.submitted_by_id = t.teach_num
WHERE c.requested_by = 'teacher' AND c.status = 'pending'
ORDER BY t.Name, c.requested_at

";

$result = $conn->query($teachsql);
$teacherRequests = [];

while ($row = $result->fetch_assoc()) {
  $teacher = $row['teacher_name'];
  if (!isset($teacherRequests[$teacher])) {
    $teacherRequests[$teacher] = [];
  }
  $teacherRequests[$teacher][] = [
    'id' => $row['request_id'],
    'reason' => $row['reason'],
    'type' => $row['reason_type'],
    'status' => $row['status'],
    'date' => $row['requested_at']
  ];
}

$studentsql = "
SELECT 
  s.id AS student_id,
  s.name,
  s.roll,
  c.id AS request_id,
  c.reason,
  c.reason_type,
  c.status,
  c.requested_at
FROM correction_requests c
JOIN students s ON c.submitted_by_id = s.roll
WHERE c.requested_by = 'student' AND c.status = 'pending' AND reason_type='result'
ORDER BY s.name, c.requested_at
";


$result = $conn->query($studentsql);
$studentRequests = [];
// var_dump($result->num_rows);
// die();
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $studentRequests[] = [
      'id' => $row['request_id'],
      'name' => $row['name'],
      'roll' => $row['roll'],
      'reason' => $row['reason'],
      'type' => $row['reason_type'],
      'status' => $row['status'],
      'date' => $row['requested_at']
    ];
  }
}

// Fetch all teachers
$teachers = [];
$teacherResult = $conn->query("SELECT teach_num, Name FROM teach_details ORDER BY Name ASC");
while ($row = $teacherResult->fetch_assoc()) {
  $teachers[] = $row;
}

// Fetch all subjects
$subjects = [];
$subjectResult = $conn->query("SELECT id, name  FROM subjects ORDER BY name  ASC");
while ($row = $subjectResult->fetch_assoc()) {
  $subjects[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #D2D3BE, #000000);
      color: #000;
      margin: 0;
      padding: 40px;
    }

    h1 {
      text-align: center;
      color: #D2D3BE;
      margin-bottom: 40px;
    }

    .dashboard-container {
      display: flex;
      gap: 100px;
      justify-content: center;
      flex-wrap: wrap;
      height: device-height;
    }

    .card {
      background: #D2D3BE;
      border-radius: 20px;
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
      width: 300px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      max-height: 600px;
      overflow-y: auto;
    }

    .card h2 {
      margin-bottom: 20px;
      font-weight: 600;
      letter-spacing: 1px;
      text-align: center;
    }

    /* Drawer styles */
    .drawer {
      margin-bottom: 15px;
      border: 1px solid #000;
      border-radius: 12px;
    }

    .drawer-header {
      background: #000;
      color: #D2D3BE;
      padding: 10px 15px;
      cursor: pointer;
      user-select: none;
      font-weight: 600;
      border-radius: 12px 12px 0 0;
    }

    .drawer-content {
      max-height: 0;
      overflow: hidden;
      background: #f0f0ea;
      color: #000;
      transition: max-height 0.3s ease;
      border-radius: 0 0 12px 12px;
    }

    .drawer-content.open {
      max-height: 400px;
      /* Adjust if needed */
      padding: 10px 15px;
    }

    .complaint {
      background: #d9d9c6;
      padding: 8px 12px;
      margin-bottom: 8px;
      border-radius: 8px;
    }

    .btn-group {
      display: flex;
      gap: 10px;
      margin-top: 8px;
    }

    button {
      flex: 1;
      padding: 6px 0;
      border-radius: 20px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .btn-approve {
      background-color: #4CAF50;
      color: white;
    }

    .btn-approve:hover {
      background-color: #45a049;
    }

    .btn-disapprove {
      background-color: #f44336;
      color: white;
    }

    .btn-disapprove:hover {
      background-color: #da190b;
    }

    /* Mini cards inside second card for student results requests */
    .mini-card {
      background: #d9d9c6;
      padding: 12px;
      margin-bottom: 12px;
      border-radius: 15px;
    }

    .mini-card-header {
      font-weight: 600;
      margin-bottom: 6px;
    }

    .mini-card-buttons {
      display: flex;
      gap: 10px;
    }

    /* Form styles for assigning subjects */
    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    label {
      font-weight: 600;
    }

    select,
    input[type="submit"] {
      padding: 10px 15px;
      border-radius: 15px;
      border: none;
      font-size: 16px;
    }

    input[type="submit"] {
      background-color: #000;
      color: #D2D3BE;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
      background-color: #222;
    }

    .sweet-alert-message {
      background-color: #dff0d8;
      /* soft green */
      color: #3c763d;
      /* dark green text */
      border: 1px solid #d6e9c6;
      padding: 15px 20px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 16px;
      display: flex;
      align-items: center;
      gap: 10px;
      max-width: 400px;
      margin: 20px auto;
      box-shadow: 0 2px 5px rgba(60, 118, 61, 0.3);
      user-select: none;
    }

    .sweet-alert-message .icon {
      font-size: 24px;
      line-height: 1;
    }

    .multi-select-container {
      position: relative;
      margin-top: 10px;
    }

    .multi-select-btn {
      width: 100%;
      padding: 10px 15px;
      font-size: 16px;
      background: white;
      color: #000000ff;
      border-radius: 15px;
      border: 2px solid #D2D3BE;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .arrow {
      border: solid #D2D3BE;
      border-width: 0 2px 2px 0;
      padding: 4px;
      transform: rotate(45deg);
      transition: 0.3s ease;
    }

    .arrow.open {
      transform: rotate(-135deg);
    }

    .dropdown {
      display: none;
      position: absolute;
      top: 110%;
      width: 100%;
      background: #f0f0ea;
      border: 2px solid #000;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
      max-height: 220px;
      overflow-y: auto;
      z-index: 10;
    }

    .dropdown.open {
      display: block;
    }

    .dropdown label {
      display: flex;
      align-items: center;
      padding: 10px 12px;
      font-size: 15px;
      cursor: pointer;
      color: #000;
    }

    .dropdown label:hover {
      background: #d9d9c6;
    }

    .dropdown input[type="checkbox"] {
      margin-right: 10px;
    }
  </style>
</head>

<body>

  <h1>Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?></h1>

  <div class="dashboard-container">
    <!-- 1st Card: Teacher Correction Requests -->

    <div class="card" id="teacher-requests-card">
      <h2>Teacher Correction Requests</h2>

      <?php if (empty($teacherRequests)): ?>
        <div class="sweet-alert-message">
          <span class="icon">✔️</span>
          <span>All teacher correction requests have been reviewed successfully!</span>
        </div>
      <?php else: ?>
        <?php foreach ($teacherRequests as $teacherName => $requests): ?>
          <div class="drawer">
            <div class="drawer-header" onclick="toggleDrawer(this)">
              Teacher: <?= htmlspecialchars($teacherName) ?>
            </div>
            <div class="drawer-content">
              <?php foreach ($requests as $req): ?>
                <div class="complaint">
                  <?= htmlspecialchars($req['reason']) ?> <br>
                  <small>Type: <?= htmlspecialchars($req['type']) ?> | Date: <?= $req['date'] ?></small>
                  <div class="btn-group">
                    <button class="btn-approve" onclick="handleActionTeacher('approved', <?= $req['id'] ?>, this)">Approve</button>
                    <button class="btn-disapprove" onclick="handleActionTeacher('rejected', <?= $req['id'] ?>, this)">Disapprove</button>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- 2nd Card: Student Result Requests -->
    <div class="card" id="student-requests-card">
      <h2>Student Correction Requests</h2>

      <!-- <div id="student-reviewed-message" style="display:none; text-align:center; margin-top:20px; font-weight:700; color:#4CAF50; font-size:1.2rem;">
    All student correction requests have been reviewed.
  </div> -->

      <?php if (empty($studentRequests)): ?>

        <div class="sweet-alert-message">
          <span class="icon">✔️</span>
          <span>All Student correction requests have been reviewed successfully!</span>

        </div>
      <?php else: ?>
        <?php foreach ($studentRequests as $req): ?>
          <div class="mini-card">
            <div class="mini-card-header">Student: <?= htmlspecialchars($req['name']) ?></div>
            <div>Request: <?= htmlspecialchars($req['reason']) ?></div>
            <small>Type: <?= htmlspecialchars($req['type']) ?> | Date: <?= $req['date'] ?></small>
            <div class="mini-card-buttons">
              <button class="btn-approve" onclick="handleActionStudent('approved', <?= $req['id'] ?>, this)">Approve</button>
              <button class="btn-disapprove" onclick="handleActionStudent('rejected', <?= $req['id'] ?>, this)">Disapprove</button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>


    <!-- 3rd Card: Assign Subjects to Teachers -->
    <div class="card" id="assign-subjects-card">
      <h2>Assign Subjects to Teachers</h2>
      <form id="assignForm" onsubmit="return assignSubject(event)">
        <label for="teacherSelect">Select Teacher:</label>
        <select id="teacherSelect" name="teacher_num" required>
          <option value="" disabled selected>Select teacher</option>
          <?php foreach ($teachers as $teacher): ?>
            <option value="<?= htmlspecialchars($teacher['teach_num']) ?>">
              <?= htmlspecialchars($teacher['Name']) ?>
            </option>
          <?php endforeach; ?>
        </select>


        <label>Select Subjects:</label>
        <div class="multi-select-container" id="multi-subject-container">
          <button type="button" class="multi-select-btn" id="multiSubjectBtn">
            Select Subjects
            <span class="arrow" id="subjectArrow"></span>
          </button>
          <div class="dropdown" id="subjectDropdown">
            <?php foreach ($subjects as $subject): ?>
              <label>
                <input type="checkbox" value="<?= $subject['id'] ?>" />
                <?= htmlspecialchars($subject['name']) ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <input type="submit" value="Assign Subjects" />
      </form>
      <div id="assignResult" style="margin-top: 12px; font-weight: 600; color: green;"></div>
    </div>

  </div>
</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const multiSubjectBtn = document.getElementById('multiSubjectBtn');
  const subjectDropdown = document.getElementById('subjectDropdown');
  const subjectArrow = document.getElementById('subjectArrow');
  const subjectContainer = document.getElementById('multi-subject-container');

  function updateSubjectLabel() {
    const checked = subjectDropdown.querySelectorAll('input[type="checkbox"]:checked');
    if (checked.length === 0) {
      multiSubjectBtn.innerHTML = 'Select Subjects <span class="arrow" id="subjectArrow"></span>';
    } else if (checked.length === 1) {
      multiSubjectBtn.innerHTML = `${checked[0].parentElement.textContent.trim()} <span class="arrow" id="subjectArrow"></span>`;
    } else {
      multiSubjectBtn.innerHTML = `${checked.length} subjects selected <span class="arrow" id="subjectArrow"></span>`;
    }
  }

  multiSubjectBtn.addEventListener('click', () => {
    subjectDropdown.classList.toggle('open');
    subjectArrow.classList.toggle('open');
  });

  document.addEventListener('click', e => {
    if (!subjectContainer.contains(e.target)) {
      subjectDropdown.classList.remove('open');
      subjectArrow.classList.remove('open');
    }
  });

  subjectDropdown.querySelectorAll('input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', updateSubjectLabel);
  });

  updateSubjectLabel();

  function toggleDrawer(headerElem) {
    const content = headerElem.nextElementSibling;
    content.classList.toggle('open');
  }

  function handleActionTeacher(status, requestId, button) {
    fetch('../QueryModel/ajax_call.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'update_correction_status',
          request_id: requestId,
          status: status
        })
      })
      .then(response => {
        if (!response.ok) throw new Error('Correction request already processed');
        return response.text();
      })
      .then(data => {
        Swal.fire({
          icon: 'success',
          title: `Request ${status === 'approved' ? 'Approved' : 'Rejected'}!`,
          text: data,
          timer: 1800,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        });

        // Remove the complaint box from the UI
        const complaintBox = button.closest('.complaint');
        if (complaintBox) {
          const drawerContent = complaintBox.parentElement; // drawer-content div
          complaintBox.remove();

          // If no complaints remain, remove the entire drawer (teacher + complaints)
          if (drawerContent.children.length === 0) {
            const drawer = drawerContent.parentElement; // drawer div
            if (drawer) drawer.remove();

            // Optional: if you want to show the "All reviewed" message dynamically:
            const teacherRequestsCard = document.getElementById('teacher-requests-card');
            if (teacherRequestsCard && teacherRequestsCard.querySelectorAll('.drawer').length === 0) {
              // Show your "all reviewed" message if you want
              const messageDiv = document.createElement('div');
              messageDiv.className = 'sweet-alert-message';
              messageDiv.innerHTML = '<span class="icon">✔️</span> <span>All teacher correction requests have been reviewed successfully!</span>';
              teacherRequestsCard.appendChild(messageDiv);
            }
          }
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: error.message,
          timer: 2500,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        });
      });
  }

  function handleActionStudent(status, requestId, button) {
    fetch('../QueryModel/ajax_call.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'update_correction_status',
          request_id: requestId,
          status: status
        })
      })
      .then(response => {
        if (!response.ok) throw new Error('Correction request already processed');
        return response.text();
      })
      .then(data => {
        Swal.fire({
          icon: 'success',
          title: `Request ${status === 'approved' ? 'Approved' : 'Rejected'}!`,
          text: data,
          timer: 1800,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        });

        const miniCard = button.closest('.mini-card');
        if (miniCard) miniCard.remove();

        const studentCard = document.getElementById('student-requests-card');
        const remainingCards = studentCard.querySelectorAll('.mini-card');
        // const reviewedMsg = document.getElementById('student-reviewed-message');

        if (remainingCards.length === 0) {
          // reviewedMsg.style.display = 'block';

          const messageDiv = document.createElement('div');
          messageDiv.className = 'sweet-alert-message';
          messageDiv.innerHTML = '<span class="icon">✔️</span> <span>All student correction requests have been reviewed successfully!</span>';

          const studentCard = document.getElementById('student-requests-card');
          studentCard.appendChild(messageDiv); // ✅ Correct target;
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: error.message,
          timer: 2500,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        });
      });
  }


  function assignSubject(event) {
    event.preventDefault();

    const teacherId = document.getElementById('teacherSelect').value;
    const subjectDropdown = document.getElementById('subjectDropdown');
    const checkedSubjects = subjectDropdown.querySelectorAll('input[type="checkbox"]:checked');
    const selectedSubjects = Array.from(checkedSubjects).map(cb => cb.value);

    if (!teacherId || selectedSubjects.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Incomplete!',
        text: 'Please select a teacher and at least one subject.',
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
      });
      return false;
    }

    const formData = new URLSearchParams();
    formData.append('action', 'assign_subjects');
    formData.append('teacher_num', teacherId);
    selectedSubjects.forEach(id => formData.append('subject_ids[]', id));

    fetch('../QueryModel/ajax_call.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData
      })
      .then(response => {
        if (!response.ok) throw new Error('Already Choosen this subject');
        return response.text();
      })
      .then(data => {
        Swal.fire({
          icon: 'success',
          title: 'Subjects Assigned!',
          text: data,
          timer: 1800,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        });

        event.target.reset();
        subjectDropdown.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

        // Optional: Update the subject button label after reset
        updateSubjectLabel();
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: error.message,
          timer: 2500,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        });
      });

    return false;
  }
</script>