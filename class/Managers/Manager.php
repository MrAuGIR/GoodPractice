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
    protected function getBdd(): PDO
    {
        return $this->bdd;
    }

    /**
     * setBdd
     *
     * @param  mixed $pdo
     * @return void
     */
    protected function setBdd(PDO $bdd)
    {
        $this->bdd = $bdd;
        return $this;
    }

    protected function cleanString(string $data): string
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }


}