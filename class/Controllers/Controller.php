<?php
namespace App\Controllers;

class Controller{


    protected $manager;
    protected $managerName;

    public function __construct()
    {
        $this->manager = new $this->managerName();
    }

}



?>