<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="css/global.css">
</head>
<body>

<div class="container">
  <h2 class="title">Iniciar sesión</h2>

  <form id="loginForm">
    <div class="form-group">
      <input class="input" type="email" id="email" placeholder="Email" required>
    </div>

    <div class="form-group">
      <input class="input" type="password" id="password" placeholder="Contraseña" required>
    </div>

    <button class="btn" type="submit">Entrar</button>
  </form>

  <p id="msg" class="msg"></p>

  <div class="link">
    ¿No tienes cuenta?
    <a href="register.php">Regístrate aquí</a>
  </div>
</div>

<script>
const form = document.getElementById('loginForm');
const msg  = document.getElementById('msg');

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  msg.textContent = '';
  msg.className = 'msg';

  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;

  const res = await fetch('/authBoard/login.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  });

  const data = await res.json();

  if (!data.ok) {
    msg.classList.add('error');
    msg.textContent = data.error;
    return;
  }

  localStorage.setItem('token', data.token);
  localStorage.setItem('expires_at', data.expires_at);

  // (opcional) guardar datos del usuario para mostrar en index
  if (data.user) localStorage.setItem('user', JSON.stringify(data.user));

  alert('Sesión iniciada correctamente ✅');
  window.location.href = 'index.php';
});
</script>

</body>
</html>
