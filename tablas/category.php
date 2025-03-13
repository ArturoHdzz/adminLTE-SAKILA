<?php
$api_url = "http://64.23.250.130/api/categories/";

function getCategories() {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function getCategory($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function createCategory($name) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'name' => $name,
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

function updateCategory($id, $name) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'name' => $name,
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

function deleteCategory($id) {
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
$category_edit = null;

if (isset($_POST['delete']) && isset($_POST['category_id'])) {
    $delete_result = deleteCategory($_POST['category_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Categoría eliminada correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar la categoría</div>';
    }
}

if (isset($_POST['save'])) {
    $name = trim($_POST['name']);
    
    if (empty($name)) {
        $message = '<div class="alert alert-danger">El campo Nombre es obligatorio.</div>';
    } else {
        if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
            $update_result = updateCategory($_POST['category_id'], $name);
            if ($update_result) {
                $message = '<div class="alert alert-success">Categoría actualizada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar la categoría</div>';
            }
        } else {
            $create_result = createCategory($name);
            if ($create_result) {
                $message = '<div class="alert alert-success">Categoría creada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear la categoría</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $category_edit = getCategory($_GET['edit']);
}

$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestión de Categorías</h1>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                <form method="post" action="">
                    <input type="hidden" name="category_id" value="<?php echo $category_edit['category_id'] ?? ''; ?>">
                    <label>Nombre:</label>
                    <input type="text" name="name" class="form-control" required value="<?php echo $category_edit['name'] ?? ''; ?>">
                    <button type="submit" name="save" class="btn btn-primary mb-3 mt-3">Guardar</button>
                </form>
                <table class="table table-bordered table-striped">
                    <thead><tr><th>ID</th><th>Nombre</th><th>Última Actualización</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo $category['category_id']; ?></td>
                                <td><?php echo $category['name']; ?></td>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($category['last_update'])); ?></td>
                                <td>
                                    <a href="?edit=<?php echo $category['category_id']; ?>" class="btn btn-info btn-sm">Editar</a>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <?php include '../_footer.php'; ?>
</div>
<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
</body>
</html>