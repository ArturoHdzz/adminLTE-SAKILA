<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sakila DB | Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
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
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header border-0">
                <h3 class="card-title">Resumen de tablas</h3>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle">
                  <thead>
                  <tr>
                    <th>Tabla</th>
                    <th>Descripción</th>
                    <th>Acción</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td>Actor</td>
                    <td>Información de actores</td>
                    <td>
                      <a href="tablas/actor.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>Film</td>
                    <td>Información de películas</td>
                    <td>
                      <a href="tablas/film.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>Customer</td>
                    <td>Información de clientes</td>
                    <td>
                      <a href="tablas/customer.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>Category</td>
                    <td>Categorías de películas</td>
                    <td>
                      <a href="tablas/category.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>Film Actor</td>
                    <td>Relación entre películas y actores</td>
                    <td>
                      <a href="tablas/film_actor.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>Film Category</td>
                    <td>Categorías de películas</td>
                    <td>
                      <a href="tablas/film_category.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>Film Text</td>
                    <td>Texto descriptivo de películas</td>
                    <td>
                      <a href="tablas/film_text.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>Inventory</td>
                    <td>Inventario de películas</td>
                    <td>
                      <a href="tablas/inventory.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>Language</td>
                    <td>Idiomas de películas</td>
                    <td>
                      <a href="tablas/language.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>Payment</td>
                    <td>Registro de pagos</td>
                    <td>
                      <a href="tablas/payment.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>Rental</td>
                    <td>Registro de alquileres</td>
                    <td>
                      <a href="tablas/rental.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>Staff</td>
                    <td>Información de empleados</td>
                    <td>
                      <a href="tablas/staff.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>Store</td>
                    <td>Información de tiendas</td>
                    <td>
                      <a href="tablas/store.php" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card">
              <div class="card-header border-0">
                <h3 class="card-title">Información del proyecto</h3>
              </div>
              <div class="card-body">
                <p>Este es un proyecto de visualización de la base de datos Sakila utilizando AdminLTE 3.2</p>
                <p>Características:</p>
                <ul>
                  <li>Visualización de todas las tablas</li>
                  <li>Eliminación de registros</li>
                  <li>Vista detallada de registros</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <?php include '_controlside.php'; ?>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <?php include '_footer.php'; ?>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>