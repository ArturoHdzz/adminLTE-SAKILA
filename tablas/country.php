<?php
$api_url = "http://64.23.250.130/api/countries/";

function getCountries() {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function getCountry($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function createCountry($country) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'country' => $country,
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

function updateCountry($id, $country) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'country' => $country,
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

function deleteCountry($id) {
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
$country_edit = null;

if (isset($_POST['delete']) && isset($_POST['country_id'])) {
    $delete_result = deleteCountry($_POST['country_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">País eliminado correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar el país</div>';
    }
}

if (isset($_POST['save'])) {
    $country = trim($_POST['country']);
    
    if (empty($country)) {
        $message = '<div class="alert alert-danger">El campo País es obligatorio.</div>';
    } else {
        if (isset($_POST['country_id']) && !empty($_POST['country_id'])) {
            $update_result = updateCountry($_POST['country_id'], $country);
            if ($update_result) {
                $message = '<div class="alert alert-success">País actualizado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar el país</div>';
            }
        } else {
            $create_result = createCountry($country);
            if ($create_result) {
                $message = '<div class="alert alert-success">País creado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear el país</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $country_edit = getCountry($_GET['edit']);
}

$countries = getCountries();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Países</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestión de Países</h1>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                <form method="post" action="" class="noGuest">
                    <input type="hidden" name="country_id" value="<?php echo $country_edit['country_id'] ?? ''; ?>">
                    <label>País:</label>
                    <input type="text" name="country" class="form-control" required value="<?php echo $country_edit['country'] ?? ''; ?>">
                    <button type="submit" name="save" class="btn btn-primary mb-3 mt-3">Guardar</button>
                </form>
                <table class="table table-bordered table-striped">
                    <thead><tr><th>ID</th><th>País</th><th>Última Actualización</th><th class="noGuest">Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($countries as $country): ?>
                            <tr>
                                <td><?php echo $country['country_id']; ?></td>
                                <td><?php echo $country['country']; ?></td>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($country['last_update'])); ?></td>
                                <td class="noGuest">
                                    <a href="?edit=<?php echo $country['country_id']; ?>" class="btn btn-info btn-sm">Editar</a>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="country_id" value="<?php echo $country['country_id']; ?>">
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
