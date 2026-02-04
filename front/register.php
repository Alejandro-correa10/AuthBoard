<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <link rel="stylesheet" href="css/global.css">
</head>
<body>

<div class="container">
  <h2 class="title">Registro de usuario</h2>

  <form id="registerForm">
    <div class="form-group">
      <input class="input" type="text" id="nombre" placeholder="Nombre" required>
    </div>

    <div class="form-group">
      <input class="input" type="text" id="apellido" placeholder="Apellido" required>
    </div>

    <div class="form-group">
      <input class="input" type="email" id="email" placeholder="Email" required>
    </div>

    <div class="form-group">
      <input class="input" type="password" id="password" placeholder="Contraseña" required>
    </div>

    <button class="btn" type="submit">Registrarse</button>
  </form>

  <p id="msg" class="msg"></p>

  <div class="link">
    ¿Ya tienes cuenta?
    <a href="login.php">Inicia sesión</a>
  </div>
</div>

<script>
const form = document.getElementById('registerForm');
const msg  = document.getElementById('msg');

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  msg.textContent = '';
  msg.className = 'msg';

  const nombre   = document.getElementById('nombre').value.trim();
  const apellido = document.getElementById('apellido').value.trim();
  const email    = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;

  const res = await fetch('/authBoard/register.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ nombre, apellido, email, password })
  });

  const data = await res.json();

  if (!data.ok) {
    msg.classList.add('error');
    msg.textContent = data.error;
    return;
  }

  msg.classList.add('success');
  msg.textContent = 'Usuario registrado correctamente ✅';

  setTimeout(() => {
    window.location.href = 'login.php';
  }, 1500);
});
</script>

</body>
</html>
