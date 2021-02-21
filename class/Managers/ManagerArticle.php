<?php
namespace App\Managers;

use App\Models\Article;
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
        $req->bindValue('description',$article['content'],PDO::PARAM_STR);
        $req->bindValue('date_create',$article['date'],PDO::PARAM_STR);
        $req->bindValue('url',$article['url'],PDO::PARAM_STR);
        $req->bindValue('url_img',$article['image'],PDO::PARAM_STR);
        $req->bindValue('id_user',$article['idUser'],PDO::PARAM_INT);
        $req->bindValue('id_category',$article['category'],PDO::PARAM_INT);
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
        $req->execute();
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
        $sql = 'SELECT * FROM article WHERE id_article = :id';
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
        $sql = 'SELECT * FROM article ORDER BY date_create ASC LIMIT 0,3';
        $req = $this->bdd->prepare($sql);
        //$req->bindValue('num',$number,PDO::PARAM_INT);
        $req->execute();
        $reponse = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach($reponse as $value){
            $article = new Article($value);
            $tabArticle[] = $article;
        }
        return $tabArticle;
    }


    public function traitementDonnees(array $post){

        $title = isset($post['title'])? $post['title']:"";
        $content = isset($post['content'])? $post['content']: "";
        $url = isset($post['url'])? $post['url']: "";
        $pathImage = isset($post['image'])? $post['image']:"";
        $date = new \DateTime();
        $idUser = isset($post['idUser'])? $post['idUser']: null;
        $idCategory = isset($post['category'])? $post['category']: 1;

        //erreur si les champs obligatoires sont vide
        if(empty($title) || empty($content) || empty($idUser)){
            $this->__errors['vide'] = 'Veuillez remplir les champs obligatoires';
        }

        //si path image vide
        if(empty($pathImage)){
            $pathImage = '/img/illustration/default.png';
        }

        //si pas d'erreur on continu
        if(!$this->_errors){
            $this->addArticle(['title'=>$title,
            'content'=>$content, 
            'url'=>$url, 
            'image'=>$pathImage, 
            'date'=>$date->format('Y-m-d H:i'),
            'idUser'=>$idUser,
            'category'=>$idCategory]);

            return false;

        }

        return $this->getErrors();

        


    }
}



?>