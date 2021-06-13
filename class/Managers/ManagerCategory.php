<?php
namespace App\Managers;

use App\Managers\Manager;
use \PDO;
use App\Models\Category;
use COM;

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

    public function getCategoryById(int $id):Category{
        $sql = 'SELECT * FROM categorie WHERE id_category = :id';
        $req = $this->bdd->prepare($sql);
        $req->bindValue('id',$id,PDO::PARAM_INT);
        $req->execute();
        $reponse = $req->fetch(PDO::FETCH_ASSOC);
        return new Category($reponse);
        
    }
    
    /**
     * getBestCategories
     *
     * @param  int $number  number of best category to return
     * @return Category[]
     */
    public function getBestCategories(int $number = 5){
        
        $arrayResult = [];

        $sql  = 'SELECT categorie.id_category, COUNT(a.id_article) FROM categorie ';
        $sql .= 'INNER JOIN article AS a ON a.id_category = categorie.id_category ';
        $sql .= 'GROUP BY categorie.id_category ';
        $sql .= 'LIMIT 0, :limit';

        $req = $this->bdd->prepare($sql);
        $req->bindParam('limit',$number,PDO::PARAM_INT);
        $req->execute();
        $reponse = $req->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($reponse as $line){

            $arrayResult[] = $this->getCategoryById($line['id_category']);
        }
        return $arrayResult;
    }

}