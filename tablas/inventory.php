<?php
$api_url = "http://64.23.250.130/api/inventories/";

function getInventories() {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
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

function getInventory($id) {
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

function createInventory($film, $store) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'film' => $film,
        'store' => $store,
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

function updateInventory($id, $film, $store) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'film' => $film,
        'store' => $store,
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

function deleteInventory($id) {
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
$inventory_edit = null;

if (isset($_POST['delete']) && isset($_POST['inventory_id'])) {
    $delete_result = deleteInventory($_POST['inventory_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Inventario eliminado correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar el inventario</div>';
    }
}

if (isset($_POST['save'])) {
    $film = intval($_POST['film']);
    $store = intval($_POST['store']);
    
    if ($film <= 0 || $store <= 0) {
        $message = '<div class="alert alert-danger">Los IDs de película y tienda son obligatorios y deben ser números válidos.</div>';
    } else {
        if (isset($_POST['inventory_id']) && !empty($_POST['inventory_id'])) {
            $update_result = updateInventory($_POST['inventory_id'], $film, $store);
            if (isset($update_result['last_update']) && is_array($update_result['last_update'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['last_update'][0] . '</div>';
            } else if ($update_result) {
                $message = '<div class="alert alert-success">Inventario actualizado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar el inventario</div>';
            }
        } else {
            $create_result = createInventory($film, $store);
            if (isset($create_result['last_update']) && is_array($create_result['last_update'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['last_update'][0] . '</div>';
            } else if ($create_result) {
                $message = '<div class="alert alert-success">Inventario creado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear el inventario</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $inventory_edit = getInventory($_GET['edit']);
}

$inventories = getInventories();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestión de Inventario</h1>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                <div class="card card-primary noGuest">
                    <div class="card-body">
                        <form method="post" action="">
                            <?php if(isset($inventory_edit) && $inventory_edit): ?>
                                <input type="hidden" name="inventory_id" value="<?php echo $inventory_edit['inventory_id'] ?? ''; ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label>ID Película:</label>
                                <input type="number" name="film" class="form-control" required value="<?php echo $inventory_edit['film'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>ID Tienda:</label>
                                <input type="number" name="store" class="form-control" required value="<?php echo $inventory_edit['store'] ?? ''; ?>">
                            </div>
                            <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                            <?php if(isset($inventory_edit)): ?>
                                <a href="inventory.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Inventario</h3>
                    </div>
                    <div class="card-body">
                        <?php if(empty($inventories)): ?>
                            <div class="alert alert-info">
                                No se encontraron registros de inventario o hubo un problema al conectar con la API.
                            </div>
                        <?php else: ?>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID Inventario</th>
                                        <th>Película ID</th>
                                        <th>Tienda ID</th>
                                        <th>Última Actualización</th>
                                        <th class="noGuest">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inventories as $inventory): ?>
                                        <tr>
                                            <td><?php echo $inventory['inventory_id'] ?? ''; ?></td>
                                            <td><?php echo $inventory['film'] ?? ''; ?></td>
                                            <td><?php echo $inventory['store'] ?? ''; ?></td>
                                            <td><?php echo isset($inventory['last_update']) ? date('d/m/Y H:i:s', strtotime($inventory['last_update'])) : ''; ?></td>
                                            <td class="noGuest">
                                                <a href="?edit=<?php echo $inventory['inventory_id'] ?? ''; ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="inventory_id" value="<?php echo $inventory['inventory_id'] ?? ''; ?>">
                                                    <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este registro de inventario?');">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
