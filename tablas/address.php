<?php
$api_url = "http://64.23.250.130/api/addresses/";

function getAuthHeaders() {
    $token = $_COOKIE['access_token'] ?? '';
    return !empty($token) ? [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ] : ['Content-Type: application/json'];
}
function getAddresses($url = null) {
    global $api_url;
    $url = $url ? $url : $api_url;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function getAddress($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function createAddress($address, $district, $phone, $location, $city) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'address' => $address,
        'district' => $district,
        'phone' => $phone,
        'location' => $location,
        'city' => $city,
        'last_update' => $current_datetime
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function updateAddress($id, $address, $district, $phone, $location, $city) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'address' => $address,
        'district' => $district,
        'phone' => $phone,
        'location' => $location,
        'city' => $city,
        'last_update' => $current_datetime
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function deleteAddress($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $http_code;
}

$message = '';
$address_edit = null;

if (isset($_POST['delete']) && isset($_POST['address_id'])) {
    $delete_result = deleteAddress($_POST['address_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Dirección eliminada correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar la dirección</div>';
    }
}

if (isset($_POST['save'])) {
    $address = trim($_POST['address']);
    $district = trim($_POST['district']);
    $phone = trim($_POST['phone']);
    $location = trim($_POST['location']);
    $city = intval($_POST['city']);
    
    if (empty($address) || empty($district) || empty($phone) || empty($location)) {
        $message = '<div class="alert alert-danger">Todos los campos requeridos deben estar llenos.</div>';
    } else {
        if (isset($_POST['address_id']) && !empty($_POST['address_id'])) {
            $update_result = updateAddress($_POST['address_id'], $address, $district, $phone, $location, $city);
            if ($update_result) {
                $message = '<div class="alert alert-success">Dirección actualizada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar la dirección</div>';
            }
        } else {
            $create_result = createAddress($address, $district, $phone, $location, $city);
            if ($create_result) {
                $message = '<div class="alert alert-success">Dirección creada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear la dirección</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $address_edit = getAddress($_GET['edit']);
}

$page_url = isset($_GET['page_url']) ? urldecode($_GET['page_url']) : null;
$addresses = getAddresses($page_url);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Direcciones</title>
    
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
    <!-- Main Sidebar Container -->
    <?php include '../_sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Encabezado de contenido -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Gestión de Direcciones</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item active">Direcciones</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contenido principal -->
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>

                <!-- Formulario de Dirección -->
                <div class="card noGuest" >
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $address_edit ? 'Editar Dirección' : 'Nueva Dirección'; ?></h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <?php if ($address_edit): ?>
                                <input type="hidden" name="address_id" value="<?php echo $address_edit['address_id']; ?>">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Dirección <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="address" name="address" required
                                               value="<?php echo $address_edit ? $address_edit['address'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="district">Distrito <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="district" name="district" required
                                               value="<?php echo $address_edit ? $address_edit['district'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Teléfono <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="phone" name="phone" required
                                               value="<?php echo $address_edit ? $address_edit['phone'] : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address2">Dirección 2</label>
                                        <input type="text" class="form-control" id="address2" name="address2"
                                               value="<?php echo $address_edit ? $address_edit['address2'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="postal_code">Código Postal</label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code"
                                               value="<?php echo $address_edit ? $address_edit['postal_code'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="location">Ubicación <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="location" name="location" required
                                               value="<?php echo $address_edit ? $address_edit['location'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="city">Ciudad</label>
                                        <input type="number" class="form-control" id="city" name="city"
                                               value="<?php echo $address_edit ? $address_edit['city'] : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                                <?php if ($address_edit): ?>
                                    <a href="address.php" class="btn btn-default">Cancelar</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Direcciones -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Direcciones</h3>
                    </div>
                    <div class="card-body">
                        <?php if(empty($addresses['results'])): ?>
                            <div class="alert alert-info">
                                No se encontraron direcciones o hubo un problema al conectar con la API.
                            </div>
                        <?php else: ?>
                            <table id="addressesTable" class="table table-bordered table-striped">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination">
                                        <li class="page-item disabled"><a class="page-link">Cantidad: <?php echo $addresses['count'] ?></a></li>
                                        <li class="page-item <?php echo $addresses['previous'] ? '' : 'disabled' ?>">
                                            <a class="page-link" href="?page_url=<?php echo urlencode($addresses['previous']); ?>"><<</a>
                                        </li>
                                        <li class="page-item <?php echo $addresses['next'] ? '' : 'disabled' ?>">
                                            <a class="page-link" href="?page_url=<?php echo urlencode($addresses['next']); ?>">>></a>
                                        </li>
                                    </ul>
                                </nav>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Dirección</th>
                                        <th>Distrito</th>
                                        <th>Teléfono</th>
                                        <th>Última Actualización</th>
                                        <th class="noGuest">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <?php foreach ($addresses['results'] as $addr): ?>
                                            <tr>
                                                <td><?php echo $addr['address_id']; ?></td>
                                                <td><?php echo $addr['address']; ?></td>
                                                <td><?php echo $addr['district']; ?></td>
                                                <td><?php echo $addr['phone']; ?></td>
                                                <td><?php echo date('d/m/Y H:i:s', strtotime($addr['last_update'])); ?></td>
                                                <td class="noGuest">
                                                    <a href="?edit=<?php echo $addr['address_id']; ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="confirmDelete(<?php echo $addr['address_id']; ?>)">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Formulario oculto para eliminación -->
                <form id="deleteForm" method="post" style="display: none;">
                    <input type="hidden" name="address_id" id="deleteAddressId">
                    <input type="hidden" name="delete" value="1">
                </form>
            </div>
        </section>
    </div>

    <!-- Control Sidebar -->
    <?php include '../_controlside.php'; ?>
    <!-- Main Footer -->
    <?php include '../_footer.php'; ?>
</div>

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<script>
  $(function () {
    $('#addressesTable').DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#addressesTable_wrapper .col-md-6:eq(0)');
  });
  
  function confirmDelete(addressId) {
    if (confirm('¿Está seguro de que desea eliminar esta dirección?')) {
      document.getElementById('deleteAddressId').value = addressId;
      document.getElementById('deleteForm').submit();
    }
  }
</script>
</body>
</html>
