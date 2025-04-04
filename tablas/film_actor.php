<?php
$api_url = "http://64.23.250.130/api/film-actors/";

function getFilmActors() {
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
    
    // Verify decoded data
    if(json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Error: ' . json_last_error_msg());
        error_log('Response: ' . substr($response, 0, 1000)); 
        return array(); 
    }
    
    return $decoded ?: array(); 
}

function getFilmActor($id) {
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

function createFilmActor($actor, $film) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'actor' => $actor,
        'film' => $film,
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

function updateFilmActor($id, $actor, $film) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'actor' => $actor,
        'film' => $film,
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

function deleteFilmActor($id) {
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
$filmactor_edit = null;

if (isset($_POST['delete']) && isset($_POST['id'])) {
    $delete_result = deleteFilmActor($_POST['id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Relación Actor-Película eliminada correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar la relación Actor-Película</div>';
    }
}

if (isset($_POST['save'])) {
    $actor = intval($_POST['actor']);
    $film = intval($_POST['film']);
    
    if ($actor <= 0 || $film <= 0) {
        $message = '<div class="alert alert-danger">Los IDs de actor y película son obligatorios y deben ser números válidos.</div>';
    } else {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $update_result = updateFilmActor($_POST['id'], $actor, $film);
            if (isset($update_result['actor']) && is_array($update_result['actor'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['actor'][0] . '</div>';
            } else if ($update_result) {
                $message = '<div class="alert alert-success">Relación Actor-Película actualizada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar la relación Actor-Película</div>';
            }
        } else {
            $create_result = createFilmActor($actor, $film);
            if (isset($create_result['actor']) && is_array($create_result['actor'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['actor'][0] . '</div>';
            } else if ($create_result) {
                $message = '<div class="alert alert-success">Relación Actor-Película creada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear la relación Actor-Película</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $filmactor_edit = getFilmActor($_GET['edit']);
}

$filmactors = getFilmActors();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Relaciones Actor-Película</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestión de Relaciones Actor-Película</h1>
        </section>
        <section class="content">
            <div class="container-fluid ">
                <?php echo $message; ?>
                <div class="card card-primary">
                    <div class="card-body noGuest">
                        <form method="post" action="">
                            <?php if(isset($filmactor_edit) && $filmactor_edit): ?>
                                <input type="hidden" name="id" value="<?php echo $filmactor_edit['id'] ?? ''; ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label>ID Actor:</label>
                                <input type="number" name="actor" class="form-control" required value="<?php echo $filmactor_edit['actor'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>ID Película:</label>
                                <input type="number" name="film" class="form-control" required value="<?php echo $filmactor_edit['film'] ?? ''; ?>">
                            </div>
                            <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                            <?php if(isset($filmactor_edit)): ?>
                                <a href="film_actor.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Relaciones Actor-Película</h3>
                    </div>
                    <div class="card-body">
                        <?php if(empty($filmactors)): ?>
                            <div class="alert alert-info">
                                No se encontraron relaciones de actor-película o hubo un problema al conectar con la API.
                            </div>
                        <?php else: ?>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Actor ID</th>
                                        <th>Película ID</th>
                                        <th>Última Actualización</th>
                                        <th class="noGuest">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($filmactors as $filmactor): ?>
                                        <tr>
                                            <td><?php echo $filmactor['id'] ?? ''; ?></td>
                                            <td><?php echo $filmactor['actor'] ?? ''; ?></td>
                                            <td><?php echo $filmactor['film'] ?? ''; ?></td>
                                            <td><?php echo isset($filmactor['last_update']) ? date('d/m/Y H:i:s', strtotime($filmactor['last_update'])) : ''; ?></td>
                                            <td class="noGuest">
                                                <a href="?edit=<?php echo $filmactor['id'] ?? ''; ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?php echo $filmactor['id'] ?? ''; ?>">
                                                    <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta relación?');">
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
