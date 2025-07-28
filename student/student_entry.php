<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Portal Entry</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #2C3E50  0%, #9B59B6  100%);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .entry-container {
      background-color: white;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 360px;
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
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;
    }

    .entry-button.existing {
      background-color: #F4D03F;
      color: #000;
    }

    .entry-button.new {
      background-color: #FFCBA4;
      color: #000;
      border: 2px solid #F4D03F;
    }

    .entry-button:hover {
      transform: translateY(-2px);
    }

    .entry-button.existing:hover {
      background-color: #e1bc2e;
    }

    .entry-button.new:hover {
      background-color: #f9b887;
    }
  </style>
</head>
<body>

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
