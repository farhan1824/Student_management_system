<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Portal</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      height: 100vh;
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      transition: background 1s ease;
      position: relative;
    }

    #glassCanvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 999;
      display: none;
      pointer-events: none;
    }

    /* Index UI */
    .container {
      display: flex;
      width: 900px;
      height: 500px;
      background: #fff;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      border-radius: 12px;
      overflow: hidden;
      z-index: 1;
      transition: opacity 0.5s ease;
    }

    .container.hidden {
      opacity: 0;
      pointer-events: none;
    }

    .left-panel {
      flex: 1;
      background: url("./unnamed.webp") center/cover no-repeat;
    }

    .right-panel {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 30px;
      background: #fff;
    }

    .right-panel h2 {
      font-size: 24px;
      margin-bottom: 20px;
      background: linear-gradient(90deg, #1E2457, #650E0B);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-weight: bold;
    }

    .btn {
      display: block;
      width: 200px;
      padding: 12px;
      margin: 10px 0;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      transition: 0.3s;
    }

    .btn.student {
      background: #1E2457;
      color: white;
      text-decoration: none;
      text-align: center;
    }

    .btn.student:hover {
      background: #162041;
    }

    .btn.teacher {
      background: #650E0B;
      color: white;
      text-decoration: none;
      text-align: center;
    }

    .btn.teacher:hover {
      background: #520b09;
    }

    /* Admin UI */
    .admin-glass {
      position: absolute;
      inset: 0;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      z-index: 1;
      display: none;
    }

    .admin-ui {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      opacity: 0;
      pointer-events: none;
      z-index: 2;
      display: none;
    }

    .admin-ui h2 {
      font-size: 22px;
      margin-bottom: 20px;
      background: linear-gradient(90deg, #1E2457, #650E0B);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .admin-btn {
      background: #1E2457;
      color: white;
      padding: 12px 24px;
      border-radius: 6px;
      font-size: 16px;
      text-decoration: none;
      display: inline-block;
      transition: transform 0.3s ease;
    }

    .admin-btn:hover {
      transform: scale(1.05);
      background: #162041;
    }
  </style>
</head>

<body>

  <canvas id="glassCanvas"></canvas>

  <!-- Index Page UI -->
  <div class="container" id="mainContainer">
    <div class="left-panel"></div>
    <div class="right-panel">
      <h2>Dhaka City College Portal</h2>
      <a href="student/student_entry.php" class="btn student">Student Login</a>
      <a href="teacher/teacher_login.php" class="btn teacher">Teacher Login</a>
    </div>
  </div>

  <script>
    const secretCode = 'admin123';
    let typed = '';

    const canvas = document.getElementById('glassCanvas');
    const ctx = canvas.getContext('2d');
    const container = document.getElementById('mainContainer');
    let adminUI, adminGlass;

    function resize() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resize);
    resize();

    window.addEventListener('keydown', e => {
      typed += e.key;
      if (typed.length > secretCode.length) typed = typed.slice(-secretCode.length);
      if (typed === secretCode) {
        shatterThenRebuild();
      }
    });

    function shatterThenRebuild() {
      canvas.style.display = 'block';
      container.classList.add('hidden');

      const shards = [];
      const rows = 12,
        cols = 16;
      const w = canvas.width / cols,
        h = canvas.height / rows;

      for (let y = 0; y < rows; y++) {
        for (let x = 0; x < cols; x++) {
          const px = x * w,
            py = y * h;
          const vx = (Math.random() - 0.5) * 12;
          const vy = (Math.random() - 0.5) * 12;
          shards.push({
            sizeX: w,
            sizeY: h,
            cx: px + w / 2,
            cy: py + h / 2,
            vx,
            vy,
            startX: px + w / 2,
            startY: py + h / 2,
            endX: px + vx * 100,
            endY: py + vy * 100
          });
        }
      }

      let step = 0,
        total = 100;

      function animateExplode() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const t = step / total;
        shards.forEach(s => {
          const x = s.startX + (s.endX - s.startX) * t;
          const y = s.startY + (s.endY - s.startY) * t;
          const opacity = 1 - t;
          ctx.save();
          ctx.globalAlpha = opacity;
          ctx.fillStyle = '#fff';
          ctx.translate(x, y);
          ctx.fillRect(0, 0, s.sizeX, s.sizeY);
          ctx.restore();
        });

        step++;
        if (step <= total) {
          requestAnimationFrame(animateExplode);
        } else {
          document.body.style.background = "url('./640px-Dhaka_city_college.jpg') center/cover no-repeat";
          setTimeout(() => animateImplode(shards), 300);
        }
      }

      function animateImplode(shards) {
        step = 0;
        shards.forEach(s => {
          const tempX = s.startX;
          const tempY = s.startY;
          s.startX = s.endX;
          s.startY = s.endY;
          s.endX = tempX;
          s.endY = tempY;
        });

        function animate() {
          ctx.clearRect(0, 0, canvas.width, canvas.height);
          const t = step / total;
          shards.forEach(s => {
            const x = s.startX + (s.endX - s.startX) * t;
            const y = s.startY + (s.endY - s.startY) * t;
            const opacity = t;
            ctx.save();
            ctx.globalAlpha = opacity;
            ctx.fillStyle = '#fff';
            ctx.translate(x, y);
            ctx.fillRect(0, 0, s.sizeX, s.sizeY);
            ctx.restore();
          });

          step++;
          if (step <= total) {
            requestAnimationFrame(animate);
          } else {
            canvas.style.display = 'none';
            showAdminUI();
          }
        }
        animate();
      }

      animateExplode();
    }

    function showAdminUI() {
      // Glass overlay behind admin login
      adminGlass = document.createElement('div');
      adminGlass.className = 'admin-glass';
      adminGlass.style.display = 'block';
      document.body.appendChild(adminGlass);

      adminUI = document.createElement('div');
      adminUI.className = 'admin-ui';
      adminUI.style.display = 'block';
      adminUI.style.opacity = '0';
      adminUI.style.pointerEvents = 'none';

      adminUI.innerHTML = `
        <h2>Welcome, Admin</h2>
        <a href="./admin/admin_login.php" class="admin-btn">Admin Login</a>
      `;

      document.body.appendChild(adminUI);

      setTimeout(() => {
        adminUI.style.opacity = '1';
        adminUI.style.pointerEvents = 'auto';
      }, 100);
    }
  </script>

</body>

</html>