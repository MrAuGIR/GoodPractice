<?php
if(session_status()===PHP_SESSION_NONE){
    session_start();
}

//si l'utilisateur n'est pas connecté
if(empty($_SESSION['user']) || !isset($_SESSION['user'])){

    //dirname(__DIR__).DIRECTORY_SEPARATOR.'index.php';
    header('location: ../index.php');
    die();
    
}


?>