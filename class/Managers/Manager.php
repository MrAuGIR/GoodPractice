<?php
namespace App\Managers;

use App\Db;
use \PDO;

class Manager{

    protected $bdd;

    public function __construct()
    {
        $this->bdd = Db::getInstance();
    }

    /**
     * getBdd
     *
     * @return PDO
     */
    public function getBdd(): PDO
    {
        return $this->bdd;
    }

    /**
     * setBdd
     *
     * @param  mixed $pdo
     * @return void
     */
    public function setBdd(PDO $bdd)
    {
        $this->bdd = $bdd;
        return $this;
    }


}