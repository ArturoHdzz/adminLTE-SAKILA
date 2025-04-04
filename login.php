<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sakila DB | Login</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>
    .spinner-border {
      width: 1.5rem;
      height: 1.5rem;
      border-width: 0.2em;
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>Sakila</b>DB</a>
  </div>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Inicia sesión para comenzar tu sesión</p>

      <form id="loginForm">
        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="Correo electrónico" name="email" id="emailInput" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="OTP code" name="otp" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Contraseña" name="password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block" id="submitButton">
              Entrar
              <span id="submitSpinner" class="spinner-border text-light" style="display:none;"></span>
            </button>
          </div>
        </div>
        <!-- Nuevo botón para otra API -->
        <div class="col-4 mt-2">
          <button type="button" id="resendButton" class="btn btn-success btn-block">
            Reenviar código
            <span id="resendSpinner" class="spinner-border text-light" style="display:none;"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="plugins/jquery/jquery.min.js" defer></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js" defer></script>
<script src="dist/js/adminlte.min.js" defer></script>

<script>
  const storedEmail = localStorage.getItem('email');

  if (storedEmail) {
    document.getElementById('emailInput').value = storedEmail;
  } else {
    window.location.href = 'code.php';
  }

  // Función para mostrar el spinner y deshabilitar el botón
  function showLoading(buttonId, spinnerId) {
    document.getElementById(buttonId).disabled = true;
    document.getElementById(spinnerId).style.display = 'inline-block';
  }

  // Función para ocultar el spinner y habilitar el botón
  function hideLoading(buttonId, spinnerId) {
    document.getElementById(buttonId).disabled = false;
    document.getElementById(spinnerId).style.display = 'none';
  }

  document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const loginData = {
      email: document.querySelector('input[name="email"]').value,
      otp: document.querySelector('input[name="otp"]').value,
      password: document.querySelector('input[name="password"]').value
    };

    showLoading('submitButton', 'submitSpinner');  // Mostrar el spinner

    fetch('http://64.23.250.130/api/verify-otp/', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(loginData)
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error en la solicitud, código de estado: ' + response.status);
      }
      return response.json();
    })
    .then(data => {
      hideLoading('submitButton', 'submitSpinner');  // Ocultar el spinner
      if (data.user && data.access && data.refresh) {
        localStorage.setItem('user_data', JSON.stringify(data));

        document.cookie = `access_token=${data.access}; path=/; SameSite=Lax`;

        window.location.href = 'dashboard.php';
      } else {
        alert('Error en la solicitud: ' + (data.error || 'No se pudo realizar la acción'));
      }
    })
    .catch(error => {
      hideLoading('submitButton', 'submitSpinner');  // Ocultar el spinner
      alert('Hubo un error en la solicitud: ' + error.message);
    });
  });

  document.getElementById('resendButton').addEventListener('click', function(event) {
    event.preventDefault();

    const additionalData = {
      email: document.querySelector('input[name="email"]').value,
    };

    showLoading('resendButton', 'resendSpinner');  // Mostrar el spinner

    fetch('http://64.23.250.130/api/login/', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(additionalData)
    })
    .then(response => response.json())
    .then(data => {
      hideLoading('resendButton', 'resendSpinner');  // Ocultar el spinner
      console.log(data.message);

      if (data.message === 'OTP enviado') {
        alert('Se ha enviado un nuevo código de verificación a tu correo electrónico.');
      } else {
        alert('Error en la solicitud: ' + (data.error || 'No se pudo realizar la acción'));
      }
    })
    .catch(error => {
      hideLoading('resendButton', 'resendSpinner');  // Ocultar el spinner
      alert('Hubo un error en la solicitud: ' + error.message);
    });
  });
</script>

</body>
</html>
