<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('vendor/autoload.php');

// objet user
//$user = isset($_SESSION['user']) ? unserialize($_SESSION['user']) : "";

App\Application::process();



?>