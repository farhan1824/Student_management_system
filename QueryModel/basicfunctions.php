<?php
require_once '../db/db.php';

// function isInputEmpty($firstinput, $secondinput) {
//     return empty(trim($firstinput)) || empty(trim($secondinput));
// }
function isInputEmpty(...$inputs) {
    foreach ($inputs as $input) {
        if (empty(trim($input))) {
            return true;
        }
    }
    return false;
}

function InputTeachAttendence($conn) {
    if (!isset($_SESSION['teacher_number'])) {
        http_response_code(403);
        echo "Unauthorized access.";
        return;
    }

    $teacherNumber = $conn->real_escape_string($_SESSION['teacher_number']);
    $subjectId = intval($_POST['subject_id']);
    $today = date('Y-m-d');

    // Check if already submitted
    $check = "SELECT id FROM teach_attendance 
              WHERE teacher_num = '$teacherNumber' 
              AND subject_id = $subjectId 
              AND attendance_date = '$today'";
    $res = $conn->query($check);

   if ($res && $res->num_rows > 0) {
    http_response_code(200); // ✅ Still valid response
    echo "Attendance already marked.";
    return;
    }

    // Insert attendance
    $insert = "INSERT INTO teach_attendance (teacher_num, subject_id, attendance_date) 
               VALUES ('$teacherNumber', $subjectId, '$today')";
    
    if ($conn->query($insert)) 
        {
         http_response_code(200); // ✅ This ensures JS sees it as "ok"
         echo "Attendance recorded successfully.";
         } 
    else {
         http_response_code(500);
         echo "Failed to record attendance: " . $conn->error;
    }
}

function InputStudentAttendance($conn) {
    // session_start();
    if (!isset($_SESSION['teacher_number'])) {
        http_response_code(403);
        echo "Unauthorized.";
        exit();
    }

    $studentId = intval($_POST['student_id']);
    $subjectId = intval($_POST['subject_id']);
    $date = $_POST['date'];

    // Check duplicate
    $check = "SELECT id FROM student_attendance 
              WHERE student_id = $studentId 
              AND subject_id = $subjectId 
              AND attendance_date = '$date'";
    $res = $conn->query($check);
    if ($res && $res->num_rows > 0) {
        echo "Already marked.";
        return;
    }

    // Insert
    $insert = "INSERT INTO student_attendance (student_id, subject_id, attendance_date, status) 
               VALUES ($studentId, $subjectId, '$date', 'Present')";
    if ($conn->query($insert)) {
        echo "Attendance marked.";
    } else {
        http_response_code(500);
        echo "Insert failed.";
    }
}

function RequestAttendanceCorrection($conn)
{
    if (!isset($_SESSION['teacher_number'])) {
        http_response_code(403);
        echo "Unauthorized access.";
        return;
    }

    $teacher = $_SESSION['teacher_number'];
    $studentId = intval($_POST['student_id']);
    $subjectId = intval($_POST['subject_id']);
    $date = $conn->real_escape_string($_POST['date']);
    $reason = $conn->real_escape_string($_POST['reason']);
    
    // Set reason_type as 'attendance' for teacher requests
    $reasonType = 'attendance';

    // Check for existing pending request
    $checkSql = "SELECT id FROM correction_requests 
                 WHERE student_id = $studentId 
                 AND subject_id = $subjectId 
                 AND attendance_date = '$date' 
                 AND reason_type = '$reasonType'
                 AND status = 'pending'";
    
    $checkResult = $conn->query($checkSql);
    if ($checkResult && $checkResult->num_rows > 0) {
        echo "A correction request is already pending.";
        return;
    }

    // Insert new request
    $insertSql = "INSERT INTO correction_requests 
        (requested_by, submitted_by_id, student_id, subject_id, attendance_date, reason_type, reason) 
        VALUES 
        ('teacher', '$teacher', $studentId, $subjectId, '$date', '$reasonType', '$reason')";

    if ($conn->query($insertSql)) {
        echo "Correction request submitted successfully.";
    } else {
        http_response_code(500);
        echo "Database error: " . $conn->error;
    }
}
function RequestRecheckStudent($conn)
{
    if (!isset($_SESSION['student_roll'])) {
        http_response_code(403);
        echo "Unauthorized access.";
        return;
    }

    $roll = $_SESSION['student_roll'];
    $studentRes = $conn->query("SELECT id FROM students WHERE roll = '$roll'");
    if (!$studentRes || $studentRes->num_rows === 0) {
        http_response_code(404);
        echo "Student not found.";
        return;
    }

    $studentId = $studentRes->fetch_assoc()['id'];
    $subjectName = $conn->real_escape_string($_POST['subject_name']);
    $reasonType = $conn->real_escape_string($_POST['reason_type']);
    $reason = $conn->real_escape_string($_POST['reason']);
    $date = isset($_POST['date']) ? $conn->real_escape_string($_POST['date']) : null;

    // Get subject ID from name
    $subRes = $conn->query("SELECT id FROM subjects WHERE name = '$subjectName'");
    if (!$subRes || $subRes->num_rows === 0) {
        http_response_code(404);
        echo "Subject not found.";
        return;
    }
    $subjectId = $subRes->fetch_assoc()['id'];

    // Check duplicate
    $checkSql = "SELECT id FROM correction_requests 
                 WHERE student_id = $studentId 
                 AND subject_id = $subjectId 
                 AND reason_type = '$reasonType'
                 " . ($date ? "AND attendance_date = '$date'" : "") . "
                 AND status = 'pending'";
    $checkRes = $conn->query($checkSql);
    if ($checkRes && $checkRes->num_rows > 0) {
        echo "A pending request already exists.";
        return;
    }

    // Insert
    $insertSql = "INSERT INTO correction_requests 
        (requested_by, submitted_by_id, student_id, subject_id, attendance_date, reason_type, reason) 
        VALUES ('student', '$roll', $studentId, $subjectId, " . ($date ? "'$date'" : "NULL") . ", '$reasonType', '$reason')";

    if ($conn->query($insertSql)) {
        echo "Recheck request submitted successfully.";
    } else {
        http_response_code(500);
        echo "Database error: " . $conn->error;
    }
}
function GetStudentSubjects($conn) {
    $roll = $_SESSION['student_roll'];
    // Get student id
    $studentRes = $conn->query("SELECT id FROM students WHERE roll = '$roll'");
    if (!$studentRes || $studentRes->num_rows === 0) {
        http_response_code(404);
        echo json_encode([]);
        return;
    }
    $studentId = $studentRes->fetch_assoc()['id'];

    // Get subjects of the student
    $sql = "
        SELECT su.name FROM student_subjects ss
        JOIN subjects su ON ss.subject_id = su.id
        WHERE ss.student_id = $studentId
    ";
    $result = $conn->query($sql);

    $subjects = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($subjects);
}
function userExists( $conn, string $email): bool {
    $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
    if (!$stmt) {
        // Optional: log or handle error
        return false;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    $exists = $stmt->num_rows > 0;
    $stmt->close();

    return $exists;
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function generateFullName($first, $middle, $last) {
    $first = trim($first);
    $middle = trim($middle);
    $last = trim($last);

    $fullName = $first;
    if (!empty($middle)) {
        $fullName .= " " . $middle;
    }
    $fullName .= " " . $last;

    return $fullName;
}

function generateRoll($conn) {
    do {
        $random = rand(10000, 99999);
        $roll = "CSE-" . $random;

        // Ensure uniqueness
        $stmt = $conn->prepare("SELECT id FROM students WHERE roll = ?");
        $stmt->bind_param("s", $roll);
        $stmt->execute();
        $stmt->store_result();

        $exists = $stmt->num_rows > 0;
        $stmt->close();

    } while ($exists);

    return $roll;
}
 
// function allCheckAndInsertData($firstName, $middleName, $lastName,$photoFilename, $dob, $gender, $nationality, $birthCountry, $email, $conn) {
//     // Check required inputs (you can use your existing validation function)
//     if (isInputEmpty($firstName, $lastName, $dob, $gender, $nationality, $birthCountry, $email))
//      {
//         // Redirect with error
//         header("Location: student_register.php?error=empty_fields");
//         exit();
//     }
//     if (!isValidEmail($email)) {
//         // Redirect with error
//         header("Location: student_register.php?error=invalid_email");
//         exit();
//     }
//     if (userExists($conn, $email)) {
//         // Redirect with error
//         header("Location: student_register.php?error=user_exists");
//         exit();
//     }

//     // Generate full name
//     $fullName = generateFullName($firstName, $middleName, $lastName);

//     // Generate roll number
//     $roll = generateRoll($conn);

//     // Insert into DB
//     $stmt = $conn->prepare("INSERT INTO students (roll, name,profile_pic,dob, gender, nationality, birth_country, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
//     if (!$stmt) {
//         die("Prepare failed: " . $conn->error);
//     }

//     $stmt->bind_param("ssssssss", $roll, $fullName,$photoFilename, $dob, $gender, $nationality, $birthCountry, $email);

//     if ($stmt->execute()) {
//         // Save to session
//         $_SESSION['student_roll'] = $roll;
//         $_SESSION['student_name'] = $fullName;
        
//         // Redirect to dashboard
//         header("Location: student_dashboard.php");
//         exit();
//     } else {
//         echo "Failed to register student: " . $stmt->error;
//     }

//     $stmt->close();
//     $conn->close();
// }
function allCheckAndInsertData($firstName, $middleName, $lastName, $dob, $gender, $nationality, $birthCountry, $email, $conn) {
// function allCheckAndInsertData($firstName, $middleName, $lastName, $photoFilename, $dob, $gender, $nationality, $birthCountry, $email, $conn) {
    // Check required inputs
    if (isInputEmpty($firstName, $lastName, $dob, $gender, $nationality, $birthCountry, $email)) {
        header("Location: student_register.php?error=empty_fields");
        exit();
    }
    if (!isValidEmail($email)) {
        header("Location: student_register.php?error=invalid_email");
        exit();
    }
    if (userExists($conn, $email)) {
        header("Location: student_register.php?error=user_exists");
        exit();
    }

    // Generate full name
    $fullName = generateFullName($firstName, $middleName, $lastName);

    // Generate roll number
    $roll = generateRoll($conn);

    // Insert into DB - note the comma after profile_pic
    $stmt = $conn->prepare("INSERT INTO students (roll, name, dob, gender, nationality, birth_country, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
    // $stmt = $conn->prepare("INSERT INTO students (roll, name, profile_pic, dob, gender, nationality, birth_country, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // 8 parameters, so 8 's' in type string
    $stmt->bind_param("sssssss", $roll, $fullName, $dob, $gender, $nationality, $birthCountry, $email);
    // $stmt->bind_param("ssssssss", $roll, $fullName, $photoFilename, $dob, $gender, $nationality, $birthCountry, $email);

    if ($stmt->execute()) {
        $_SESSION['register_student_roll'] = $roll;
        $_SESSION['student_name'] = $fullName;
        // $_SESSION['first_login'] = time(); // Set first login to true
        // $_SESSION['first_login'] = true;
        
        // header("Location: student_dashboard.php");
        header("Location:student_subject_choice.php");
        exit();
    } else {
        echo "Failed to register student: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>





