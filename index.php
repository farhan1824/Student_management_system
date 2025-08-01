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
      background: linear-gradient(135deg, #2C3E50 0%, #9B59B6 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      color: #fff;
      transition: background 1s ease;
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

    .container {
      text-align: center;
      padding: 40px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      z-index: 1;
      transition: opacity 0.5s ease;
    }

    .container.hidden {
      opacity: 0;
      pointer-events: none;
    }

    .btn {
      display: inline-block;
      margin: 10px;
      padding: 14px 28px;
      font-size: 18px;
      color: black;
      background: linear-gradient(45deg, #FFCBA4, #F4D03F);
      border: none;
      border-radius: 30px;
      text-decoration: none;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .btn:hover {
      transform: scale(1.05);
    }

    .admin-ui {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: #E7E8D1;
      color: black;
      padding: 40px;
      border-radius: 20px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      opacity: 0;
      pointer-events: none;
      z-index: 1000;
      display: none;
    }

    .admin-btn {
      background: black;
      color: #E7E8D1;
      padding: 14px 28px;
      border-radius: 30px;
      font-size: 18px;
      text-decoration: none;
      display: inline-block;
      margin-top: 20px;
      transition: transform 0.3s ease;
    }

    .admin-btn:hover {
      transform: scale(1.05);
    }
  </style>
</head>

<body>

  <canvas id="glassCanvas"></canvas>

  <div class="container" id="mainContainer">
    <h2>Welcome to the Login Portal</h2>
    <div id="buttonGroup">
      <a href="student/student_entry.php" class="btn">Student Login</a>
      <a href="teacher/teacher_login.php" class="btn">Teacher Login</a>
    </div>
  </div>

  <script>
    const secretCode = 'admin123';
    let typed = '';

    const canvas = document.getElementById('glassCanvas');
    const ctx = canvas.getContext('2d');
    const container = document.getElementById('mainContainer');
    let adminUI; // Will be created dynamically

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
          document.body.style.background = 'linear-gradient(135deg, #E7E8D1 0%, #000 100%)';
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
            showAdminUI(); // call the dynamic builder
          }
        }
        animate();
      }

      animateExplode();
    }

    function showAdminUI() {
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

      // Trigger fade-in
      setTimeout(() => {
        adminUI.style.opacity = '1';
        adminUI.style.pointerEvents = 'auto';
      }, 100);
    }
  </script>

</body>

</html>