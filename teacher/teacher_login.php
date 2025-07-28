<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Login</title>
    <style>
        body {
            background: #141e30;
            background: linear-gradient(to right, #243b55, #141e30);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 15px;
            backdrop-filter: blur(8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        h2 {
            margin-bottom: 20px;
        }

        input[type="text"], input[type="password"] {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            outline: none;
        }

        button {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        .error {
            color: #ff8080;
            margin-top: 10px;
        }

        a {
            color: #9ecfff;
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
    <h2>Teacher Login</h2>
    <form method="POST" action="">
        <input type="text" name="teacher_number" placeholder="Teacher Number" ><br>
        <input type="password" name="password" placeholder="Password" ><br>
        <button type="submit">Login</button>
    </form>
    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <a href="../index.php">← Back to Home</a>
</div>

</body>
</html>


<?php
require_once '../db/db.php'; // Include database connection
require_once '../error.php'; // Include error handling
require_once '../QueryModel/basicfunctions.php'; // Include basic functions
session_start();

// Redirect if already logged in
if (isset($_SESSION['teacher_number'])) {
    header("Location: teacher_dashboard.php");
    exit();
}
$error = '';
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $teacherNumber = $_POST['teacher_number'];
    $passwordInput = $_POST['password'];

    // Escape input to prevent SQL injection
    $teacherNumber = $conn->real_escape_string($teacherNumber);
    $passwordInput = $conn->real_escape_string($passwordInput);
    if(isInputEmpty($teacherNumber, $passwordInput)) {
        header("Location: teacher_login.php?error=empty_fields");
        exit();

    }
    else{
 // Query
    $sql = "SELECT * FROM teach_details WHERE teach_num = '$teacherNumber' AND pwd = '$passwordInput'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        // Valid credentials, set session
        $_SESSION['teacher_number'] = $teacherNumber;

        // Redirect to dashboard
        header("Location: teacher_dashboard.php");
        exit();
    } else {
       header("Location: ../error.php?error=invalid_teacher_login");
        exit();
    }
    }
    
   
}
?>