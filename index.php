<?php
  // if the user is not logged in, redirect to the login page
  session_start();
  if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
  }
 
?>
