<?php
// Incluir Guzzle HTTP Client
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Configurar cliente HTTP
$client = new Client([
    'base_uri' => 'http://tu-dominio.com/api/', // Cambia esto por la URL de tu API
    'timeout'  => 10.0,
]);

// Procesar eliminación si se solicita
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        // Llamada al endpoint para eliminar categoría
        $response = $client->delete("categories/{$id}");
        
        if ($response->getStatusCode() == 200) {
            $mensaje = "Categoría eliminada correctamente";
        } else {
            $error = "Error al eliminar categoría";
        }
    } catch (RequestException $e) {
        $error = "Error de conexión: " . $e->getMessage();
    }
}

// Configurar paginación
$registros_por_pagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

try {
    // Parámetros para la solicitud
    $params = [
        'query' => [
            'page' => $pagina,
            'per_page' => $registros_por_pagina
        ]
    ];

    // Añadir parámetro de búsqueda si existe
    if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
        $params['query']['search'] = $_GET['buscar'];
    }

    // Obtener datos de categorías
    $response = $client->get('categories', $params);
    
    if ($response->getStatusCode() == 200) {
        $data = json_decode($response->getBody(), true);
        
        $categorias = $data['data'];
        $total_registros = $data['total'];
        $total_paginas = $data['last_page'];
    } else {
        $error = "Error al obtener categorías";
        $categorias = [];
        $total_registros = 0;
        $total_paginas = 0;
    }
} catch (RequestException $e) {
    $error = "Error de conexión: " . $e->getMessage();
    $categorias = [];
    $total_registros = 0;
    $total_paginas = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sakila DB | Categorías</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <?php include '../_navbar.php'; ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php include '../_sidebar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Categorías</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Categorías</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <?php if (isset($mensaje)): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h5><i class="icon fas fa-check"></i> ¡Éxito!</h5>
          <?php echo $mensaje; ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h5><i class="icon fas fa-ban"></i> ¡Error!</h5>
          <?php echo $error; ?>
        </div>
        <?php endif; ?>
      
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Listado de Categorías</h3>

                <div class="card-tools">
                  <form action="" method="GET">
                    <div class="input-group input-group-sm" style="width: 150px;">
                      <input type="text" name="buscar" class="form-control float-right" placeholder="Buscar">
                      <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                          <i class="fas fa-search"></i>
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Última actualización</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($categoria['category_id']); ?></td>
                      <td><?php echo htmlspecialchars($categoria['name']); ?></td>
                      <td><?php echo htmlspecialchars($categoria['last_update']); ?></td>
                      <td>
                        <a href="category_detalle.php?id=<?php echo $categoria['category_id']; ?>" class="btn btn-info btn-sm">
                          <i class="fas fa-eye"></i>
                        </a>
                        <a href="javascript:void(0)" onclick="confirmarEliminar(<?php echo $categoria['category_id']; ?>)" class="btn btn-danger btn-sm">
                          <i class="fas fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                  <?php if ($pagina > 1): ?>
                  <li class="page-item"><a class="page-link" href="?pagina=1">&laquo;</a></li>
                  <?php endif; ?>
                  
                  <?php
                  // Mostrar enlaces de paginación
                  $inicio_paginas = max(1, $pagina - 2);
                  $fin_paginas = min($total_paginas, $pagina + 2);
                  
                  for ($i = $inicio_paginas; $i <= $fin_paginas; $i++):
                  ?>
                  <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                    <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                  </li>
                  <?php endfor; ?>
                  
                  <?php if ($pagina < $total_paginas): ?>
                  <li class="page-item"><a class="page-link" href="?pagina=<?php echo $total_paginas; ?>">&raquo;</a></li>
                  <?php endif; ?>
                </ul>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <?php include '../_controlside.php'; ?>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <?php include '../_footer.php'; ?>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

<script>
function confirmarEliminar(id) {
  if (confirm('¿Estás seguro de que deseas eliminar esta categoría? Esta acción no se puede deshacer.')) {
    window.location.href = '?action=delete&id=' + id;
  }
}
</script>
</body>
</html>