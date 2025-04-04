<?php
$api_url = "http://64.23.250.130/api/films/";

function getFilms($url = null) {
    global $api_url;
    $url = $url ? $url : $api_url;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function getFilm($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function createFilm($title, $description, $release_year, $rental_duration, $rental_rate, 
                    $length, $replacement_cost, $rating, $language) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'title' => $title,
        'description' => $description,
        'release_year' => $release_year,
        'rental_duration' => intval($rental_duration),
        'rental_rate' => floatval($rental_rate),
        'length' => intval($length),
        'replacement_cost' => floatval($replacement_cost),
        'rating' => $rating,
        'last_update' => $current_datetime,
        'language' => intval($language),
        'original_language' => null
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

function updateFilm($id, $title, $description, $release_year, $rental_duration, $rental_rate, 
                    $length, $replacement_cost, $rating, $language) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $existing_film = getFilm($id);
    
    $data = array(
        'title' => $title,
        'description' => $description,
        'release_year' => $release_year,
        'rental_duration' => intval($rental_duration),
        'rental_rate' => floatval($rental_rate),
        'length' => intval($length),
        'replacement_cost' => floatval($replacement_cost),
        'rating' => $rating,
        'last_update' => $current_datetime,
        'language' => intval($language),
        'original_language' => $existing_film['original_language']
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

function deleteFilm($id) {
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
$film_edit = null;

if (isset($_POST['delete']) && isset($_POST['film_id'])) {
    $delete_result = deleteFilm($_POST['film_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Película eliminada correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar la película</div>';
    }
}

if (isset($_POST['save'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $release_year = trim($_POST['release_year']);
    $rental_duration = trim($_POST['rental_duration']);
    $rental_rate = trim($_POST['rental_rate']);
    $length = trim($_POST['length']);
    $replacement_cost = trim($_POST['replacement_cost']);
    $rating = trim($_POST['rating']);
    $language = trim($_POST['language']);
    
    if (empty($title)) {
        $message = '<div class="alert alert-danger">El campo Título es obligatorio.</div>';
    } else {
        if (isset($_POST['film_id']) && !empty($_POST['film_id'])) {
            $update_result = updateFilm(
                $_POST['film_id'], $title, $description, $release_year, $rental_duration, 
                $rental_rate, $length, $replacement_cost, $rating, $language
            );
            if ($update_result) {
                $message = '<div class="alert alert-success">Película actualizada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar la película</div>';
            }
        } else {
            $create_result = createFilm(
                $title, $description, $release_year, $rental_duration, $rental_rate, 
                $length, $replacement_cost, $rating, $language
            );
            if ($create_result) {
                $message = '<div class="alert alert-success">Película creada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear la película</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $film_edit = getFilm($_GET['edit']);
}

$page_url = isset($_GET['page_url']) ? urldecode($_GET['page_url']) : null;
$films = getFilms($page_url);

$rating_options = array('G', 'PG', 'PG-13', 'R', 'NC-17');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Películas</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Gestión de Películas</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                <div class="card card-primary">
                    <div class="card-body">
                        <form method="post" action="">
                            <input type="hidden" name="film_id" value="<?php echo $film_edit['film_id'] ?? ''; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Título:</label>
                                        <input type="text" name="title" class="form-control" required value="<?php echo $film_edit['title'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Año de Lanzamiento:</label>
                                        <input type="number" name="release_year" class="form-control" value="<?php echo $film_edit['release_year'] ?? date('Y'); ?>" min="1900" max="2099">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea name="description" class="form-control" rows="3"><?php echo $film_edit['description'] ?? ''; ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Duración de Alquiler (días):</label>
                                        <input type="number" name="rental_duration" class="form-control" value="<?php echo $film_edit['rental_duration'] ?? '3'; ?>" min="1">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Tarifa de Alquiler ($):</label>
                                        <input type="number" name="rental_rate" class="form-control" value="<?php echo $film_edit['rental_rate'] ?? '4.99'; ?>" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Costo de Reemplazo ($):</label>
                                        <input type="number" name="replacement_cost" class="form-control" value="<?php echo $film_edit['replacement_cost'] ?? '19.99'; ?>" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Duración (minutos):</label>
                                        <input type="number" name="length" class="form-control" value="<?php echo $film_edit['length'] ?? '90'; ?>" min="1">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Calificación:</label>
                                        <select name="rating" class="form-control">
                                            <?php foreach ($rating_options as $option): ?>
                                                <option value="<?php echo $option; ?>" <?php echo (isset($film_edit['rating']) && $film_edit['rating'] == $option) ? 'selected' : ''; ?>><?php echo $option; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Idioma:</label>
                                        <input type="number" name="language" class="form-control" value="<?php echo $film_edit['language'] ?? '1'; ?>" min="1">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                                <?php if ($film_edit): ?>
                                    <a href="film.php" class="btn btn-secondary">Cancelar</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Películas</h3>
                    </div>
                    <div class="card-body">
                        <?php if(empty($films['results'])): ?>
                            <div class="alert alert-info">
                                No se encontraron peliculas o hubo un problema al conectar con la API.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination">
                                            <li class="page-item disabled"><a class="page-link">Cantidad: <?php echo $films['count'] ?></a></li>
                                            <li class="page-item <?php echo $films['previous'] ? '' : 'disabled' ?>">
                                                <a class="page-link" href="?page_url=<?php echo urlencode($films['previous']); ?>"><<</a>
                                            </li>
                                            <li class="page-item <?php echo $films['next'] ? '' : 'disabled' ?>">
                                                <a class="page-link" href="?page_url=<?php echo urlencode($films['next']); ?>">>></a>
                                            </li>
                                        </ul>
                                    </nav>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Año</th>
                                            <th>Duración</th>
                                            <th>Calificación</th>
                                            <th>Tarifa</th>
                                            <th>Última Actualización</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                           <?php foreach ($films['results'] as $film): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($film['film_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($film['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($film['release_year']); ?></td>
                                                    <td><?php echo htmlspecialchars($film['length']); ?> min</td>
                                                    <td><?php echo htmlspecialchars($film['rating']); ?></td>
                                                    <td><?php echo htmlspecialchars($film['rental_rate']); ?></td>
                                                    <td><?php echo htmlspecialchars($film['last_update']); ?></td>
                                                    <td>
                                                        <a href="?edit=<?php echo $film['film_id']; ?>" class="btn btn-info btn-sm">Editar</a>
                                                        <form method="post" style="display:inline;">
                                                            <input type="hidden" name="film_id" value="<?php echo $film['film_id']; ?>">
                                                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Eliminar</button>
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
            </div>
        </section>
    </div>
</div>
<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<?php include '../_footer.php'; ?>
</body>
</html>