<?php
session_start();
if (!isset($_SESSION['teacher_number'])) {
    header("Location: teacher_login.php");
    exit();
}

require_once '../db/db.php';

$teacherNumber = $_SESSION['teacher_number'];
$teacherName = "";

// Get teacher name
$sql = "SELECT name FROM teach_details WHERE teach_num = '$teacherNumber'";
$result = $conn->query($sql);
if ($result && $result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $teacherName = $row['name'];
} else {
    $teacherName = "Unknown";
}

// Get subjects and class counts
$subjectQuery = "
    SELECT s.id, s.name AS subject_name, s.class_count
    FROM teacher_subjects ts
    JOIN subjects s ON ts.subject_id = s.id
    WHERE ts.teacher_num = '$teacherNumber'
";
$subjectRows = $conn->query($subjectQuery)->fetch_all(MYSQLI_ASSOC);

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="../styles.css" />
    <style>
        table {
            background: #fff;
            color: #000;
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        .progress-container {
            background: #ddd;
            border-radius: 10px;
            height: 20px;
            width: 120px;
            margin: 0 auto;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            transition: width 0.3s ease;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin: 20px auto;
            display: inline-block;
        }
        .logout-btn:hover {
            background: #c82333;
        }
        h2 {
            text-align: center;
            margin-top: 30px;
        }
        .subject-link.disabled {
            pointer-events: none;
            color: gray;
            text-decoration: none;
        }
        .subject-link.active {
            color: #000000ff;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($teacherName); ?>!</h2>

    <?php if (!empty($subjectRows)) : ?>
    <table border="1" aria-label="Subjects and Attendance">
        <thead>
            <tr>
                <th scope="col">Subject</th>
                <th scope="col">Class Count</th>
                <th scope="col">Progress</th>
                <th scope="col">Mark Today</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subjectRows as $subject) :
                $sid = $subject['id'];
                $classCount = (int)$subject['class_count'];

                // Check if attendance already marked today
                $checkSql = "SELECT id FROM teach_attendance 
                             WHERE teacher_num = '$teacherNumber' 
                             AND subject_id = $sid 
                             AND attendance_date = '$today'";
                $alreadySubmitted = $conn->query($checkSql)->num_rows > 0;

                // Count total attendance
                $attSql = "SELECT COUNT(*) as total FROM teach_attendance 
                           WHERE teacher_num = '$teacherNumber' AND subject_id = $sid";
                $attResult = $conn->query($attSql);
                $attended = ($attResult && $attResult->num_rows > 0) ? $attResult->fetch_assoc()['total'] : 0;

                $percentage = $classCount > 0 ? round(($attended / $classCount) * 100) : 0;
                $progressColor = $percentage >= 80 ? '#28a745' : ($percentage >= 50 ? '#ffc107' : '#dc3545');
                $rowStyle = ($attended >= $classCount && $classCount > 0) ? 'style="background: #d4edda;"' : '';
            ?>
            <tr <?php echo $rowStyle; ?>>
                <td>
                    <a href="./student_attendence.teacherview.php?subject_id=<?php echo $sid; ?>"
                       class="subject-link <?php echo $alreadySubmitted ? 'active' : 'disabled'; ?>"
                       id="link-<?php echo $sid; ?>">
                       <?php echo htmlspecialchars($subject['subject_name']); ?>
                    </a>
                </td>
                <td><?php echo $classCount; ?></td>
                <td>
                    <div class="progress-container" title="<?php echo $attended; ?>/<?php echo $classCount; ?> classes">
                        <div class="progress-bar" style="width: <?php echo $percentage; ?>%; background: <?php echo $progressColor; ?>;"></div>
                    </div>
                    <small><?php echo $percentage; ?>%</small>
                </td>
                <td>
                    <input type="checkbox"
                           onchange="markAttendance(<?php echo $sid; ?>, this)"
                           <?php echo $alreadySubmitted ? 'checked disabled aria-checked="true"' : 'aria-checked="false"'; ?>
                           aria-label="Mark attendance for <?php echo htmlspecialchars($subject['subject_name']); ?>">
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
        <p style="text-align:center;">No subjects assigned.</p>
    <?php endif; ?>

    <div style="text-align: center;">
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>

<script>
function markAttendance(subjectId, checkbox) {
    if (!checkbox.checked) return;

    checkbox.disabled = true;

    const link = document.getElementById('link-' + subjectId);

    // Add a temporary "Saving..." span safely
    const savingText = document.createElement('small');
    savingText.innerText = ' Saving...';
    savingText.style.color = 'gray';
    savingText.classList.add('saving-status'); // for later removal
    checkbox.parentElement.appendChild(savingText);

    fetch('../QueryModel/ajax_call.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'mark_attendance',
            subject_id: subjectId
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Server error');
        return response.text();
    })
    .then(data => {
        alert(data);
        console.log("Attendance submitted:", new Date().toLocaleTimeString());

        if (link) {
            link.classList.remove('disabled');
            link.classList.add('active');
        }

        // Remove "Saving..." message
        const savingEl = checkbox.parentElement.querySelector('.saving-status');
        if (savingEl) savingEl.remove();

        // Update progress bar visually
        const row = checkbox.closest('tr');
        const classCount = parseInt(row.children[1].innerText, 10);
        const smallTag = row.querySelector('td:nth-child(3) small');
        const bar = row.querySelector('.progress-bar');

        if (classCount && bar && smallTag) {
            let currentPercent = parseInt(smallTag.innerText.replace('%', ''), 10) || 0;
            let attended = Math.round((currentPercent / 100) * classCount);

            attended += 1; // Add newly marked class
            const newPercent = Math.round((attended / classCount) * 100);

            // Update progress bar width and color
            bar.style.width = newPercent + '%';
            bar.style.background = newPercent >= 80 ? '#28a745' : newPercent >= 50 ? '#ffc107' : '#dc3545';

            // Update % text
            smallTag.innerText = newPercent + '%';

            // Update tooltip title with attended classes count
            const container = row.querySelector('.progress-container');
            if (container) {
                container.title = `${attended}/${classCount} classes`;
            }
        }
    })
    .catch(error => {
        alert('Error submitting attendance');
        console.error(error);

        // Roll back checkbox changes
        checkbox.checked = false;
        checkbox.disabled = false;

        const savingEl = checkbox.parentElement.querySelector('.saving-status');
        if (savingEl) savingEl.remove();
    });
}

// Auto-refresh page at midnight
(function refreshAtMidnight() {
    const now = new Date();
    const midnight = new Date();
    midnight.setHours(24, 0, 0, 0);
    const msToMidnight = midnight.getTime() - now.getTime();
    setTimeout(() => location.reload(), msToMidnight);
})();
</script>
</body>
</html>
