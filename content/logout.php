<?php
    if(session_status()===PHP_SESSION_NONE){
        session_start();
    }

    unset($_SESSION['user']);

    header('location: /bonne_pratique/index.php?logout=ok');
    exit();



?>