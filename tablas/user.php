
<?php
$api_url = "http://64.23.250.130/api/";

function login($email, $password) {
    global $api_url;

    $data = json_encode(array(
        'email' => $email,
        'password' => $password
    ));

    $ch = curl_init($api_url . 'login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json'
    ));

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $response_data = json_decode($response, true);

        // Aquí guardamos en sesión
        session_start();
        $_SESSION['access_token'] = $response_data['access'];
        $_SESSION['refresh_token'] = $response_data['refresh'];
        $_SESSION['user'] = $response_data['user'];

        return true; // éxito
    } else {
        return false; // error
    }
}


