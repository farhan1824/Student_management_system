<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Portal</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #2C3E50  0%, #9B59B6  100%);
      display: flex;
      justify-content: center;
      align-items: center;
      color: #ffffff;
    }

    .container {
      text-align: center;
      background: rgba(255, 255, 255, 0.1);
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      backdrop-filter: blur(10px);
      position: relative;
      transition: all 0.4s ease;
    }

    h2 {
      margin-bottom: 30px;
      font-size: 32px;
      font-weight: 600;
      transition: opacity 0.4s ease;
    }

    .btn {
      display: inline-block;
      margin: 10px;
      padding: 14px 28px;
      font-size: 18px;
      color: black;
      background: linear-gradient(45deg, #FFCBA4 , #F4D03F);
      border: none;
      border-radius: 30px;
      cursor: pointer;
      text-decoration: none;
      opacity: 1;
      transition: opacity 0.5s ease, transform 0.3s ease;
    }

    .btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    .fade-out {
      opacity: 0 !important;
      pointer-events: none;
    }

    .admin-reveal {
      opacity: 0;
      animation: fadeIn 0.7s ease forwards;
      margin-top: 0;
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
      }
    }

    @media (max-width: 500px) {
      .btn {
        width: 100%;
        margin: 10px 0;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2 id="portalTitle">Welcome to the Login Portal</h2>
    <div id="buttonGroup">
      <a href="student/student_entry.php" class="btn student">Student Login</a>
      <a href="teacher/teacher_login.php" class="btn teacher">Teacher Login</a>
    </div>
  </div>
</body>
</html>
  <script>
    const secretCode = 'admin123';  // ← Set your secret code here
    let typed = '';

    window.addEventListener('keydown', (e) => {
      typed += e.key;
      if (typed.length > secretCode.length) {
        typed = typed.slice(-secretCode.length);
      }

      if (typed === secretCode) {
        showAdminLogin();
      }
    });

    function showAdminLogin() {
      // Fade out existing buttons
      const btnGroup = document.getElementById('buttonGroup');
      const buttons = btnGroup.querySelectorAll('.btn');
      buttons.forEach(btn => btn.classList.add('fade-out'));

      // Create admin button
      const adminBtn = document.createElement('a');
      adminBtn.href = 'admin/admin_login.php';
      adminBtn.className = 'btn admin-reveal';
      adminBtn.textContent = 'Admin Login';

      // After a short delay to let fade-out complete
      setTimeout(() => {
        btnGroup.remove();
        document.querySelector('.container').appendChild(adminBtn);
      }, 600);
    }
  </script>