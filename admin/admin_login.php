<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      height: 100vh;
      background: linear-gradient(135deg, #D2D3BE, #000000);
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-card {
      background: #D2D3BE;
      color: #000;
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 400px;
      transition: all 0.3s ease;
    }

    .login-card h2 {
      margin-bottom: 30px;
      font-size: 28px;
      text-align: center;
      font-weight: 600;
      letter-spacing: 1px;
    }

    .input-group {
      margin-bottom: 20px;
    }

    .input-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }

    .input-group input {
      width: 100%;
      padding: 12px 15px;
      border: none;
      border-radius: 10px;
      background: #f0f0ea;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .input-group input:focus {
      outline: none;
      background: #e6e6db;
    }

    .login-btn {
      width: 100%;
      padding: 14px;
      background: #000;
      color: #D2D3BE;
      font-size: 16px;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .login-btn:hover {
      transform: scale(1.03);
      background: #111;
    }

    .footer-note {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: #444;
    }
  </style>
</head>

<body>

  <div class="login-card">
    <h2>Admin Panel Login</h2>
    <form action="admin_login.php" method="POST">
      <div class="input-group">
        <label for="username_or_email">Username or Email</label>
        <input type="text" id="username_or_email" name="username_or_email" required />
      </div>
      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>
      <button type="submit" class="login-btn">Login</button>
    </form>

    <div class="footer-note">© 2025 Secure Access</div>
  </div>

</body>

</html>
<?php
session_start();
require_once "../error.php"; // Include error handling
require_once '../db/db.php'; // DB connection
require_once '../QueryModel/basicfunctions.php'; // has isInputEmpty()

if (isset($_SESSION['admin_username'])) {
  header("Location: admin_dashboard.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = trim($_POST['username_or_email']);
  $passwordInput = trim($_POST['password']);


  if (isInputEmpty($input, $passwordInput)) {
    header("Location: admin_login.php?error=admin_empty_fields");
    exit();
  }

  $input = $conn->real_escape_string($input);

  $stmt = $conn->prepare("SELECT username, password FROM admin_info WHERE username = ? OR email = ?");
  $stmt->bind_param("ss", $input, $input);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($admin = $result->fetch_assoc()) {
    if (password_verify(trim($_POST['password']), $admin['password'])) {
      $_SESSION['admin_username'] = $admin['username'];
      header("Location: admin_dashboard.php");
      exit();
    } else {
      header("Location: admin_login.php?error=admin_password_mismatch");
      exit();
    }
  } else {
    header("Location: admin_login.php?error=admin_not_found");
    exit();
  }
}
?>