<?php
$api_url = "http://64.23.250.130/api/film-categories/";

function getAuthHeaders() {
    $token = $_COOKIE['access_token'] ?? '';
    return !empty($token) ? [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ] : ['Content-Type: application/json'];
}
function getFilmCategories($url = null) {
    global $api_url;
    $url = $url ? $url : $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
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

function getFilmCategory($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

function createFilmCategory($film, $category) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'film' => $film,
        'category' => $category,
        'last_update' => $current_datetime
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

function updateFilmCategory($id, $film, $category) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'film' => $film,
        'category' => $category,
        'last_update' => $current_datetime
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

function deleteFilmCategory($id) {
    global $api_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . $id . "/");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders());
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
$filmcategory_edit = null;

if (isset($_POST['delete']) && isset($_POST['id'])) {
    $delete_result = deleteFilmCategory($_POST['id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Relación Película-Categoría eliminada correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar la relación Película-Categoría</div>';
    }
}

if (isset($_POST['save'])) {
    $film = intval($_POST['film']);
    $category = intval($_POST['category']);
    
    if ($film <= 0 || $category <= 0) {
        $message = '<div class="alert alert-danger">Los IDs de película y categoría son obligatorios y deben ser números válidos.</div>';
    } else {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $update_result = updateFilmCategory($_POST['id'], $film, $category);
            if (isset($update_result['film']) && is_array($update_result['film'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['film'][0] . '</div>';
            } else if ($update_result) {
                $message = '<div class="alert alert-success">Relación Película-Categoría actualizada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar la relación Película-Categoría</div>';
            }
        } else {
            $create_result = createFilmCategory($film, $category);
            if (isset($create_result['film']) && is_array($create_result['film'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['film'][0] . '</div>';
            } else if ($create_result) {
                $message = '<div class="alert alert-success">Relación Película-Categoría creada correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear la relación Película-Categoría</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $filmcategory_edit = getFilmCategory($_GET['edit']);
}

$page_url = isset($_GET['page_url']) ? urldecode($_GET['page_url']) : null;
$filmcategories = getFilmCategories($page_url);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías de Películas</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestión de Categorías de Películas</h1>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                <div class="card card-primary noGuest">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo isset($filmcategory_edit) ? 'Editar' : 'Agregar'; ?> Categoría de Película</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <?php if(isset($filmcategory_edit) && $filmcategory_edit): ?>
                                <input type="hidden" name="id" value="<?php echo $filmcategory_edit['id'] ?? ''; ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label>ID Película:</label>
                                <input type="number" name="film" class="form-control" required value="<?php echo $filmcategory_edit['film'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>ID Categoría:</label>
                                <input type="number" name="category" class="form-control" required value="<?php echo $filmcategory_edit['category'] ?? ''; ?>">
                            </div>
                            <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                            <?php if(isset($filmcategory_edit)): ?>
                                <a href="film_category.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Categorías de Películas</h3>
                    </div>
                    <div class="card-body">
                        <?php if(empty($filmcategories['results'])): ?>
                            <div class="alert alert-info">
                                No se encontraron categorías de películas o hubo un problema al conectar con la API.
                            </div>
                        <?php else: ?>
                            <table class="table table-bordered table-striped">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination">
                                        <li class="page-item disabled"><a class="page-link">Cantidad: <?php echo $filmcategories['count'] ?></a></li>
                                        <li class="page-item <?php echo $filmcategories['previous'] ? '' : 'disabled' ?>">
                                            <a class="page-link" href="?page_url=<?php echo urlencode($filmcategories['previous']); ?>"><<</a>
                                        </li>
                                        <li class="page-item <?php echo $filmcategories['next'] ? '' : 'disabled' ?>">
                                            <a class="page-link" href="?page_url=<?php echo urlencode($filmcategories['next']); ?>">>></a>
                                        </li>
                                    </ul>
                                </nav>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Película ID</th>
                                        <th>Categoría ID</th>
                                        <th>Última Actualización</th>
                                        <th class="noGuest">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($filmcategories['results'] as $filmcategory): ?>
                                        <tr>
                                            <td><?php echo $filmcategory['id'] ?? ''; ?></td>
                                            <td><?php echo $filmcategory['film'] ?? ''; ?></td>
                                            <td><?php echo $filmcategory['category'] ?? ''; ?></td>
                                            <td><?php echo isset($filmcategory['last_update']) ? date('d/m/Y H:i:s', strtotime($filmcategory['last_update'])) : ''; ?></td>
                                            <td class="noGuest">
                                                <a href="?edit=<?php echo $filmcategory['id'] ?? ''; ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?php echo $filmcategory['id'] ?? ''; ?>">
                                                    <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta categoría?');">
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
