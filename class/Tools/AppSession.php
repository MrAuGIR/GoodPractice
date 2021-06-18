<?php


namespace App\Tools;

class AppSession{

    public $flash = [];
    private static $currentController = "";
    private static $currentTask = "";

    public function __construct()
    {
        $this->start();
    }

    public static function getCurrentController():string
    {
        return self::$currentController;
    }

    public static function getCurrentTask(): string
    {
        return self::$currentTask;
    }

    public static function setCurrentController(string $controller)
    {
        self::$currentController = $controller;
    }

    public static function setCurrentTask(string $task)
    {
        self::$currentTask = $task;
    }

    public function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    
    public function set(string $key, string $message)
    {
        $_SESSION[$key] = $message;
        return $this;
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key]?? $default;
    }

    public function getFlashMessage():array
    {
        foreach($_SESSION as $key => $value){
            if($key == 'user') continue;
            if(in_array($key, ['success','danger','warning'])){

                $this->flash[$key] = $value;
                unset($_SESSION[$key]);

            }
        }
        return $this->flash;
    }

    public function redirect(string $url)
    {
        header('location: '.$url);
        exit();
    }

    public static function updateCurrentPage(string $controller, string $task){
        self::setCurrentController($controller);
        self::setCurrentTask($task);
    }

    public static function isPageActive(string $template):string
    {
        
        if(self::$currentTask === $template){
                return true;
        }

        if($template === 'admin' ){
            if(in_array(self::$currentTask, ['add','edit','delete','adminArticle'])){
                return true;
            }
        }

        return false;
    
    }

}