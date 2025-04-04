<?php
$api_url = "http://64.23.250.130/api/stores/";

function getStores($url = null) {
    global $api_url;
    $url = $url ? $url : $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return array(); 
    }
    
    curl_close($ch);
    $decoded = json_decode($response, true);
    
    if(json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Error: ' . json_last_error_msg());
        error_log('Response: ' . substr($response, 0, 1000)); 
        return array();
    }
    
    return $decoded ?: array(); 
}

function getStore($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

function createStore($manager_staff, $address) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'manager_staff' => $manager_staff,
        'address' => $address,
        'last_update' => $current_datetime
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

function updateStore($id, $manager_staff, $address) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'manager_staff' => $manager_staff,
        'address' => $address,
        'last_update' => $current_datetime
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

function deleteStore($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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
$store_edit = null;

if (isset($_POST['delete']) && isset($_POST['store_id'])) {
    $delete_result = deleteStore($_POST['store_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Tienda eliminada correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar la tienda</div>';
    }
}

if (isset($_POST['save'])) {
    $manager_staff = intval($_POST['manager_staff']);
    $address = intval($_POST['address']);
    
    $errors = array();
    
    if ($manager_staff <= 0) {
        $errors[] = "El ID del gerente es obligatorio y debe ser válido.";
    }
    
    if ($address <= 0) {
        $errors[] = "El ID de la dirección es obligatorio y debe ser válido.";
    }
    
    if (!empty($errors)) {
        $message = '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            $message .= '<li>' . $error . '</li>';
        }
        $message .= '</ul></div>';
    } else {
        if (isset($_POST['store_id']) && !empty($_POST['store_id'])) {
            $update_result = updateStore($_POST['store_id'], $manager_staff, $address);
            
            if (is_array($update_result) && isset($update_result['manager_staff']) && is_array($update_result['manager_staff'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['manager_staff'][0] . '</div>';
            } elseif (is_array($update_result) && isset($update_result['address']) && is_array($update_result['address'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['address'][0] . '</div>';
            } elseif ($update_result) {
                $message = '<div class="alert alert-success">Tienda actualizada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar la tienda</div>';
            }
        } else {
            $create_result = createStore($manager_staff, $address);
            
            if (is_array($create_result) && isset($create_result['manager_staff']) && is_array($create_result['manager_staff'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['manager_staff'][0] . '</div>';
            } elseif (is_array($create_result) && isset($create_result['address']) && is_array($create_result['address'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['address'][0] . '</div>';
            } elseif ($create_result) {
                $message = '<div class="alert alert-success">Tienda creada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear la tienda</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $store_edit = getStore($_GET['edit']);
}

$page_url = isset($_GET['page_url']) ? urldecode($_GET['page_url']) : null;
$stores = getStores($page_url);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tiendas</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestión de Tiendas</h1>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                <div class="card card-primary">
                    <div class="card-body">
                        <form method="post" action="">
                            <?php if(isset($store_edit) && $store_edit): ?>
                                <input type="hidden" name="store_id" value="<?php echo $store_edit['store_id'] ?? ''; ?>">
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ID del Gerente:</label>
                                        <input type="number" name="manager_staff" class="form-control" required value="<?php echo $store_edit['manager_staff'] ?? ''; ?>">
                                        <small class="form-text text-muted">ID del miembro del personal que será gerente (debe ser único)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ID de la Dirección:</label>
                                        <input type="number" name="address" class="form-control" required value="<?php echo $store_edit['address'] ?? ''; ?>">
                                        <small class="form-text text-muted">ID de la dirección de la tienda</small>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                            <?php if(isset($store_edit)): ?>
                                <a href="store.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Tiendas</h3>
                    </div>
                    <div class="card-body">
                        <?php if(empty($stores['results'])): ?>
                            <div class="alert alert-info">
                                No se encontraron tiendas o hubo un problema al conectar con la API.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination">
                                            <li class="page-item disabled"><a class="page-link">Cantidad: <?php echo $stores['count'] ?></a></li>
                                            <li class="page-item <?php echo $stores['previous'] ? '' : 'disabled' ?>">
                                                <a class="page-link" href="?page_url=<?php echo urlencode($stores['previous']); ?>"><<</a>
                                            </li>
                                            <li class="page-item <?php echo $stores['next'] ? '' : 'disabled' ?>">
                                                <a class="page-link" href="?page_url=<?php echo urlencode($stores['next']); ?>">>></a>
                                            </li>
                                        </ul>
                                    </nav>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>ID Gerente</th>
                                            <th>ID Dirección</th>
                                            <th>Última Actualización</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stores['results'] as $store): ?>
                                            <tr>
                                                <td><?php echo $store['store_id'] ?? ''; ?></td>
                                                <td><?php echo $store['manager_staff'] ?? ''; ?></td>
                                                <td><?php echo $store['address'] ?? ''; ?></td>
                                                <td><?php echo isset($store['last_update']) ? date('d/m/Y H:i:s', strtotime($store['last_update'])) : ''; ?></td>
                                                <td>
                                                    <a href="?edit=<?php echo $store['store_id'] ?? ''; ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                    <form method="post" style="display:inline;">
                                                        <input type="hidden" name="store_id" value="<?php echo $store['store_id'] ?? ''; ?>">
                                                        <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta tienda?');">
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