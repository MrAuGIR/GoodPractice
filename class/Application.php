<?php

namespace App;

use App\Controllers\ArticleController;
use App\Tools\AppSession;

class Application
{
    private static $routes = [
        'AdminController' => ['adminArticle', 'adminCategory','adminUser'],
        'UserController'  => ['login', 'register', 'logout','edit','delete'],
        'ArticleController' => ['index', 'show', 'list', 'add', 'delete', 'edit', 'postComment', 'deleteComment']
    ];


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

        if(!self::routeExist($controllerName,$task)){
            $controllerName = "ArticleController";
            $task = "default";
        }
        AppSession::updateCurrentPage($controllerName,$task);

        $controllerName = "App\Controllers\\" . $controllerName;
        
        $controller = new $controllerName();
        $controller->$task();
    }

    public static function secure(array $roleAllowed = null )
    {
        //si l'utilisateur n'est pas connecté
        if (empty($_SESSION['user']) || !isset($_SESSION['user'])) {

            header('location:?controller=article&action=index');
            exit();
        }
        //si le role de l'utilisateur est spécifié
        if($roleAllowed != null){
            if (!in_array($_SESSION['user']['role'], $roleAllowed)){
                header('location:?controller=article&action=index');
                exit();
            }
        }
        

        return $_SESSION['user'];
    }

    public static function routeExist($controllerName,$taskName)
    {
        if(array_key_exists($controllerName,self::$routes)){
            if(in_array($taskName,self::$routes[$controllerName])){
                return true;
            }
        }
        return false;
    }
 
}