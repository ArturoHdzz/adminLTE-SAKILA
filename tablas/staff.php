<?php
$api_url = "http://64.23.250.130/api/staffs/";

function getStaffs($url = null) {
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

function getStaff($id) {
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

function createStaff($first_name, $last_name, $picture, $email, $active, $username, $address, $store) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'picture' => $picture,
        'email' => $email,
        'active' => $active,
        'username' => $username,
        'last_update' => $current_datetime,
        'address' => $address,
        'store' => $store
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

function updateStaff($id, $first_name, $last_name, $picture, $email, $active, $username, $address, $store) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'picture' => $picture,
        'email' => $email,
        'active' => $active,
        'username' => $username,
        'last_update' => $current_datetime,
        'address' => $address,
        'store' => $store
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

function deleteStaff($id) {
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
$staff_edit = null;

if (isset($_POST['delete']) && isset($_POST['staff_id'])) {
    $delete_result = deleteStaff($_POST['staff_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Miembro del personal eliminado correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar el miembro del personal</div>';
    }
}

if (isset($_POST['save'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $picture = !empty($_POST['picture']) ? $_POST['picture'] : null;
    $email = trim($_POST['email']);
    $active = isset($_POST['active']) ? 1 : 0;
    $username = trim($_POST['username']);
    $address = intval($_POST['address']);
    $store = intval($_POST['store']);
    
    $errors = array();
    
    if (empty($first_name)) {
        $errors[] = "El nombre es obligatorio.";
    }
    
    if (empty($last_name)) {
        $errors[] = "El apellido es obligatorio.";
    }
    
    if (empty($username)) {
        $errors[] = "El nombre de usuario es obligatorio.";
    }
    
    if ($address <= 0) {
        $errors[] = "La dirección es obligatoria y debe ser un ID válido.";
    }
    
    if ($store <= 0) {
        $errors[] = "La tienda es obligatoria y debe ser un ID válido.";
    }
    
    if (!empty($errors)) {
        $message = '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            $message .= '<li>' . $error . '</li>';
        }
        $message .= '</ul></div>';
    } else {
        if (isset($_POST['staff_id']) && !empty($_POST['staff_id'])) {
            $update_result = updateStaff($_POST['staff_id'], $first_name, $last_name, $picture, $email, $active, $username, $address, $store);
            
            if (is_array($update_result) && isset($update_result['first_name']) && is_array($update_result['first_name'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['first_name'][0] . '</div>';
            } elseif (is_array($update_result) && isset($update_result['last_name']) && is_array($update_result['last_name'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['last_name'][0] . '</div>';
            } elseif ($update_result) {
                $message = '<div class="alert alert-success">Miembro del personal actualizado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar el miembro del personal</div>';
            }
        } else {
            $create_result = createStaff($first_name, $last_name, $picture, $email, $active, $username, $address, $store);
            
            if (is_array($create_result) && isset($create_result['first_name']) && is_array($create_result['first_name'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['first_name'][0] . '</div>';
            } elseif (is_array($create_result) && isset($create_result['last_name']) && is_array($create_result['last_name'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['last_name'][0] . '</div>';
            } elseif ($create_result) {
                $message = '<div class="alert alert-success">Miembro del personal creado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear el miembro del personal</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $staff_edit = getStaff($_GET['edit']);
}

$page_url = isset($_GET['page_url']) ? urldecode($_GET['page_url']) : null;
$staffs = getStaffs($page_url);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Personal</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestión de Personal</h1>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                <div class="card card-primary noGuest">
                    <div class="card-body">
                        <form method="post" action="">
                            <?php if(isset($staff_edit) && $staff_edit): ?>
                                <input type="hidden" name="staff_id" value="<?php echo $staff_edit['staff_id'] ?? ''; ?>">
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre:</label>
                                        <input type="text" name="first_name" class="form-control" required value="<?php echo $staff_edit['first_name'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Apellido:</label>
                                        <input type="text" name="last_name" class="form-control" required value="<?php echo $staff_edit['last_name'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email:</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo $staff_edit['email'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre de Usuario:</label>
                                        <input type="text" name="username" class="form-control" required value="<?php echo $staff_edit['username'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>URL de la Imagen:</label>
                                        <input type="text" name="picture" class="form-control" value="<?php echo $staff_edit['picture'] ?? ''; ?>" placeholder="URL de la imagen (opcional)">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Activo:</label>
                                        <div class="custom-control custom-switch mt-2">
                                            <input type="checkbox" class="custom-control-input" id="activeSwitch" name="active" <?php echo (!isset($staff_edit) || $staff_edit['active'] == 1) ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="activeSwitch">Sí</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>ID Dirección:</label>
                                        <input type="number" name="address" class="form-control" required value="<?php echo $staff_edit['address'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>ID Tienda:</label>
                                        <input type="number" name="store" class="form-control" required value="<?php echo $staff_edit['store'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                            <?php if(isset($staff_edit)): ?>
                                <a href="staff.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Personal</h3>
                    </div>
                    <div class="card-body">
                        <?php if(empty($staffs['results'])): ?>
                            <div class="alert alert-info">
                                No se encontraron miembros del personal o hubo un problema al conectar con la API.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination">
                                            <li class="page-item disabled"><a class="page-link">Cantidad: <?php echo $staffs['count'] ?></a></li>
                                            <li class="page-item <?php echo $staffs['previous'] ? '' : 'disabled' ?>">
                                                <a class="page-link" href="?page_url=<?php echo urlencode($staffs['previous']); ?>"><<</a>
                                            </li>
                                            <li class="page-item <?php echo $staffs['next'] ? '' : 'disabled' ?>">
                                                <a class="page-link" href="?page_url=<?php echo urlencode($staffs['next']); ?>">>></a>
                                            </li>
                                        </ul>
                                    </nav>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Usuario</th>
                                            <th>Estado</th>
                                            <th>Última Actualización</th>
                                            <th>Dirección ID</th>
                                            <th>Tienda ID</th>
                                            <th class="noGuest">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($staffs['results'] as $staff): ?>
                                            <tr>
                                                <td><?php echo $staff['staff_id'] ?? ''; ?></td>
                                                <td><?php echo ($staff['first_name'] ?? '') . ' ' . ($staff['last_name'] ?? ''); ?></td>
                                                <td><?php echo $staff['email'] ?? ''; ?></td>
                                                <td><?php echo $staff['username'] ?? ''; ?></td>
                                                <td>
                                                    <?php if(isset($staff['active']) && $staff['active'] == 1): ?>
                                                        <span class="badge badge-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo isset($staff['last_update']) ? date('d/m/Y H:i:s', strtotime($staff['last_update'])) : ''; ?></td>
                                                <td><?php echo $staff['address'] ?? ''; ?></td>
                                                <td><?php echo $staff['store'] ?? ''; ?></td>
                                                <td class="noGuest">
                                                    <a href="?edit=<?php echo $staff['staff_id'] ?? ''; ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                    <form method="post" style="display:inline;">
                                                        <input type="hidden" name="staff_id" value="<?php echo $staff['staff_id'] ?? ''; ?>">
                                                        <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este miembro del personal?');">
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
