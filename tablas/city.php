<?php
$api_url = "http://64.23.250.130/api/cities/";

function getCities() {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function getCity($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function createCity($city, $country) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'city' => $city,
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

function updateCity($id, $city, $country) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'city' => $city,
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

function deleteCity($id) {
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
$city_edit = null;

if (isset($_POST['delete']) && isset($_POST['city_id'])) {
    $delete_result = deleteCity($_POST['city_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Ciudad eliminada correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar la ciudad</div>';
    }
}

if (isset($_POST['save'])) {
    $city = trim($_POST['city']);
    $country = intval($_POST['country']);
    
    if (empty($city)) {
        $message = '<div class="alert alert-danger">El campo Ciudad es obligatorio.</div>';
    } else {
        if (isset($_POST['city_id']) && !empty($_POST['city_id'])) {
            $update_result = updateCity($_POST['city_id'], $city, $country);
            if ($update_result) {
                $message = '<div class="alert alert-success">Ciudad actualizada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar la ciudad</div>';
            }
        } else {
            $create_result = createCity($city, $country);
            if ($create_result) {
                $message = '<div class="alert alert-success">Ciudad creada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear la ciudad</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $city_edit = getCity($_GET['edit']);
}

$cities = getCities();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ciudades</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestión de Ciudades</h1>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                <form method="post" action="">
                    <input type="hidden" name="city_id" value="<?php echo $city_edit['city_id'] ?? ''; ?>">
                    <label>Ciudad:</label>
                    <input type="text" name="city" class="form-control" required value="<?php echo $city_edit['city'] ?? ''; ?>">
                    <label>País ID:</label>
                    <input type="number" name="country" class="form-control" required value="<?php echo $city_edit['country'] ?? ''; ?>">
                    <button type="submit" name="save" class="btn btn-primary mb-3 mt-3">Guardar</button>
                </form>
                <table class="table table-bordered table-striped">
                    <thead><tr><th>ID</th><th>Ciudad</th><th>País ID</th><th>Última Actualización</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($cities as $city): ?>
                            <tr>
                                <td><?php echo $city['city_id']; ?></td>
                                <td><?php echo $city['city']; ?></td>
                                <td><?php echo $city['country']; ?></td>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($city['last_update'])); ?></td>
                                <td>
                                    <a href="?edit=<?php echo $city['city_id']; ?>" class="btn btn-info btn-sm">Editar</a>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="city_id" value="<?php echo $city['city_id']; ?>">
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
</body>
</html>