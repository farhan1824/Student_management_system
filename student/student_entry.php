<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Portal Entry</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      background: url("../640px-Dhaka_city_college.jpg") center/cover no-repeat;
    }

    /* Frosted glass overlay */
    .glass-bg {
      position: absolute;
      inset: 0;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      z-index: 0;
    }

    .entry-container {
      position: relative;
      z-index: 1;
      background: rgba(255, 255, 255, 0.85);
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      text-align: center;
      transition: all 0.3s ease;
    }

    .entry-container h2 {
      margin-bottom: 10px;
      color: #444;
    }

    .entry-container p {
      margin-bottom: 25px;
      color: #666;
    }

    .entry-button {
      display: block;
      width: 100%;
      margin: 12px 0;
      padding: 12px;
      font-size: 16px;
      font-weight: bold;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .entry-button.existing {
      background-color: #1E2457;
      color: #fff;
    }

    .entry-button.new {
      background-color: #650E0B;
      color: #fff;
    }

    .entry-button:hover {
      transform: translateY(-2px);
    }

    .entry-button.existing:hover {
      background-color: #fff;
      color: #1E2457;
      border: 2px solid #1E2457;
    }

    .entry-button.new:hover {
      background-color: #fff;
      color: #650E0B;
      border: 2px solid #650E0B;
    }
  </style>
</head>

<body>

  <!-- Glass overlay -->
  <div class="glass-bg"></div>

  <div class="entry-container">
    <h2>Welcome to the Student Portal</h2>
    <p>Please choose your option:</p>

    <button class="entry-button new" onclick="location.href='student_register.php'">
      I am a New Student
    </button>

    <button class="entry-button existing" onclick="location.href='student_login.php'">
      I am an Existing Student
    </button>
  </div>

</body>

</html>