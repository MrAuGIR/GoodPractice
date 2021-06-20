<?php
namespace App\Managers;

use App\Models\Article;
use App\Managers\ManagerCategory;
use \PDO;


class ManagerArticle extends Manager{

    private $_errors = [];
    
    /**
     * getErrors
     *
     * @return array
     */
    public function getErrors():array{
        return $this->_errors;
    }

    /**
     * methode ajout d'un article dans la base de donnée
     * @param Article $article
     */
    public function addArticle(array $article){
        $sql = 'INSERT INTO article (title, description, url,url_img, date_create, id_user, id_category) VALUES ( :title, :description, :url,:url_img, :date_create, :id_user, :id_category)';
        $req = $this->bdd->prepare($sql);
        $req->bindValue('title',$article['title'],PDO::PARAM_STR);
        $req->bindValue('description',$article['description'],PDO::PARAM_STR);
        $req->bindValue('date_create',$article['date_create'],PDO::PARAM_STR);
        $req->bindValue('url',$article['url'],PDO::PARAM_STR);
        $req->bindValue('url_img',$article['url_img'],PDO::PARAM_STR);
        $req->bindValue('id_user',$article['id_user'],PDO::PARAM_INT);
        $req->bindValue('id_category',$article['id_category'],PDO::PARAM_INT);
        $req->execute();
    }

    /**
     * methode suppression d'un objet Article
     * @param Article $article
     */
    public function deleteArticle(Article $article){
        $sql = 'DELETE FROM article WHERE id_article = :id';
        $req = $this->bdd->prepare($sql);
        $req->bindValue('id',$article->getId_article(),PDO::PARAM_INT);
        $result = $req->execute();
        return $result;
    }

    /**
     * method mise a jour d'un article
     * @param Article $article
     */
    public function updateArticle(Article $article){
        $sql = 'UPDATE article SET title = :title, description = :description, url = :url,url_img = :url_img, date_create = :date_create, id_user = :id_user, id_category = :id_category WHERE id_article = :id';
        $req = $this->bdd->prepare($sql);
        $req->bindValue('title',$article->getTitle(),PDO::PARAM_STR);
        $req->bindValue('description',$article->getDescription(),PDO::PARAM_STR);
        $req->bindValue('date_create',$article->getDate_create(),PDO::PARAM_STR);
        $req->bindValue('url',$article->getUrl(),PDO::PARAM_STR);
        $req->bindValue('url_img',$article->getUrl_img(),PDO::PARAM_STR);
        $req->bindValue('id_user',$article->getId_user(),PDO::PARAM_INT);
        $req->bindValue('id_category',$article->getId_category(),PDO::PARAM_INT);
        $req->bindValue('id',$article->getId_article(),PDO::PARAM_INT);
        $req->execute();
    }

    /**
     * methode recupere tous les articles dans la base de donnée
     * @return array $reponse
     */
    public function getAllArticle():array{
        $tabArticle=[];
        $sql = 'SELECT * FROM article';
        $req = $this->bdd->prepare($sql);
        $req->execute();
        $reponse = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach($reponse as $value){
            $article = new Article($value);
            $tabArticle[] = $article;
        }
        return $tabArticle;
    }

    /**
     * methode recupere un article en fonction de son id
     * @param int $id
     * @return Article $article
     */
    public function getArticleById(int $id):Article{
        $sql = 'SELECT * FROM article WHERE id_article = :id';
        $req = $this->bdd->prepare($sql);
        $req->bindValue('id',$id,PDO::PARAM_INT);
        $req->execute();
        $reponse = $req->fetch(PDO::FETCH_ASSOC);
        $article = new Article($reponse);
        return $article;
    }


    /**
     * methode recupere tous les articles en fonction de son utilisateur
     * @param int $id_user
     * @return array $tabArticle
     */
    public function getArticleByIdUser(int $id_user):array{
        $tabArticle=[];
        $sql = 'SELECT * FROM article WHERE id_user = :id';
        $req = $this->bdd->prepare($sql);
        $req->bindValue('id',$id_user,PDO::PARAM_INT);
        $req->execute();
        $reponse = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach($reponse as $value){
            $article = new Article($value);
            $tabArticle[] = $article;
        }
        return $tabArticle;
    }

    
    /**
     * getXarticle
     * retourn le nombre d'article demandé par ordre chronologique
     * @param  mixed $number
     * @return array
     */
    public function getXarticle(int $number):array{
        $tabArticle=[];
        $sql = 'SELECT * FROM article ORDER BY date_create DESC LIMIT 0,:number';
        $req = $this->bdd->prepare($sql);
        $req->bindParam('number',$number,PDO::PARAM_INT);
        $req->execute();
        $reponse = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach($reponse as $value){
            $article = new Article($value);
            $tabArticle[] = $article;
        }
        return $tabArticle;
    }
    
    /**
     * getArticleByFilter
     * Recherche d'article avec filtre
     * @param  mixed $idCategory
     * @return array
     */
    public function getArticleByFilter(int $idCategory):array
    {
        $tabArticle=[];
        $sql = 'SELECT * FROM article WHERE id_category = :idCategory ORDER BY date_create ASC';
        //$sql .= 'LIMIT 0,10';
        $req = $this->bdd->prepare($sql);
        $req->bindValue('idCategory',$idCategory,PDO::PARAM_INT);
      //  $req->bindValue('number',$number,PDO::PARAM_INT);
        $req->execute();

        $reponse = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach($reponse as $row){
            $article = new Article($row);
            $tabArticle[] = $article;
        }
        return $tabArticle;
        
    }

    
    /**
     * traitementDonnees
     * verification que les données posté sont présentes et correctes
     * 
     * @param  mixed $post
     * @param  mixed $type 'add' or 'update'
     * @return void
     */
    public function traitementDonnees(array $post,string $type){

        $title = isset($post['title'])? $post['title']:null;
        $content = isset($post['content'])? $post['content']: "";
        $url = isset($post['url'])? $post['url']: "";
        $pathImage = isset($post['image'])? $post['image']:"";
        $date = new \DateTime();
        $idUser = isset($post['idUser'])? $post['idUser']: null;
        $idArticle = isset($post['idArticle'])? $post['idArticle']: null;
        $idCategory = isset($post['category'])? $post['category']: 1;

        
        //erreur si les champs obligatoires sont vide
        if(!$title || !$content || !$idUser){
            $this->_errors['error'] = 'Veuillez remplir les champs obligatoires';
        }
        
        //si path image vide
        if(empty($pathImage)){
            if(!empty($idCategory)){
                $manager = new ManagerCategory();
                $category = $manager->getCategoryById($idCategory);
                
                $pathImage = PATH_IMG_DEFAULT.$category->getDefault_image();
            }else{
                $pathImage = '/img/illustration/illustrationDefault.png';
            }
            
        }

        //si pas d'erreur on continu
        if(empty($this->getErrors())){
            if($type === 'add'){

                $this->addArticle([
                    'title' => $title,
                    'description' => $content,
                    'url' => $url,
                    'url_img' => $pathImage,
                    'date_create' => $date->format('Y-m-d H:i'),
                    'id_user' => $idUser,
                    'id_category' => $idCategory
                ]);

                return false;
            }
            //type = 'update
            $article = new Article([
                'id_article' => $idArticle,
                'title' => $title,
                'description' => $content,
                'url' => $url,
                'url_img' => $pathImage,
                'date_create' => $date->format('Y-m-d H:i'),
                'id_user' => $idUser,
                'id_category' => $idCategory
            ]);
            $this->updateArticle($article);
            
            return false;

        }

        return $this->getErrors();

    }

    public function getNumCommnents(int $id_article):int
    {
        $sql  = "SELECT COUNT(c.id_commentary) AS total";
        $sql .= " FROM article AS a ";
        $sql .= " INNER JOIN commentary AS c ON a.id_article = c.id_article";
        $sql .= " WHERE c.id_article = :id"; 

        $req = $this->bdd->prepare($sql);
        $req->bindValue('id',$id_article,PDO::PARAM_INT);
        $req->execute();

        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}



?>