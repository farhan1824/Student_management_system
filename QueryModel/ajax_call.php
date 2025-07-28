<?php
session_start();
require_once '../db/db.php';
require_once 'basicfunctions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // if (!isset($_SESSION['teacher_number'])) {
    //     http_response_code(403);
    //     echo "Unauthorized access.";
    //     exit();
    // }
    if (!isset($_SESSION['student_roll']) && !isset($_SESSION['teacher_number'])&& !isset($_SESSION["register_student_roll"])) {
    http_response_code(403);
    echo "Unauthorized access.";
    exit();
}

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'mark_attendance':
            if (isset($_POST['subject_id'])) {
                InputTeachAttendence($conn); // Function defined in basicfunctions.php
            } else {
                http_response_code(400);
                echo "Subject ID missing.";
            }
            break;

        case 'student_attendance':
          if (isset($_POST['student_id'], $_POST['subject_id'], $_POST['date'])) {
              InputStudentAttendance($conn);
          } else {
              http_response_code(400);
              echo "Missing required data.";
          }
          break;

        case 'request_attendance_correction':
            if (isset($_POST['student_id'], $_POST['subject_id'], $_POST['date'], $_POST['reason'])) {
                RequestAttendanceCorrection($conn);
            } else {
                http_response_code(400);
                echo "Missing required data.";
            }
            break;

        case 'request_recheck':
            if (isset($_SESSION['student_roll'], $_POST['subject_name'], $_POST['reason_type'], $_POST['reason'])) {
                RequestRecheckStudent($conn);
            } else {
                http_response_code(400);
                echo "Missing required data.";
            }
            break;
        case 'get_student_subjects':
            if (isset($_SESSION['student_roll'])) {
                GetStudentSubjects($conn);
            } else {
                http_response_code(403);
                echo json_encode([]);
            }
            break;
        case 'save_selected_subjects':
    if (isset($_SESSION['register_student_roll'])) {
        studentSubjectSelect($conn);
    } else {
        http_response_code(403);
        echo "Session expired.";
    }
    break;


        default:
            http_response_code(400);
            echo "Unknown action.";
            break;
    }
}
