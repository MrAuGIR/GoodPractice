<?php

namespace App;

use App\Tools\AppSession;
use Symfony\Component\Routing\Route;
use App\Controllers\ArticleController;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class Application
{
    private static $routes = [
        'AdminController' => ['adminArticle', 'adminCategory','adminUser'],
        'UserController'  => ['login', 'register', 'logout','edit','delete'],
        'ArticleController' => ['index', 'show', 'list', 'add', 'delete', 'edit', 'postComment', 'deleteComment']
    ];


    public static function process(Request $request)
    {
        $routes = new RouteCollection;
        $routes->add('index', new Route('/{controller}/{task}',['controller' => "ArticleController", "task" => "index"]));

        $context = new RequestContext();
        $context->fromRequest($request);
        // $controllerName = "ArticleController";
        // $task = "index";
        $pathinfo = $request->getPathInfo();
        $urlMatcher = new UrlMatcher($routes,$context);

        
        $resultat = $urlMatcher->match($pathinfo);
        
        $controllerName = ucFirst($resultat['controller']).'Controller';
        $task = $resultat['task'];

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