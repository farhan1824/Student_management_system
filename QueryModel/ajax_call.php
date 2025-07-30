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
    if (!isset($_SESSION['student_roll']) && !isset($_SESSION['teacher_number'])&& !isset($_SESSION["register_student_roll"])&& !isset($_POST['request_id'])&& !isset($_POST['status'])&& !isset($_POST['teacher_num'])&&!isset($_POST['subject_ids'])) {
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

    case 'update_correction_status':
    if (!empty($_POST['request_id']) && !empty($_POST['status'])) {
        $requestId = $_POST['request_id'];
        $status = $_POST['status'];

        $result = updateCorrectionRequestStatus($conn, $requestId, $status);
        if ($result['success'] && $status === 'approved') {
            updateAttendanceIfApproved($conn, $requestId);
        }

        echo $result['message'];
    } else {
        echo "Request ID or status missing.";
    }
    break;



 case 'assign_subjects':
    if (isset($_POST['teacher_num'], $_POST['subject_ids']) && is_array($_POST['subject_ids'])) {
        $teacherNum = $_POST['teacher_num']; // Don't cast to int
        $subjectIds = array_map('intval', $_POST['subject_ids']); // subject_ids are integers
        $result = assignMultipleSubjectsToTeacher($conn, $teacherNum, $subjectIds);

        if ($result['success']) {
            echo $result['message'];
        } else {
            http_response_code(400);
            echo $result['message'];
        }
    } else {
        http_response_code(400);
        echo "Missing or invalid parameters.";
    }
    break;


        default:
            http_response_code(400);
            echo "Unknown action.";
            break;
    }
}
