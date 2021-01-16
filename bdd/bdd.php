<?php

require_once 'config.php';

try{
    $bdd =new PDO('mysql:host='.HOST.'; dbname='.DB_NAME.';charset=utf8',USER_NAME, PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

}
catch(exception $e){
    echo 'Erreur :'.$e->getMessage();
}



?>