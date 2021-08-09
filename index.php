<?php

use Symfony\Component\HttpFoundation\Request;

require_once(__DIR__ .DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');

$session = new App\Tools\AppSession();
$session->start();

$request = Request::createFromGlobals();

App\Application::process($request);



?>