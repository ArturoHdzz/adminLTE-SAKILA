<?php
$api_url = "http://64.23.250.130/api/film-texts/";

function getFilmTexts() {
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
        return array();
    }
    
    return $decoded ?: array();
}

function getFilmText($film_id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $film_id . "/");
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

function createFilmText($film_id, $title, $description) {
    global $api_url;
    
    $data = array(
        'film_id' => $film_id,
        'title' => $title,
        'description' => $description
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

function updateFilmText($film_id, $title, $description) {
    global $api_url;
    
    $data = array(
        'film_id' => $film_id,
        'title' => $title,
        'description' => $description
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $film_id . "/");
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

function deleteFilmText($film_id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $film_id . "/");
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
$filmtext_edit = null;

if (isset($_POST['delete']) && isset($_POST['film_id'])) {
    $delete_result = deleteFilmText($_POST['film_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Texto de película eliminado correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar el texto de película</div>';
    }
}

if (isset($_POST['save'])) {
    $film_id = intval($_POST['film_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    
    if ($film_id <= 0 || empty($title)) {
        $message = '<div class="alert alert-danger">El ID de película y el título son obligatorios.</div>';
    } else {
        if (isset($_POST['is_edit']) && $_POST['is_edit'] == '1') {
            $update_result = updateFilmText($film_id, $title, $description);
            if (isset($update_result['film_id']) && is_array($update_result['film_id'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['film_id'][0] . '</div>';
            } else if (isset($update_result['title']) && is_array($update_result['title'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['title'][0] . '</div>';
            } else if ($update_result) {
                $message = '<div class="alert alert-success">Texto de película actualizado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar el texto de película</div>';
            }
        } else {
            $create_result = createFilmText($film_id, $title, $description);
            if (isset($create_result['film_id']) && is_array($create_result['film_id'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['film_id'][0] . '</div>';
            } else if (isset($create_result['title']) && is_array($create_result['title'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['title'][0] . '</div>';
            } else if ($create_result) {
                $message = '<div class="alert alert-success">Texto de película creado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear el texto de película</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $filmtext_edit = getFilmText($_GET['edit']);
}

$filmtexts = getFilmTexts();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Textos de Películas</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestión de Textos de Películas</h1>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                <div class="card card-primary noGuest">
                    <div class="card-body">
                        <form method="post" action="">
                            <?php if(isset($filmtext_edit) && $filmtext_edit): ?>
                                <input type="hidden" name="is_edit" value="1">
                            <?php endif; ?>
                            <div class="form-group">
                                <label>ID Película:</label>
                                <input type="number" name="film_id" class="form-control" required value="<?php echo $filmtext_edit['film_id'] ?? ''; ?>" <?php echo isset($filmtext_edit) ? 'readonly' : ''; ?>>
                                <?php if(isset($filmtext_edit)): ?>
                                <small class="form-text text-muted">El ID de película no se puede cambiar una vez creado.</small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="title" class="form-control" required value="<?php echo $filmtext_edit['title'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea name="description" class="form-control" rows="4"><?php echo $filmtext_edit['description'] ?? ''; ?></textarea>
                            </div>
                            <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                            <?php if(isset($filmtext_edit)): ?>
                                <a href="film_text.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Textos de Películas</h3>
                    </div>
                    <div class="card-body">
                        <?php if(empty($filmtexts)): ?>
                            <div class="alert alert-info">
                                No se encontraron textos de películas o hubo un problema al conectar con la API.
                            </div>
                        <?php else: ?>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID Película</th>
                                        <th>Título</th>
                                        <th>Descripción</th>
                                        <th class="noGuest">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($filmtexts as $filmtext): ?>
                                        <tr>
                                            <td><?php echo $filmtext['film_id'] ?? ''; ?></td>
                                            <td><?php echo $filmtext['title'] ?? ''; ?></td>
                                            <td><?php echo $filmtext['description'] ?? ''; ?></td>
                                            <td class="noGuest">
                                                <a href="?edit=<?php echo $filmtext['film_id'] ?? ''; ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="film_id" value="<?php echo $filmtext['film_id'] ?? ''; ?>">
                                                    <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este texto de película?');">
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
