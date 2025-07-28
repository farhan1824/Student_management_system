<?php
require_once '../db/db.php';
session_start();
// Fetch student info
$studentRoll = $_SESSION['register_student_roll'];
$sql = "SELECT id, name, profile_pic,email FROM students WHERE roll = '$studentRoll'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $studentId = $row['id'];
    $studentName = $row['name'];
    $studentEmail = $row['email'];
    $profilePic = $row['profile_pic'] ?? 'default.jpg';
}
$availableSubjects = [];
$subjectQuery = "
  SELECT DISTINCT s.id, s.name
  FROM subjects s
  JOIN teach_attendance ta ON ta.subject_id = s.id
  ORDER BY s.name ASC
";
$res = $conn->query($subjectQuery);
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $availableSubjects[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Subject Choice</title>
  <style>
    /* === Global Styling === */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #F4D03F, #FFCBA4);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
    }

    .card.profile-form {
      background: white;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      width: 100%;
      max-width: 900px;
      padding: 30px;
    }

    .form-flex {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
    }

    .left {
      flex: 1;
      text-align: center;
    }

    .profile-pic {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      border: 5px solid #F4D03F;
      object-fit: cover;
    }

    .middle {
      flex: 2;
      min-width: 300px;
    }

    .middle h2 {
      margin-bottom: 10px;
      color: #333;
    }

    .middle label {
      display: block;
      margin-top: 16px;
      font-weight: 600;
      color: #333;
    }

    .middle input[type="text"] {
      width: 100%;
      max-width: 450px;
      padding: 12px;
      font-size: 16px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    /* === Custom Dropdown === */
    .multi-select-container {
      position: relative;
      margin-top: 16px;
      max-width: 450px;
    }

    .multi-select-btn {
      width: 100%;
      padding: 12px 16px;
      font-size: 16px;
      background: #F4D03F;
      color: #333;
      border: 2px solid #F4D03F;
      border-radius: 8px;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .arrow {
      border: solid #333;
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
      background: white;
      border: 2px solid #F4D03F;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      max-height: 200px;
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
    }

    .dropdown label:hover {
      background: #FFF4CC;
    }

    .dropdown input[type="checkbox"] {
      margin-right: 10px;
    }

    /* === Submit Button === */
    .middle button[type="submit"] {
      margin-top: 24px;
      padding: 12px 24px;
      font-size: 16px;
      background: linear-gradient(to right, #F4D03F, #FFCBA4);
      border: none;
      border-radius: 8px;
      color: #333;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .middle button[type="submit"]:hover {
      background: linear-gradient(to right, #FFCBA4, #F4D03F);
    }
  </style>
</head>
<body>

<div class="card profile-form" id="welcomeCard">
  <div class="form-flex">
    <div class="left">
      <img src="../<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" class="profile-pic" />
    </div>
    <div class="middle">
      <h2>Hello, <?php echo htmlspecialchars($studentName); ?> 👋</h2>
      <p>Roll: <strong><?php echo htmlspecialchars($studentRoll); ?></strong></p>

      <label for="student_name">Full Name</label>
      <input type="text" id="student_name" value="<?php echo htmlspecialchars($studentName); ?>" />

      <label for="student_email">Email</label>
      <input type="text" id="student_email" value="<?php echo htmlspecialchars($studentEmail); ?>" />

      <label for="subject_multi_select">Choose Subjects</label>
      <div class="multi-select-container" id="subject_multi_select">
        <button type="button" class="multi-select-btn" aria-haspopup="listbox" aria-expanded="false" id="multiSelectBtn">
          Select Subjects
          <span class="arrow" id="arrow"></span>
        </button>
        <div class="dropdown" role="listbox" aria-multiselectable="true" tabindex="-1" id="dropdownList">
          <?php foreach ($availableSubjects as $sub): ?>
            <label>
              <input type="checkbox" value="<?php echo $sub['id']; ?>" />
              <?php echo htmlspecialchars($sub['name']); ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <form id="studentInfoForm" method="post" action="your_submit_endpoint.php">
        <input type="hidden" name="student_name" id="hiddenStudentName" />
        <div id="hiddenSubjectsContainer"></div>
        <button type="submit">Submit</button>
      </form>
    </div>
  </div>
</div>

<script>
  const multiSelectBtn = document.getElementById('multiSelectBtn');
  const dropdown = document.getElementById('dropdownList');
  const arrow = document.getElementById('arrow');
  const multiSelectContainer = document.getElementById('subject_multi_select');

  const studentNameInput = document.getElementById('student_name');
  const studentEmailInput = document.getElementById('student_email');
  const hiddenStudentName = document.getElementById('hiddenStudentName');
  const hiddenSubjectsContainer = document.getElementById('hiddenSubjectsContainer');
  const studentInfoForm = document.getElementById('studentInfoForm');

  function updateButtonLabel() {
    const checked = dropdown.querySelectorAll('input[type="checkbox"]:checked');
    if (checked.length === 0) {
      multiSelectBtn.innerHTML = 'Select Subjects <span class="arrow" id="arrow"></span>';
    } else if (checked.length === 1) {
      multiSelectBtn.innerHTML = checked[0].parentElement.textContent.trim() + ' <span class="arrow" id="arrow"></span>';
    } else {
      multiSelectBtn.innerHTML = `${checked.length} subjects selected <span class="arrow" id="arrow"></span>`;
    }
  }

  multiSelectBtn.addEventListener('click', () => {
    dropdown.classList.toggle('open');
    arrow.classList.toggle('open');
    multiSelectBtn.setAttribute('aria-expanded', dropdown.classList.contains('open'));
  });

  document.addEventListener('click', (e) => {
    if (!multiSelectContainer.contains(e.target)) {
      dropdown.classList.remove('open');
      arrow.classList.remove('open');
      multiSelectBtn.setAttribute('aria-expanded', false);
    }
  });

  dropdown.querySelectorAll('input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', updateButtonLabel);
  });

  studentInfoForm.addEventListener('submit', () => {
    hiddenSubjectsContainer.innerHTML = '';
    dropdown.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'subjects[]';
      input.value = cb.value;
      hiddenSubjectsContainer.appendChild(input);
    });

    hiddenStudentName.value = studentNameInput.value.trim();
  });

  updateButtonLabel();
</script>

</body>
</html>
