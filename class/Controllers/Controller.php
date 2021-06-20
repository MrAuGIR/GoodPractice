<?php
namespace App\Controllers;

use App\Render;
use App\Tools\AppSession;

abstract class Controller{


    protected $manager;
    protected $managerName;

    public function __construct()
    {
        $this->manager = new $this->managerName();
        $this->session = new AppSession();
    }

    public function default(){
        Render::render('default/error404', ['title' => '404 not found']);
    }

}



?>