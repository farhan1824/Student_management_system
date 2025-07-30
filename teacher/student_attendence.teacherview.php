<?php
require_once '../db/db.php';
session_start();
if (!isset($_SESSION['teacher_number'])) {
    header("Location: teacher_login.php");
    exit();
}
$teacherNumber = $_SESSION['teacher_number'];
$subjectId = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$today = date('Y-m-d');

// Get subject name
$subjectName = "Unknown Subject";
$subjectSql = "SELECT name FROM subjects WHERE id = $subjectId";
$subjectRes = $conn->query($subjectSql);
if ($subjectRes && $subjectRes->num_rows > 0) {
    $subjectName = $subjectRes->fetch_assoc()['name'];
}

// Get students and attendance
$studentSql = "
    SELECT s.id, s.roll, s.name,
        (SELECT 1 FROM student_attendance 
         WHERE student_id = s.id 
         AND subject_id = $subjectId 
         AND attendance_date = '$selectedDate') AS attended
    FROM students s
    INNER JOIN student_subjects ss ON ss.student_id = s.id
    WHERE ss.subject_id = $subjectId
";


$students = $conn->query($studentSql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .calendar-wrapper {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="date"] {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
            background-color: #fff;
            color: #333;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        input[type="date"]:hover {
            border-color: #007bff;
        }

        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background: #007bff;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .back-btn {
            display: block;
            margin: 30px auto;
            width: 200px;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .back-btn:hover {
            background: #0056b3;
        }
        .correction-btn {
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .correction-btn:hover {
            background: linear-gradient(135deg, #e03e57, #e1441d);
            box-shadow: 0 4px 8px rgba(0,0,0,0.25);
            transform: translateY(-2px);
        }

    </style>
</head>
<body>

<h2>Attendance for "<?php echo htmlspecialchars($subjectName); ?>"</h2>

<div class="calendar-wrapper">
    <label for="date">Select Date: </label>
    <input
        type="date"
        id="date"
        name="date"
        value="<?php echo $selectedDate; ?>"
        max="<?php echo $today; ?>"  
    >
</div>

<?php if ($students && $students->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Roll</th>
                <th>Name</th>
                <th>
                    <?php
                    if ($selectedDate === $today) {
                        echo "Mark Attendance";
                    } else {
                        echo "Attendance Status";
                    }
                    ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $students->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['roll']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>
                           <?php if ($selectedDate === $today): ?>
                               <!-- For today, show checkbox -->
                               <input 
                                   type="checkbox"
                                   onchange="markStudentAttendance(<?php echo $row['id']; ?>, this)"
                                   <?php echo $row['attended'] ? 'checked disabled' : ''; ?>
                               >
                           <?php else: ?>
                               <?php
                                   if ($row['attended']) {
                                       echo "<strong style='color:green;'>Present</strong>";
                                   } 


                             else {
                                       echo "<strong style='color:red;'>Absent</strong>";?>
                                       <br>
                                <button onclick="requestCorrection(<?php echo $row['id']; ?>)" class="correction-btn">Request Correction</button>
                                  <?php } ?>
                           <?php endif; ?>
                    </td>

                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align:center;">No students found for this subject.</p>
<?php endif; ?>

<button class="back-btn" onclick="window.location.href='teacher_dashboard.php'">← Back to Dashboard</button>


</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // On date change, reload with the selected date
document.getElementById('date').addEventListener('change', function () {
    const date = this.value;
    const url = new URL(window.location.href);
    url.searchParams.set('date', date);
    window.location.href = url.href;
});

// AJAX to mark attendance (only works for today)
function markStudentAttendance(studentId, checkbox) {
    if (!checkbox.checked) return;
    checkbox.disabled = true;

    fetch('../QueryModel/ajax_call.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'student_attendance',
            student_id: studentId,
            subject_id: <?php echo $subjectId; ?>,
            date: "<?php echo $selectedDate; ?>"
        })
    })
    .then(response => response.text())
    .then(data => {
        Swal.fire({
            icon: 'success',
            title: 'Attendance Marked',
            text: data,
            timer: 2000,
            toast: true,
            position: 'top-end',
            showConfirmButton: false
        });
    })
    .catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error marking attendance.',
            toast: true,
            position: 'top-end',
            timer: 2500,
            showConfirmButton: false
        });
        checkbox.checked = false;
        checkbox.disabled = false;
        console.error(err);
    });
}

function requestCorrection(studentId) {
    Swal.fire({
        title: 'Request Attendance Correction',
        input: 'text',
        inputLabel: 'Reason',
        inputPlaceholder: 'Enter your reason...',
        showCancelButton: true,
        confirmButtonText: 'Submit',
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#dc3545',
        inputValidator: (value) => {
            if (!value) return 'You need to provide a reason!';
        }
    }).then(result => {
        if (!result.isConfirmed) return;

        fetch('../QueryModel/ajax_call.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'request_attendance_correction',
                student_id: studentId,
                subject_id: <?php echo $subjectId; ?>,
                date: "<?php echo $selectedDate; ?>",
                reason: result.value
            })
        })
        .then(res => res.text())
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Correction Requested',
                text: data,
                timer: 2000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error requesting correction.',
                toast: true,
                position: 'top-end',
                timer: 2500,
                showConfirmButton: false
            });
            console.error(err);
        });
    });
}

</script>
