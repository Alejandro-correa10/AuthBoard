# 🔐 authBoard

Sistema de autenticación completo desarrollado en **PHP + MySQL**, con gestión de sesiones mediante **tokens con expiración**, renovación de sesión, dashboard moderno y arquitectura separada **Frontend / API**.

> Proyecto pensado para portafolio profesional: demuestra backend, frontend, seguridad básica y UX.

---

## ✨ Características principales

* Registro de usuarios con datos reales (nombre, apellido, email)
* Hash seguro de contraseñas (`password_hash`)
* Login con generación de token seguro (`random_bytes`)
* Sesiones almacenadas en base de datos (MySQL)
* Expiración automática de sesión
* Renovación de sesión (refresh token)
* Logout que invalida el token
* Validación de sesión desde backend
* Dashboard moderno y responsive
* Frontend desacoplado del backend (API REST)

---

## 🧱 Arquitectura del proyecto

```
front  →  api  →  database
```

* **Front**: HTML, CSS, JavaScript (fetch API)
* **API**: PHP (PDO, JSON, REST)
* **DB**: MySQL

---

## 📂 Estructura del proyecto

```
/api
  ├── db.php
  ├── helpers.php
  ├── register.php
  ├── login.php
  ├── logout.php
  ├── refresh.php
  ├── me.php

/front
  ├── login.php
  ├── register.php
  ├── index.php
  └── css
      └── global.css

/database
  └── auth_app.sql
```

---

## 🔐 Flujo de autenticación

1. Usuario se registra (POST `/authBoard/register.php`)
2. Usuario inicia sesión (POST `/authBoard/login.php`)
3. Se genera token con expiración (15 min)
4. Token se guarda en `localStorage`
5. Dashboard valida token con backend (`/authBoard/me.php`)
6. Usuario puede renovar sesión (`/authBoard/refresh.php`)
7. Logout elimina sesión (`/authBoard/logout.php`)

---

## 🖥️ Dashboard

El dashboard muestra:

* Datos reales del usuario
* Estado de sesión en tiempo real
* Barra de progreso de expiración
* Botón para mantener sesión activa
* Acciones rápidas (simuladas)

Diseñado para ser escalable a:

* Plataformas educativas
* Sistemas administrativos
* Paneles internos

---

## 🗄️ Base de datos

### Tabla `users`

* id
* nombre
* apellido
* email (único)
* password_hash
* status
* created_at

### Tabla `sessions`

* token
* user_id
* issued_at
* expires_at

---

## 🚀 Cómo ejecutar el proyecto

1. Clonar repositorio
2. Importar el archivo SQL en MySQL
3. Configurar credenciales en `api/db.php`
4. Ejecutar en servidor local (XAMPP / Laragon)
5. Acceder a `/front/login.php`

---

## 🛡️ Seguridad

* Contraseñas nunca se almacenan en texto plano
* Tokens no predecibles
* Sesiones con expiración forzada
* Validación en backend (no solo frontend)
* Logout invalida token

---

## 🎯 Objetivo del proyecto

Demostrar capacidad para:

* Diseñar sistemas de autenticación
* Pensar arquitectura escalable
* Integrar backend + frontend
* Cuidar experiencia de usuario
* Preparar proyectos para producción

---

## 📌 Próximas mejoras (ideas)

* Roles de usuario
* Edición de perfil
* Tema oscuro
* Auditoría de sesiones
* Protección CSRF

---

## 👤 Autor

Proyecto desarrollado como parte de un **portafolio profesional**.

---

Si estás revisando este repositorio como reclutador o cliente: este proyecto representa una base sólida para sistemas reales.

