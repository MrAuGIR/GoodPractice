<?php

namespace App;

use App\Controllers\ArticleController;

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
            $controllerName = ucFirst($_GET['controller']);
        }

        if (!empty($_GET['action'])) {
            $task = $_GET['action'];
        }


        $controllerName = "App\Controllers\\" . $controllerName;

        $controller = new $controllerName();
        $controller->$task();
    }
}