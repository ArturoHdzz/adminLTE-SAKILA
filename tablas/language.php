<?php
$api_url = "http://64.23.250.130/api/languages/";

function getLanguages() {
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

function getLanguage($id) {
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

function createLanguage($name) {
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
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

function updateLanguage($id, $name) {
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
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

function deleteLanguage($id) {
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
$language_edit = null;

if (isset($_POST['delete']) && isset($_POST['language_id'])) {
    $delete_result = deleteLanguage($_POST['language_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Idioma eliminado correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar el idioma</div>';
    }
}

if (isset($_POST['save'])) {
    $name = trim($_POST['name']);
    
    if (empty($name)) {
        $message = '<div class="alert alert-danger">El nombre del idioma es obligatorio.</div>';
    } else {
        if (isset($_POST['language_id']) && !empty($_POST['language_id'])) {
            $update_result = updateLanguage($_POST['language_id'], $name);
            if (isset($update_result['name']) && is_array($update_result['name'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['name'][0] . '</div>';
            } else if ($update_result) {
                $message = '<div class="alert alert-success">Idioma actualizado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar el idioma</div>';
            }
        } else {
            $create_result = createLanguage($name);
            if (isset($create_result['name']) && is_array($create_result['name'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['name'][0] . '</div>';
            } else if ($create_result) {
                $message = '<div class="alert alert-success">Idioma creado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear el idioma</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $language_edit = getLanguage($_GET['edit']);
}

$languages = getLanguages();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Idiomas</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestión de Idiomas</h1>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                <div class="card card-primary">
                    <div class="card-body">
                        <form method="post" action="">
                            <?php if(isset($language_edit) && $language_edit): ?>
                                <input type="hidden" name="language_id" value="<?php echo $language_edit['language_id'] ?? ''; ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label>Nombre del Idioma:</label>
                                <input type="text" name="name" class="form-control" required value="<?php echo $language_edit['name'] ?? ''; ?>">
                            </div>
                            <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                            <?php if(isset($language_edit)): ?>
                                <a href="language.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Idiomas</h3>
                    </div>
                    <div class="card-body">
                        <?php if(empty($languages)): ?>
                            <div class="alert alert-info">
                                No se encontraron idiomas o hubo un problema al conectar con la API.
                            </div>
                        <?php else: ?>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Última Actualización</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($languages as $language): ?>
                                        <tr>
                                            <td><?php echo $language['language_id'] ?? ''; ?></td>
                                            <td><?php echo $language['name'] ?? ''; ?></td>
                                            <td><?php echo isset($language['last_update']) ? date('d/m/Y H:i:s', strtotime($language['last_update'])) : ''; ?></td>
                                            <td>
                                                <a href="?edit=<?php echo $language['language_id'] ?? ''; ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="language_id" value="<?php echo $language['language_id'] ?? ''; ?>">
                                                    <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este idioma?');">
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