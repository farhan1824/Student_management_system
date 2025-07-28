<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New Student Registration</title>
  <style>
    * {
      box-sizing: border-box;
      padding: 0;
      margin: 0;
    }

    body {
      background: #FFE5B4;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    form {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      width: 400px;
    }

    h2 {
      text-align: center;
      color: #F4A460;
      margin-bottom: 25px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }

    input, select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      background: #fff8f0;
    }

    input[type="file"] {
      padding: 5px;
    }

    button {
      background: #F4D03F;
      color: black;
      border: none;
      padding: 12px;
      width: 100%;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      background: #eacb36;
    }
  </style>
</head>
<body>

<form method="POST" action="" enctype="multipart/form-data">
  <h2>New Student Registration</h2>

  <div class="form-group">
    <label>First Name</label>
    <input type="text" name="first_name" required>
  </div>

  <div class="form-group">
    <label>Middle Name (Optional)</label>
    <input type="text" name="middle_name">
  </div>

  <div class="form-group">
    <label>Last Name</label>
    <input type="text" name="last_name" required>
  </div>

  <div class="form-group">
    <label>Date of Birth</label>
    <input type="date" name="dob" required>
  </div>

  <div class="form-group">
    <label>Gender</label>
    <select name="gender" required>
      <option value="">Select Gender</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
      <option value="Other">Other / Prefer not to say</option>
    </select>
  </div>

  <div class="form-group">
    <label>Nationality</label>
    <input type="text" name="nationality" required>
  </div>

  <div class="form-group">
    <label>Country of Birth</label>
    <input type="text" name="birth_country" required>
  </div>

  <div class="form-group">
    <label>Upload Photo</label>
    <input type="file" name="photo" accept="image/*" required>
  </div>

  <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" required>
  </div>
  <button type="submit">Register</button>
</form>

</body>
</html>
<?php
session_start();
require_once '../db/db.php'; // Include database connection
require_once '../error.php'; // Include error handling
require_once "../QueryModel/basicfunctions.php"; // Include basic functions

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Sanitize inputs
//     $firstName = trim($_POST['first_name'] );
//     $middleName = trim($_POST['middle_name'] ?? '');
//     $lastName = trim($_POST['last_name']);
//     $dob = $_POST['dob'];
//     $gender = $_POST['gender'] ;
//     $nationality = trim($_POST['nationality']);
//     $birthCountry = trim($_POST['birth_country']);
//     $email = trim($_POST['email']);



//  allCheckAndInsertData($firstName, $middleName, $lastName, $dob, $gender, $nationality, $birthCountry, $email, $conn);

//     $photoFilename = null;

//     if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
//     $photo = $_FILES['photo'];
//     $uploadDir = '../uploads/';
//     $photoFilename = uniqid() . '-' . basename($photo['name']);
//     $uploadFile = $uploadDir . $photoFilename;

//     $check = getimagesize($photo['tmp_name']);
//     if ($check !== false && move_uploaded_file($photo['tmp_name'], $uploadFile)) {
//         // File is valid
//     } else {
//         header("Location: student_register.php?error=photo_upload_failed");
//         exit();
//     }
//     } 

//  allCheckAndInsertData($firstName, $middleName, $lastName,$photoFilename, $dob, $gender, $nationality, $birthCountry, $email, $conn);

// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $firstName = trim($_POST['first_name']);
    $middleName = trim($_POST['middle_name'] ?? '');
    $lastName = trim($_POST['last_name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $nationality = trim($_POST['nationality']);
    $birthCountry = trim($_POST['birth_country']);
    $email = trim($_POST['email']);

    // Handle file upload first
    // if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    //     $photo = $_FILES['photo'];
    //     $uploadDir = '../uploads/';
    //     $photoFilename = uniqid() . '-' . basename($photo['name']);
    //     $uploadFile = $uploadDir . $photoFilename;

    //     $check = getimagesize($photo['tmp_name']);
    //     if ($check === false || !move_uploaded_file($photo['tmp_name'], $uploadFile)) {
    //         header("Location: student_register.php?error=photo_upload_failed");
    //         exit();
    //     }
    // } else {
    //     // Photo required
    //     header("Location: student_register.php?error=photo_required");
    //     exit();
    // }

    // Now call your function with correct parameters and order
    // allCheckAndInsertData($firstName, $middleName, $lastName, $photoFilename, $dob, $gender, $nationality, $birthCountry, $email, $conn);
    allCheckAndInsertData($firstName, $middleName, $lastName, $dob, $gender, $nationality, $birthCountry, $email, $conn);
}

?>