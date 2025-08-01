<?php
$errorMessages = [
    "invalid_teacher_login" => "Invalid Teacher Number or Password!",
    "invalid_student_login" => "Invalid Roll Number!",
    "session_expired"       => "Your session has expired. Please log in again.",
    "unauthorized_access"   => "You must log in to access this page.",
    "db_error"              => "Something went wrong. Please try again later.",
    "empty_fields"          => "Fill up your form.",
    "user_exists"           => "User already exists with this email.",
    "invalid_email"         => "Invalid email format.",
    "photo_upload_error"    => "Failed to upload photo. Please try again.",
    "password_mismatch"     => "Passwords do not match.",
    "admin_not_found"       => "Admin not found.",
];

$hasError = isset($_GET['error']) && isset($errorMessages[$_GET['error']]);
$message = $hasError ? $errorMessages[$_GET['error']] : '';
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
                // Redirect after user clicks OK
                window.location.href = 'index.php';
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