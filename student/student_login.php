<?php
session_start();
if (isset($_SESSION['student_roll'])) {
    header("Location: student_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <style>
        body {
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.08);
            padding: 40px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        input[type="text"] {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
        }

        button {
            background: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        .error {
            color: #ff8080;
            margin-top: 10px;
        }

        a {
            color: #90d4ff;
            display: block;
            margin-top: 15px;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Student Login</h2>
    <form method="POST" action="">
        <input type="text" name="roll" placeholder="Enter reg Number" required><br>
        <button type="submit">Login</button>
    </form>
    <a href="../index.php">← Back to Home</a>
</div>
</body>
</html>
<?php
require_once '../db/db.php'; // Include database connection
require_once '../error.php'; // Include error handling


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll = $conn->real_escape_string($_POST['roll']);

    $sql = "SELECT * FROM students WHERE roll = '$roll'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $_SESSION['student_roll'] = $roll;
        $_SESSION['first_login']=false; // Set first login to false
        header("Location: student_dashboard.php");
        exit();
    } else {
        header("Location: ../error.php?error=invalid_student_login");
        exit();
    }
}
?>