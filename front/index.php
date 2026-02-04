<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/global.css">
</head>
<body class="dashboard">

<div class="dashboard-shell">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="left">
      <div class="appmark"></div>
      <div>
        <h1>Panel de usuario</h1>
        <div class="sub" id="welcomeText">Cargando usuario...</div>
      </div>
    </div>

    <div class="pill">
      <span class="dot"></span>
      <span>Sesión activa • <b id="timeLeft">--</b></span>
    </div>
  </div>

  <div class="layout">
    <!-- SIDEBAR -->
    <aside class="sidebar-pro">
      <div style="font-weight:700; color:#111827;">Menú</div>
      <div class="nav-pro">
        <button class="active" type="button">🏠 Dashboard</button>
        <button type="button" onclick="alert('Aquí puedes poner tu página de perfil');">👤 Perfil</button>
        <button type="button" onclick="alert('Aquí puedes poner tu configuración');">⚙️ Configuración</button>
        <button type="button" onclick="alert('Aquí puedes poner tu actividad');">🧾 Actividad</button>
      </div>

      <div class="notice">
        Tip: si vas a estar más tiempo, presiona <b>Mantener sesión</b>.
      </div>
    </aside>

    <!-- MAIN -->
    <main class="main-pro">

      <section class="cards-grid">
        <!-- PERFIL -->
        <div class="card-pro">
          <h2>👤 Información del usuario</h2>
          <div class="kv-pro">
            <div>Nombre: <span id="nombre">--</span></div>
            <div>Apellido: <span id="apellido">--</span></div>
            <div>Email: <span id="email">--</span></div>
            <div>ID: <span id="userId">--</span></div>
          </div>
        </div>

        <!-- SESIÓN -->
        <div class="card-pro">
          <h2>⏳ Sesión</h2>
          <div class="kv-pro">
            <div>Expira en: <span id="timeLeft2">--</span></div>
            <div>Fecha de expiración: <span id="expiresAtText">--</span></div>
          </div>

          <div class="progress-wrap">
            <div class="progress-bar"><div id="progressBar"></div></div>
          </div>

          <div class="actions">
            <button class="btn outline small" id="keepAlive" type="button">Mantener sesión</button>
            <button class="btn danger small" id="logout" type="button">Cerrar sesión</button>
          </div>

          <p id="sessionMsg" class="msg" style="text-align:left;"></p>
        </div>
      </section>

      <!-- ACCIONES -->
      <section class="card-pro">
        <h2>🚀 Acciones rápidas</h2>
        <div class="actions">
          <button class="btn small" type="button" onclick="alert('Módulo: Mis cursos');">Mis cursos</button>
          <button class="btn small" type="button" onclick="alert('Módulo: Reportes');">Reportes</button>
          <button class="btn small" type="button" onclick="alert('Módulo: Mensajes');">Mensajes</button>
          <button class="btn small" type="button" onclick="alert('Módulo: Calendario');">Calendario</button>
        </div>
        <div class="notice">
          Cambia estos botones por funcionalidades reales según tu proyecto.
        </div>
      </section>

    </main>
  </div>
</div>

<script>
function clearAndGoLogin() {
  localStorage.clear();
  window.location.href = 'login.php';
}

function formatTime(seconds) {
  const m = Math.floor(seconds / 60);
  const s = seconds % 60;
  return `${m}m ${String(s).padStart(2,'0')}s`;
}

function formatDateTimeFromUnix(unixSeconds) {
  const d = new Date(unixSeconds * 1000);
  return d.toLocaleString();
}

let timer = null;
let totalSessionSeconds = 15 * 60;

async function loadDashboard() {
  const token = localStorage.getItem('token');
  const expiresAt = Number(localStorage.getItem('expires_at') || 0);
  const now = Math.floor(Date.now() / 1000);

  if (!token || !expiresAt || expiresAt < now) {
    clearAndGoLogin();
    return;
  }

  const res = await fetch('/authBoard/me.php', {
    headers: { 'Authorization': 'Bearer ' + token }
  });

  const data = await res.json();
  if (!data.ok) {
    clearAndGoLogin();
    return;
  }

  document.getElementById('welcomeText').textContent =
    `Hola, ${data.user.nombre} ${data.user.apellido}. Bienvenido/a a tu panel.`;

  document.getElementById('nombre').textContent = data.user.nombre;
  document.getElementById('apellido').textContent = data.user.apellido;
  document.getElementById('email').textContent = data.user.email;
  document.getElementById('userId').textContent = data.user.id;

  const issuedAt = data.session.issued_at;
  const expiresAtServer = data.session.expires_at;
  let secondsLeft = data.session.seconds_left;

  totalSessionSeconds = Math.max(1, expiresAtServer - issuedAt);

  localStorage.setItem('expires_at', String(expiresAtServer));

  document.getElementById('expiresAtText').textContent = formatDateTimeFromUnix(expiresAtServer);

  const timeElA = document.getElementById('timeLeft');
  const timeElB = document.getElementById('timeLeft2');
  const bar = document.getElementById('progressBar');

  if (timer) clearInterval(timer);

  function render() {
    const txt = formatTime(secondsLeft);
    timeElA.textContent = txt;
    timeElB.textContent = txt;

    const pctRemaining = Math.max(0, Math.min(1, secondsLeft / totalSessionSeconds));
    bar.style.width = `${pctRemaining * 100}%`;

    if (secondsLeft <= 0) {
      clearInterval(timer);
      clearAndGoLogin();
    }
  }

  render();
  timer = setInterval(() => {
    secondsLeft--;
    render();
  }, 1000);
}

document.getElementById('logout').addEventListener('click', async () => {
  const token = localStorage.getItem('token');
  if (token) {
    await fetch('/authBoard/logout.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ token })
    });
  }
  clearAndGoLogin();
});

document.getElementById('keepAlive').addEventListener('click', async () => {
  const sessionMsg = document.getElementById('sessionMsg');
  sessionMsg.textContent = '';
  sessionMsg.className = 'msg';

  const token = localStorage.getItem('token');
  if (!token) return clearAndGoLogin();

  const res = await fetch('/authBoard/refresh.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ token })
  });

  const data = await res.json();

  if (!data.ok) {
    sessionMsg.classList.add('error');
    sessionMsg.textContent = data.error || 'No se pudo renovar la sesión';
    return clearAndGoLogin();
  }

  localStorage.setItem('token', data.token);
  localStorage.setItem('expires_at', data.expires_at);

  sessionMsg.classList.add('success');
  sessionMsg.textContent = '✅ Sesión renovada por 15 minutos';

  loadDashboard();
});

loadDashboard();
</script>

</body>
</html>
