<?php


namespace App\Tools;

class AppSession{

    public $flash = [];

    public function __construct()
    {
        $this->start();
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
            if(in_array($key, ['success','alert','warning'])){

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

}