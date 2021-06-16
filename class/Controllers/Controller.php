<?php
namespace App\Controllers;

use App\Tools\AppSession;

class Controller{


    protected $manager;
    protected $managerName;

    public function __construct()
    {
        $this->manager = new $this->managerName();
        $this->session = new AppSession();
    }

}



?>