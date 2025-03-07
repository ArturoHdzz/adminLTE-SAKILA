<?php
$api_url = "http://64.23.250.130/api/payments/";

function getPayments() {
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

function getPayment($id) {
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

function createPayment($amount, $payment_date, $customer, $staff, $rental) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'amount' => $amount,
        'payment_date' => $payment_date,
        'last_update' => $current_datetime,
        'customer' => $customer,
        'staff' => $staff,
        'rental' => $rental
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

function updatePayment($id, $amount, $payment_date, $customer, $staff, $rental) {
    global $api_url;
    $current_datetime = gmdate('Y-m-d\TH:i:s\Z');
    
    $data = array(
        'amount' => $amount,
        'payment_date' => $payment_date,
        'last_update' => $current_datetime,
        'customer' => $customer,
        'staff' => $staff,
        'rental' => $rental
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

function deletePayment($id) {
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
$payment_edit = null;

if (isset($_POST['delete']) && isset($_POST['payment_id'])) {
    $delete_result = deletePayment($_POST['payment_id']);
    if ($delete_result == 204) {
        $message = '<div class="alert alert-success">Pago eliminado correctamente</div>';
    } else {
        $message = '<div class="alert alert-danger">Error al eliminar el pago</div>';
    }
}

if (isset($_POST['save'])) {
    $amount = floatval(str_replace(',', '.', $_POST['amount']));
    $payment_date = $_POST['payment_date'];
    $customer = intval($_POST['customer']);
    $staff = intval($_POST['staff']);
    $rental = intval($_POST['rental']);
    
    if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $payment_date)) {
        try {
            $date = new DateTime($payment_date);
            $payment_date = $date->format('Y-m-d\TH:i:s\Z');
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger">Formato de fecha inválido. Use el formato: YYYY-MM-DDTHH:MM:SSZ</div>';
        }
    }
    
    if ($amount <= 0) {
        $message = '<div class="alert alert-danger">El monto debe ser un número positivo válido.</div>';
    } elseif ($customer <= 0 || $staff <= 0 || $rental <= 0) {
        $message = '<div class="alert alert-danger">Los IDs de cliente, personal y alquiler son obligatorios y deben ser números válidos.</div>';
    } else {
        if (isset($_POST['payment_id']) && !empty($_POST['payment_id'])) {
            $update_result = updatePayment($_POST['payment_id'], $amount, $payment_date, $customer, $staff, $rental);
            if (isset($update_result['amount']) && is_array($update_result['amount'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['amount'][0] . '</div>';
            } elseif (isset($update_result['payment_date']) && is_array($update_result['payment_date'])) {
                $message = '<div class="alert alert-danger">Error: ' . $update_result['payment_date'][0] . '</div>';
            } elseif ($update_result) {
                $message = '<div class="alert alert-success">Pago actualizado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al actualizar el pago</div>';
            }
        } else {
            $create_result = createPayment($amount, $payment_date, $customer, $staff, $rental);
            if (isset($create_result['amount']) && is_array($create_result['amount'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['amount'][0] . '</div>';
            } elseif (isset($create_result['payment_date']) && is_array($create_result['payment_date'])) {
                $message = '<div class="alert alert-danger">Error: ' . $create_result['payment_date'][0] . '</div>';
            } elseif ($create_result) {
                $message = '<div class="alert alert-success">Pago creado correctamente</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al crear el pago</div>';
            }
        }
    }
}

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $payment_edit = getPayment($_GET['edit']);
}

$payments = getPayments();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pagos</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../_navbar.php'; ?>
    <?php include '../_sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestión de Pagos</h1>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php echo $message; ?>
                <div class="card card-primary">
                    <div class="card-body">
                        <form method="post" action="">
                            <?php if(isset($payment_edit) && $payment_edit): ?>
                                <input type="hidden" name="payment_id" value="<?php echo $payment_edit['payment_id'] ?? ''; ?>">
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Monto:</label>
                                        <input type="text" name="amount" class="form-control" required value="<?php echo $payment_edit['amount'] ?? ''; ?>" placeholder="Ej: 9.99">
                                        <small class="form-text text-muted">Use punto como separador decimal (Ej: 9.99)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de Pago:</label>
                                        <input type="text" name="payment_date" class="form-control" required value="<?php echo $payment_edit['payment_date'] ?? gmdate('Y-m-d\TH:i:s\Z'); ?>" placeholder="YYYY-MM-DDTHH:MM:SSZ">
                                        <small class="form-text text-muted">Formato: YYYY-MM-DDTHH:MM:SSZ</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ID Cliente:</label>
                                        <input type="number" name="customer" class="form-control" required value="<?php echo $payment_edit['customer'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ID Personal:</label>
                                        <input type="number" name="staff" class="form-control" required value="<?php echo $payment_edit['staff'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ID Alquiler:</label>
                                        <input type="number" name="rental" class="form-control" required value="<?php echo $payment_edit['rental'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="save" class="btn btn-primary">Guardar</button>
                            <?php if(isset($payment_edit)): ?>
                                <a href="payment.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Pagos</h3>
                    </div>
                    <div class="card-body">
                        <?php if(empty($payments)): ?>
                            <div class="alert alert-info">
                                No se encontraron pagos o hubo un problema al conectar con la API.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Monto</th>
                                            <th>Fecha de Pago</th>
                                            <th>Última Actualización</th>
                                            <th>Cliente ID</th>
                                            <th>Personal ID</th>
                                            <th>Alquiler ID</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payments as $payment): ?>
                                            <tr>
                                                <td><?php echo $payment['payment_id'] ?? ''; ?></td>
                                                <td><?php echo $payment['amount'] ?? ''; ?></td>
                                                <td><?php echo isset($payment['payment_date']) ? date('d/m/Y H:i:s', strtotime($payment['payment_date'])) : ''; ?></td>
                                                <td><?php echo isset($payment['last_update']) ? date('d/m/Y H:i:s', strtotime($payment['last_update'])) : ''; ?></td>
                                                <td><?php echo $payment['customer'] ?? ''; ?></td>
                                                <td><?php echo $payment['staff'] ?? ''; ?></td>
                                                <td><?php echo $payment['rental'] ?? ''; ?></td>
                                                <td>
                                                    <a href="?edit=<?php echo $payment['payment_id'] ?? ''; ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                    <form method="post" style="display:inline;">
                                                        <input type="hidden" name="payment_id" value="<?php echo $payment['payment_id'] ?? ''; ?>">
                                                        <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este pago?');">
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