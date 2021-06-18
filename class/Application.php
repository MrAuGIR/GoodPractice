<?php

namespace App;

use App\Controllers\ArticleController;
use App\Tools\AppSession;

// class Application
// {


//     public static function process()
//     {

//         $controllerName = 'ArticleController';
//         $action = 'index';

//         if (!empty($_GET['controller'])) {
//             $controllerName = ucfirst($_GET['controller']);
//         }

//         if (!empty($_GET['action'])) {
//             $action = $_GET['action'];
//         }

//         $controllerName = "Controllers\".$controllerName;

//         $controller = new $controllerName();
//         $controller->$action();

//     }
// }


class Application
{

    public static function process()
    {
        $controllerName = "ArticleController";
        $task = "index";

        if (!empty($_GET['controller'])) {
            $controllerName = ucFirst($_GET['controller']).'Controller';
        }

        if (!empty($_GET['action'])) {
            $task = $_GET['action'];
        }

        AppSession::updateCurrentPage($controllerName,$task);

        $controllerName = "App\Controllers\\" . $controllerName;

        $controller = new $controllerName();
        $controller->$task();
    }

    public static function secure()
    {
        //si l'utilisateur n'est pas connecté
        if (empty($_SESSION['user']) || !isset($_SESSION['user'])) {

            header('location:?controller=article&action=index');
            die();
        }

        return $_SESSION['user'];
    }

    
}