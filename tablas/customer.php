<?php
$api_url = "http://64.23.250.130/api/customers/";

function getCustomers($url = null) {
    global $api_url;
    $url = $url ? $url : $api_url;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function getCustomer($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function createCustomer($first_name, $last_name, $email, $active) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'active' => $active,
        'create_date' => $current_datetime,
        'last_update' => $current_datetime,
        'store' => 1, 
        'address' => 1 
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

function updateCustomer($id, $first_name, $last_name, $email, $active) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $existing_customer = getCustomer($id);
    
    $data = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'active' => $active,
        'create_date' => $existing_customer['create_date'], 
        'last_update' => $current_datetime,
        'store' => $existing_customer['store'] ?? 1,
        'address' => $existing_customer['address'] ?? 1
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

function deleteCustomer($id) {
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
$customer_edit = null;

if (isset($_POST['delete']) && isset($_POST['customer_id'])) {
    $delete_result = deleteCustomer($_POST['customer_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Cliente eliminado correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar el cliente</div>';
    }
}

if (isset($_POST['save'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $active = intval($_POST['active']);
    
    if (empty($first_name) || empty($last_name)) {
        $message = '<div class="alert alert-danger">Los campos Nombre y Apellido son obligatorios.</div>';
    } else {
        if (isset($_POST['customer_id']) && !empty($_POST['customer_id'])) {
            $update_result = updateCustomer($_POST['customer_id'], $first_name, $last_name, $email, $active);
            if ($update_result) {
                $message = '<div class="alert alert-success">Cliente actualizado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar el cliente</div>';
            }
        } else {
            $create_result = createCustomer($first_name, $last_name, $email, $active);
            if ($create_result) {
                $message = '<div class="alert alert-success">Cliente creado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear el cliente</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $customer_edit = getCustomer($_GET['edit']);
}

$page_url = isset($_GET['page_url']) ? urldecode($_GET['page_url']) : null;
$customers = getCustomers($page_url);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1 >Gestión de Clientes</h1>
        </section>
        <section class="content">
            <div class="container-fluid">
            <?php echo $message; ?>
                <div class="card card-primary noGuest">
                    <div class="card-body">
                        <form method="post" action="">
                            <input type="hidden" name="customer_id" value="<?php echo $customer_edit['customer_id'] ?? ''; ?>">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" name="first_name" class="form-control" required value="<?php echo $customer_edit['first_name'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Apellido:</label>
                                <input type="text" name="last_name" class="form-control" required value="<?php echo $customer_edit['last_name'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $customer_edit['email'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Activo:</label>
                                <select name="active" class="form-control">
                                    <option value="1" <?php echo (isset($customer_edit['active']) && $customer_edit['active'] == 1) ? 'selected' : ''; ?>>Sí</option>
                                    <option value="0" <?php echo (isset($customer_edit['active']) && $customer_edit['active'] == 0) ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                            <?php if ($customer_edit): ?>
                                <a href="customer.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Clientes</h3>
                    </div>
                    <div class="card-body">
                        <?php if(empty($customers['results'])): ?>
                            <div class="alert alert-info">
                                No se encontraron clientes o hubo un problema al conectar con la API.
                            </div>
                        <?php else: ?>
                            <table class="table table-bordered table-striped">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination">
                                        <li class="page-item disabled"><a class="page-link">Cantidad: <?php echo $customers['count'] ?></a></li>
                                        <li class="page-item <?php echo $customers['previous'] ? '' : 'disabled' ?>">
                                            <a class="page-link" href="?page_url=<?php echo urlencode($customers['previous']); ?>"><<</a>
                                        </li>
                                        <li class="page-item <?php echo $customers['next'] ? '' : 'disabled' ?>">
                                            <a class="page-link" href="?page_url=<?php echo urlencode($customers['next']); ?>">>></a>
                                        </li>
                                    </ul>
                                </nav>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Email</th>
                                        <th>Activo</th>
                                        <th>Creación</th>
                                        <th>Última Actualización</th>
                                        <th class="noGuest">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($customers['results'] as $customer): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
                                                <td><?php echo htmlspecialchars($customer['first_name']); ?></td>
                                                <td><?php echo htmlspecialchars($customer['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                                <td><?php echo $customer['active'] ? 'Sí' : 'No'; ?></td>
                                                <td><?php echo htmlspecialchars($customer['create_date']); ?></td>
                                                <td><?php echo htmlspecialchars($customer['last_update']); ?></td>
                                                <td class="noGuest">
                                                    <a href="?edit=<?php echo $customer['customer_id']; ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                    <form method="post" style="display:inline;">
                                                        <input type="hidden" name="customer_id" value="<?php echo $customer['customer_id']; ?>">
                                                        <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este cliente?');">
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
