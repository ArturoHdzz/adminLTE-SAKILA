<?php
$api_url = "http://64.23.250.130/api/rentals/";

function getAuthHeaders() {
    $token = $_COOKIE['access_token'] ?? '';
    return !empty($token) ? [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ] : ['Content-Type: application/json'];
}
function getRentals($url = null) {
    global $api_url;
    $url = $url ? $url : $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return array(); 
    }
    
    curl_close($ch);
    $decoded = json_decode($response, true);
    
    // Verify decoded data
    if(json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Error: ' . json_last_error_msg());
        error_log('Response: ' . substr($response, 0, 1000)); 
        return array(); 
    }
    
    return $decoded ?: array();
}

function getRental($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

function createRental($rental_date, $return_date, $inventory, $customer, $staff) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'rental_date' => $rental_date,
        'return_date' => $return_date,
        'last_update' => $current_datetime,
        'inventory' => $inventory,
        'customer' => $customer,
        'staff' => $staff
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

function updateRental($id, $rental_date, $return_date, $inventory, $customer, $staff) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'rental_date' => $rental_date,
        'return_date' => $return_date,
        'last_update' => $current_datetime,
        'inventory' => $inventory,
        'customer' => $customer,
        'staff' => $staff
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

function deleteRental($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return 0;
    }
    
    curl_close($ch);
    return $http_code;
}

$message = '';
$rental_edit = null;

if (isset($_POST['delete']) && isset($_POST['rental_id'])) {
    $delete_result = deleteRental($_POST['rental_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Alquiler eliminado correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar el alquiler</div>';
    }
}

if (isset($_POST['save'])) {
    $rental_date = $_POST['rental_date'];
    $return_date = !empty($_POST['return_date']) ? $_POST['return_date'] : null;
    $inventory = intval($_POST['inventory']);
    $customer = intval($_POST['customer']);
    $staff = intval($_POST['staff']);
    
    $datetime_pattern = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/';
    if (!preg_match($datetime_pattern, $rental_date)) {
        try {
            $date = new DateTime($rental_date);
            $rental_date = $date->format('Y-m-d\TH:i:s\Z');
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger">Formato de fecha de alquiler inválido. Use el formato: YYYY-MM-DDTHH:MM:SSZ</div>';
        }
    }
    
    if ($return_date && !preg_match($datetime_pattern, $return_date)) {
        try {
            $date = new DateTime($return_date);
            $return_date = $date->format('Y-m-d\TH:i:s\Z');
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger">Formato de fecha de devolución inválido. Use el formato: YYYY-MM-DDTHH:MM:SSZ</div>';
        }
    }
    
    if ($inventory <= 0 || $customer <= 0 || $staff <= 0) {
        $message = '<div class="alert alert-danger">Los IDs de inventario, cliente y personal son obligatorios y deben ser números válidos.</div>';
    } else {
        if (isset($_POST['rental_id']) && !empty($_POST['rental_id'])) {
            $update_result = updateRental($_POST['rental_id'], $rental_date, $return_date, $inventory, $customer, $staff);
            if (isset($update_result['rental_date']) && is_array($update_result['rental_date'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['rental_date'][0] . '</div>';
            } elseif (isset($update_result['return_date']) && is_array($update_result['return_date'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['return_date'][0] . '</div>';
            } elseif ($update_result) {
                $message = '<div class="alert alert-success">Alquiler actualizado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar el alquiler</div>';
            }
        } else {
            $create_result = createRental($rental_date, $return_date, $inventory, $customer, $staff);
            if (isset($create_result['rental_date']) && is_array($create_result['rental_date'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['rental_date'][0] . '</div>';
            } elseif (isset($create_result['return_date']) && is_array($create_result['return_date'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['return_date'][0] . '</div>';
            } elseif ($create_result) {
                $message = '<div class="alert alert-success">Alquiler creado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear el alquiler</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $rental_edit = getRental($_GET['edit']);
}

$page_url = isset($_GET['page_url']) ? urldecode($_GET['page_url']) : null;
$rentals = getRentals($page_url);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Alquileres</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestión de Alquileres</h1>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                <div class="card card-primary noGuest">
                    <div class="card-body">
                        <form method="post" action="">
                            <?php if(isset($rental_edit) && $rental_edit): ?>
                                <input type="hidden" name="rental_id" value="<?php echo $rental_edit['rental_id'] ?? ''; ?>">
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de Alquiler:</label>
                                        <input type="text" name="rental_date" class="form-control" required value="<?php echo $rental_edit['rental_date'] ?? gmdate('Y-m-d\TH:i:s\Z'); ?>" placeholder="YYYY-MM-DDTHH:MM:SSZ">
                                        <small class="form-text text-muted">Formato: YYYY-MM-DDTHH:MM:SSZ</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de Devolución:</label>
                                        <input type="text" name="return_date" class="form-control" value="<?php echo $rental_edit['return_date'] ?? ''; ?>" placeholder="YYYY-MM-DDTHH:MM:SSZ">
                                        <small class="form-text text-muted">Formato: YYYY-MM-DDTHH:MM:SSZ (Opcional)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ID Inventario:</label>
                                        <input type="number" name="inventory" class="form-control" required value="<?php echo $rental_edit['inventory'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ID Cliente:</label>
                                        <input type="number" name="customer" class="form-control" required value="<?php echo $rental_edit['customer'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ID Personal:</label>
                                        <input type="number" name="staff" class="form-control" required value="<?php echo $rental_edit['staff'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                            <?php if(isset($rental_edit)): ?>
                                <a href="rental.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Alquileres</h3>
                    </div>
                    <div class="card-body">
                        <?php if(empty($rentals['results'])): ?>
                            <div class="alert alert-info">
                                No se encontraron alquileres o hubo un problema al conectar con la API.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination">
                                            <li class="page-item disabled"><a class="page-link">Cantidad: <?php echo $rentals['count'] ?></a></li>
                                            <li class="page-item <?php echo $rentals['previous'] ? '' : 'disabled' ?>">
                                                <a class="page-link" href="?page_url=<?php echo urlencode($rentals['previous']); ?>"><<</a>
                                            </li>
                                            <li class="page-item <?php echo $rentals['next'] ? '' : 'disabled' ?>">
                                                <a class="page-link" href="?page_url=<?php echo urlencode($rentals['next']); ?>">>></a>
                                            </li>
                                        </ul>
                                    </nav>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha de Alquiler</th>
                                            <th>Fecha de Devolución</th>
                                            <th>Última Actualización</th>
                                            <th>ID Inventario</th>
                                            <th>ID Cliente</th>
                                            <th>ID Personal</th>
                                            <th class="noGuest">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rentals['results'] as $rental): ?>
                                            <tr>
                                                <td><?php echo $rental['rental_id'] ?? ''; ?></td>
                                                <td><?php echo isset($rental['rental_date']) ? date('d/m/Y H:i:s', strtotime($rental['rental_date'])) : ''; ?></td>
                                                <td><?php echo isset($rental['return_date']) && !empty($rental['return_date']) ? date('d/m/Y H:i:s', strtotime($rental['return_date'])) : 'No devuelto'; ?></td>
                                                <td><?php echo isset($rental['last_update']) ? date('d/m/Y H:i:s', strtotime($rental['last_update'])) : ''; ?></td>
                                                <td><?php echo $rental['inventory'] ?? ''; ?></td>
                                                <td><?php echo $rental['customer'] ?? ''; ?></td>
                                                <td><?php echo $rental['staff'] ?? ''; ?></td>
                                                <td class="noGuest">
                                                    <a href="?edit=<?php echo $rental['rental_id'] ?? ''; ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                    <form method="post" style="display:inline;">
                                                        <input type="hidden" name="rental_id" value="<?php echo $rental['rental_id'] ?? ''; ?>">
                                                        <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este alquiler?');">
                                                            <i class="fas fa-trash"></i> Eliminar
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php include '../_footer.php'; ?>
</div>
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/adminlte.min.js"></script>
</body>
</html>
