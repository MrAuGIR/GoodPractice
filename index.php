<?php

require_once(__DIR__ .DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');

$session = new App\Tools\AppSession();
$session->start();


App\Application::process();



?>