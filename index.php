<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal</title>
    <style>
        * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }

        body {
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2C3E50  0%, #9B59B6  100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }
        /* body {
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        } */

        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        h2 {
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            margin: 10px;
            padding: 14px 28px;
            font-size: 18px;
            color: white;
            background: #00c6ff;
            background: linear-gradient(45deg, #FFCBA4 , #F4D03F );
            color: black;
            /* background: linear-gradient(45deg, #00c6ff, #0072ff); */
            border: none;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
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
        <h2>Welcome to the Login Portal</h2>
        <!-- <a href="student/student_login.php" class="btn">Student Login</a> -->
        <a href="student/student_entry.php" class="btn">Student Login</a>
        <a href="teacher/teacher_login.php" class="btn">Teacher Login</a>
    </div>
</body>
</html>
