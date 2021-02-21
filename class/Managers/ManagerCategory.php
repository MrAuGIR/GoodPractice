<?php
namespace App\Managers;

use App\Managers\Manager;
use \PDO;
use App\Models\Category;

class ManagerCategory extends Manager{


    /**
     * methode recupere toutes les categorie dans la base de donnée
     * @return array $reponse
     */
    public function getAllCategory(): array
    {
        $tabCategory = [];
        $sql = 'SELECT * FROM categorie';
        $req = $this->bdd->prepare($sql);
        $req->execute();
        $reponse = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reponse as $value) {
            $category = new Category($value);
            $tabCategory[] = $category;
        }
        return $tabCategory;
    }

}