<?php
$api_url = "http://64.23.250.130/api/actors/";

function getActors() {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function getActor($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function createActor($first_name, $last_name) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'last_update' => $current_datetime
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function updateActor($id, $first_name, $last_name) {
    global $api_url;
    // Formato ISO 8601 requerido: YYYY-MM-DDThh:mm:ssZ
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'last_update' => $current_datetime
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function deleteActor($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $http_code;
}

$message = '';
$actor_edit = null;

if (isset($_POST['delete']) && isset($_POST['actor_id'])) {
    $delete_result = deleteActor($_POST['actor_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Actor eliminado correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar el actor</div>';
    }
}

if (isset($_POST['save'])) {
    $first_name = strtoupper($_POST['first_name']);
    $last_name = strtoupper($_POST['last_name']);
    
    if (isset($_POST['actor_id']) && !empty($_POST['actor_id'])) {
        $update_result = updateActor($_POST['actor_id'], $first_name, $last_name);
        if ($update_result) {
            $message = '<div class="alert alert-success">Actor actualizado correctamente</div>';
        } else {
            $message = '<div class="alert alert-danger">Error al actualizar el actor</div>';
        }
    } else {
        $create_result = createActor($first_name, $last_name);
        if ($create_result) {
            $message = '<div class="alert alert-success">Actor creado correctamente</div>';
        } else {
            $message = '<div class="alert alert-danger">Error al crear el actor</div>';
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $actor_edit = getActor($_GET['edit']);
}

$actors = getActors();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE | Gestión de Actores</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
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
                        <h1>Gestión de Actores</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item active">Actores</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $actor_edit ? 'Editar Actor' : 'Nuevo Actor'; ?></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form method="post" action="">
                            <?php if ($actor_edit): ?>
                                <input type="hidden" name="actor_id" value="<?php echo $actor_edit['actor_id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">Nombre</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required
                                               value="<?php echo $actor_edit ? $actor_edit['first_name'] : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Apellido</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required
                                               value="<?php echo $actor_edit ? $actor_edit['last_name'] : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                                <?php if ($actor_edit): ?>
                                    <a href="actor.php" class="btn btn-default">Cancelar</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
                
                <!-- Tabla de Actores -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Actores</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="actorsTable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Última Actualización</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($actors): ?>
                                <?php foreach ($actors as $actor): ?>
                                    <tr>
                                        <td><?php echo $actor['actor_id']; ?></td>
                                        <td><?php echo $actor['first_name']; ?></td>
                                        <td><?php echo $actor['last_name']; ?></td>
                                        <td><?php echo date('d/m/Y H:i:s', strtotime($actor['last_update'])); ?></td>
                                        <td>
                                            <a href="?edit=<?php echo $actor['actor_id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete(<?php echo $actor['actor_id']; ?>)">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
                
                <form id="deleteForm" method="post" style="display: none;">
                    <input type="hidden" name="actor_id" id="deleteActorId">
                    <input type="hidden" name="delete" value="1">
                </form>
            </div>
            <!-- /.container-fluid -->
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

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    $('#actorsTable').DataTable({
      "responsive": true, 
      "lengthChange": false, 
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#actorsTable_wrapper .col-md-6:eq(0)');
  });
  
  function confirmDelete(actorId) {
    if (confirm('¿Está seguro de que desea eliminar este actor?')) {
      document.getElementById('deleteActorId').value = actorId;
      document.getElementById('deleteForm').submit();
    }
  }
</script>
</body>
</html>