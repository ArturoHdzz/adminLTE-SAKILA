<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sakila DB | Dashboard</title>

  <!-- Preload critical assets -->
  <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style">
  <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">
  <link rel="preload" href="https://code.jquery.com/jquery-3.6.0.min.js" as="script">

  <!-- Google Font: Source Sans Pro with display swap for better performance -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback&display=swap">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    .spinner-border {
      width: 1.5rem;
      height: 1.5rem;
      border-width: 0.2em;
    }
  </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
  <div class="card shadow-lg p-4 w-25">
    <h2 class="text-center mb-3">Ingresa tu correo</h2>
    <form id="emailForm">
      <div class="mb-3">
        <input type="email" class="form-control" id="email" placeholder="Correo electrónico" required name="email">
      </div>
      <button type="submit" class="btn btn-primary w-100" id="submitButton">
        Enviar
        <span id="submitSpinner" class="spinner-border text-light" style="display:none;"></span>
      </button>
    </form>
  </div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
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

    document.getElementById('emailForm').addEventListener('submit', function(event) {
      event.preventDefault();

      const email = document.querySelector('input[name="email"]').value;

      const loginData = {
        email: email
      };

      showLoading('submitButton', 'submitSpinner');  // Mostrar el spinner

      fetch('http://64.23.250.130/api/login/', {
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

        if (data.message && data.message === 'OTP enviado') {
          alert('Se ha enviado un código de verificación a tu correo electrónico.');
          localStorage.setItem('email', email);
          window.location.href = 'login.php';
        } else {
          alert('Error en la solicitud: ' + (data.error || 'No se pudo realizar la acción'));
        }
      })
      .catch(error => {
        hideLoading('submitButton', 'submitSpinner');  // Ocultar el spinner
        alert('Hubo un error en la solicitud: ' + error.message);
      });
    });
  </script>

  <script>
    function checkLocalStorageAndRedirect() {
      const userData = localStorage.getItem('email');
      if (userData) {
        window.location.href = "/login.php";
      }
    }
    document.addEventListener("DOMContentLoaded", checkLocalStorageAndRedirect);
  </script>
</body>
</html>
