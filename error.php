<?php
// Error codes and messages
$errorMessages = [
    "invalid_teacher_login" => "Invalid Teacher Number or Password!",
    "invalid_student_login" => "Invalid Roll Number!",
    "session_expired"       => "Your session has expired. Please log in again.",
    "unauthorized_access"   => "You must log in to access this page.",
    "db_error"              => "Something went wrong. Please try again later.",
    "empty_fields"          => "Fill up your form.",
    "user_exists"          => "User already exists with this email.",
    "invalid_email"         => "Invalid email format.",
    "photo_upload_error"    => "Failed to upload photo. Please try again.",
];

// Show only if a valid error exists
$hasError = isset($_GET['error']) && isset($errorMessages[$_GET['error']]);
$message = $hasError ? $errorMessages[$_GET['error']] : '';
?>

<?php if ($hasError): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <style>
        body {
            background: #2c3e50;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .error-box {
            background: #e74c3c;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            text-align: center;
            animation: fadeIn 0.4s ease;
        }

        .error-box h2 {
            margin: 0 0 15px;
            font-size: 24px;
        }

        .error-box p {
            font-size: 18px;
        }

        .error-box a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #34495e;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .error-box a:hover {
            background: #2c3e50;
        }

        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }
    </style>
</head>
<body>
    <div class="error-box">
        <h2>Error</h2>
        <p><?= htmlspecialchars($message) ?></p>
        <a href="./index.php">← Go Back to Home</a>
    </div>
</body>
</html>
<?php endif; ?>
