<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sakila DB | Registro</title>

  <!-- Preload critical assets -->
  <link rel="preload" href="dist/css/adminlte.min.css" as="style">
  <link rel="preload" href="plugins/fontawesome-free/css/all.min.css" as="style">
  <link rel="preload" href="plugins/jquery/jquery.min.js" as="script">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>
    /* Estilos personalizados */
    .register-box {
      width: 450px; /* Aumentar el ancho del formulario */
      margin: 0 auto;
    }
    .card-body {
      padding: 30px;
    }
    .register-logo a {
      font-size: 2rem;
      font-weight: bold;
      color: #007bff;
    }
    .register-box .form-control {
      border-radius: 0.375rem; /* Bordes redondeados en los campos */
    }
    .input-group-text {
      background-color: #f8f9fa;
    }
    .btn-block {
      border-radius: 0.375rem; /* Bordes redondeados en el botón */
      padding: 10px 15px;
    }
    .text-center a {
      color: #007bff;
    }
    .text-center a:hover {
      text-decoration: underline;
    }
    .input-group {
      margin-bottom: 1.5rem;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <?php include '_navbar.php'; ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php include '_sidebar.php'; ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Main content -->
        <section class="content">
          <div class="register-box">
            <div class="register-logo">
              <a href="#"><b> Registro en Sakila</b>DB</a>
            </div>

            <div class="card">
              <div class="card-body register-card-body">
                <p class="login-box-msg">Regístrate para crear una cuenta</p>

                <form id="registerForm">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Nombre" name="first_name" required>
                    <div class="input-group-append">
                      <div class="input-group-text">
                        <span class="fas fa-user"></span>
                      </div>
                    </div>
                  </div>
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Apellido" name="last_name" required>
                    <div class="input-group-append">
                      <div class="input-group-text">
                        <span class="fas fa-user"></span>
                      </div>
                    </div>
                  </div>
                  <div class="input-group">
                    <input type="email" class="form-control" placeholder="Correo electrónico" name="email" required>
                    <div class="input-group-append">
                      <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                      </div>
                    </div>
                  </div>
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Nombre de usuario" name="username" required>
                    <div class="input-group-append">
                      <div class="input-group-text">
                        <span class="fas fa-user-circle"></span>
                      </div>
                    </div>
                  </div>
                  <div class="input-group">
                    <input type="password" class="form-control" placeholder="Contraseña" name="password" required>
                    <div class="input-group-append">
                      <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                      </div>
                    </div>
                  </div>
                  <div class="input-group">
                    <input type="password" class="form-control" placeholder="Repite la contraseña" name="confirm_password" required>
                    <div class="input-group-append">
                      <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                      </div>
                    </div>
                  </div>

                  <!-- Campo para Dirección (número) -->
                  <div class="input-group">
                    <input type="number" class="form-control" placeholder="Número de Dirección" name="address" required>
                    <div class="input-group-append">
                      <div class="input-group-text">
                        <span class="fas fa-home"></span>
                      </div>
                    </div>
                  </div>

                  <!-- Campo para seleccionar el Rol -->
                  <div class="input-group">
                    <select class="form-control" name="role" required>
                      <option value="">Seleccionar rol</option>
                      <option value="1">Administrador</option>
                      <option value="3">Invitado</option>
                      <option value="2">Cliente</option>
                    </select>
                    <div class="input-group-append">
                      <div class="input-group-text">
                        <span class="fas fa-user-tag"></span>
                      </div>
                    </div>
                  </div>

                  <!-- Campo para seleccionar la Tienda (Store) -->
                  <div class="input-group">
                    <select class="form-control" name="store" required>
                      <option value="">Seleccionar tienda</option>
                      <option value="1">Tienda 1</option>
                      <option value="2">Tienda 2</option>
                      <option value="3">Tienda 3</option>
                    </select>
                    <div class="input-group-append">
                      <div class="input-group-text">
                        <span class="fas fa-store"></span>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-8">
                      <div class="icheck-primary">
                        <input type="checkbox" id="agreeTerms" name="terms" value="agree" required>
                        <label for="agreeTerms">
                         Acepto los <a href="#">términos y condiciones</a>
                        </label>
                      </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                      <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
                    </div>
                    <!-- /.col -->
                  </div>
                </form>

              </div>
              <!-- /.form-box -->
            </div><!-- /.card -->
          </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

<!-- REQUIRED SCRIPTS -->
<script src="plugins/jquery/jquery.min.js" defer></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js" defer></script>
<script src="dist/js/adminlte.min.js" defer></script>

<script>
  document.getElementById('registerForm').addEventListener('submit', function(event) {
    event.preventDefault(); 

    const firstName = document.querySelector('input[name="first_name"]').value;
    const lastName = document.querySelector('input[name="last_name"]').value;
    const email = document.querySelector('input[name="email"]').value;
    const username = document.querySelector('input[name="username"]').value;
    const password = document.querySelector('input[name="password"]').value;
    const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
    const address = parseInt(document.querySelector('input[name="address"]').value); // Convertir a int
    const role = parseInt(document.querySelector('select[name="role"]').value); // Convertir a int
    const store = parseInt(document.querySelector('select[name="store"]').value); // Convertir a int

    if (password !== confirmPassword) {
      alert('Las contraseñas no coinciden.');
      return;
    }

    const userData = {
      first_name: firstName,
      last_name: lastName,
      email: email,
      username: username,
      password: password,
      address_id: address,
      role_id: role,
      store_id: store
    };

    fetch('http://64.23.250.130/api/register/', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
      if (data.active) {
        alert('Registro exitoso. Por favor, verifica tu correo electrónico para activar tu cuenta.');
      } else {
        alert('Error al registrar: ' + data.message);
      }
    })
    .catch(error => {
      alert('Hubo un error en la solicitud: ' + error);
    });
  });
</script>

</body>
</html>
