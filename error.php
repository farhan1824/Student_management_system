<?php
$errorMessages = [
    "invalid_teacher_login"      => "Invalid Teacher Number or Password!",
    "invalid_student_login"      => "Invalid Roll Number!",
    "session_expired"            => "Your session has expired. Please log in again.",
    "unauthorized_access"        => "You must log in to access this page.",
    "db_error"                   => "Something went wrong. Please try again later.",
    "student_empty_fields"       => "Fill up your form.",
    "student_user_exists"        => "User already exists with this email.",
    "student_invalid_email"      => "Invalid email format.",
    "photo_upload_error"         => "Failed to upload photo. Please try again.",
    "password_mismatch"          => "Passwords do not match.",
    "admin_not_found"            => "Admin not found.",
    "admin_password_mismatch"    => "Incorrect admin password.",
    "admin_empty_fields"         => "Please fill in all fields.",
    "teach_empty_fields"         => "Please fill in all fields.",
];

$hasError = isset($_GET['error']) && isset($errorMessages[$_GET['error']]);
$errorKey = $_GET['error'] ?? '';
$message = $hasError ? $errorMessages[$errorKey] : '';

// Determine redirect URL based on error type
$redirectUrl = '../index.php'; // default fallback

// Teacher login errors
if ($errorKey === 'invalid_teacher_login' || $errorKey === 'teach_empty_fields') {
    $redirectUrl = './teacher_login.php';
}
// Admin login errors
elseif (
    $errorKey === 'admin_not_found' ||
    $errorKey === 'admin_password_mismatch' ||
    $errorKey === 'admin_empty_fields'
) {
    $redirectUrl = './admin_login.php';
}
// Student registration errors
elseif (
    $errorKey === 'student_user_exists' ||
    $errorKey === 'student_invalid_email' ||
    $errorKey === 'student_empty_fields' ||
    $errorKey === 'photo_upload_error' ||
    $errorKey === 'password_mismatch'
) {
    $redirectUrl = './student_register.php';
}
// Student login and general session errors
elseif (
    $errorKey === 'invalid_student_login' ||
    $errorKey === 'session_expired' ||
    $errorKey === 'unauthorized_access'
) {
    $redirectUrl = './student/student_login.php';
}
?>


<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($hasError): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: <?= json_encode($message) ?>,
                confirmButtonColor: '#e74c3c',
                background: '#fefefe',
                backdrop: `rgba(0, 0, 0, 0.4)`,
                customClass: {
                    popup: 'elegant-popup'
                }
            }).then(() => {
                window.location.href = <?= json_encode($redirectUrl) ?>;
            });
        });
    </script>
<?php endif; ?>

<style>
    .swal2-popup.elegant-popup {
        border-radius: 20px !important;
        font-family: 'Segoe UI', sans-serif;
        padding: 25px;
        animation: fadeInPopup 0.3s ease-in-out;
    }

    @keyframes fadeInPopup {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>