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
WHERE c.requested_by = 'student' AND c.status = 'pending' AND reason_type='attendance'
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
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

        /* Floating Chat Icon */
        #complaint-icon {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #007bff;
            color: white;
            font-size: 28px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 999;
            transition: background 0.3s ease;
        }

        #complaint-icon:hover {
            background: #0056b3;
        }

        #complaint-popup {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 370px;
            max-height: 460px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            display: none;
            flex-direction: column;
            padding: 20px;
            z-index: 1000;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 15px;
        }

        #complaint-popup h3 {
            margin: 0 0 12px;
            color: #007bff;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
        }

        #close-complaint-popup {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            color: #aaa;
            font-weight: bold;
        }

        .complaint-card {
            background: #f1f5f9;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 14px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
            font-size: 15px;
            color: #333;
        }

        .complaint-card strong {
            font-size: 15.5px;
            color: #111;
        }

        .complaint-card span {
            display: block;
            margin: 6px 0;
            color: #555;
        }

        .complaint-card small {
            display: block;
            color: #777;
            font-size: 13px;
        }

        .mini-card-buttons {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .mini-card-buttons .btn-approve,
        .mini-card-buttons .btn-disapprove {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .btn-approve {
            background-color: #28a745;
            color: white;
        }

        .btn-approve:hover {
            background-color: #218838;
        }

        .btn-disapprove {
            background-color: #dc3545;
            color: white;
        }

        .btn-disapprove:hover {
            background-color: #c82333;
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

    <!-- Floating Complaint Icon -->
    <div id="complaint-icon" title="View Student Complaints">💬</div>

    <!-- Complaint Popup UI -->
    <div id="complaint-popup">
        <h3>Student Complaints</h3>
        <div id="close-complaint-popup" onclick="toggleComplaintPopup()">×</div>
        <div id="complaint-popup-content">
            <?php if (!empty($studentRequests)) : ?>
                <?php foreach ($studentRequests as $req): ?>
                    <div class="complaint-card">
                        <strong><?php echo htmlspecialchars($req['name']); ?> (<?php echo htmlspecialchars($req['roll']); ?>)</strong>
                        <span><strong>Issue:</strong> <?php echo htmlspecialchars($req['reason']); ?></span>
                        <small><strong>Date:</strong> <?php echo $req['date']; ?> | <strong>Type:</strong> <?php echo htmlspecialchars($req['type']); ?></small>
                        <div class="mini-card-buttons">
                            <button class="btn-approve" onclick="handleActionStudent('approved', <?php echo $req['id']; ?>, this)">Approve</button>
                            <button class="btn-disapprove" onclick="handleActionStudent('rejected', <?php echo $req['id']; ?>, this)">Disapprove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div style="text-align:center; padding: 10px;">No complaints found.</div>
            <?php endif; ?>
        </div>
    </div>

    <div style="text-align: center;">
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>
</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function markAttendance(subjectId, checkbox) {
        if (!checkbox.checked) return;

        checkbox.disabled = true;

        const link = document.getElementById('link-' + subjectId);

        // Add a temporary "Saving..." span safely
        const savingText = document.createElement('small');
        savingText.innerText = ' Saving...';
        savingText.style.color = 'gray';
        savingText.classList.add('saving-status');
        checkbox.parentElement.appendChild(savingText);

        fetch('../QueryModel/ajax_call.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
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
                // ✅ Stylish Success Popup
                Swal.fire({
                    icon: 'success',
                    title: 'Attendance Submitted!',
                    text: data,
                    timer: 1800,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });

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
                    attended += 1;
                    const newPercent = Math.round((attended / classCount) * 100);

                    bar.style.width = newPercent + '%';
                    bar.style.background = newPercent >= 80 ? '#28a745' : newPercent >= 50 ? '#ffc107' : '#dc3545';
                    smallTag.innerText = newPercent + '%';

                    const container = row.querySelector('.progress-container');
                    if (container) {
                        container.title = `${attended}/${classCount} classes`;
                    }
                }
            })
            .catch(error => {
                // ❌ Stylish Error Popup
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Failed',
                    text: 'Something went wrong. Please try again.',
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });

                checkbox.checked = false;
                checkbox.disabled = false;

                const savingEl = checkbox.parentElement.querySelector('.saving-status');
                if (savingEl) savingEl.remove();

                console.error(error);
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

                // Remove the complaint card
                const miniCard = button.closest('.complaint-card');
                if (miniCard) miniCard.remove();

                // Check if all cards are reviewed
                const complaintContainer = document.getElementById('complaint-popup-content');
                const remainingCards = complaintContainer.querySelectorAll('.complaint-card');

                if (remainingCards.length === 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'All Done!',
                        text: 'All student correction requests have been reviewed.',
                        confirmButtonText: 'Nice!',
                        background: '#D2D3BE',
                        color: '#000',
                        iconColor: '#4CAF50',
                        customClass: {
                            popup: 'rounded-popup'
                        }
                    });

                    // Optionally, close the popup after review
                    document.getElementById('complaint-popup').style.display = 'none';
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

    function toggleComplaintPopup() {
        const popup = document.getElementById('complaint-popup');
        popup.style.display = (popup.style.display === 'flex' || popup.style.display === 'block') ? 'none' : 'flex';
    }

    // ✅ Add this to activate the toggle when icon is clicked
    document.getElementById('complaint-icon').addEventListener('click', toggleComplaintPopup);

    // Close popup on outside click
    window.addEventListener('click', function(e) {
        const popup = document.getElementById('complaint-popup');
        const icon = document.getElementById('complaint-icon');
        if (popup.style.display !== 'none' && !popup.contains(e.target) && !icon.contains(e.target)) {
            popup.style.display = 'none';
        }
    });
</script>