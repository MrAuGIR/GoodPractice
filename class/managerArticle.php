<?php

require dirname(__DIR__). DIRECTORY_SEPARATOR .'class'.DIRECTORY_SEPARATOR.'Article.php';

class ManagerArticle{

    private $_bdd; //PDO instance

    /**
     * Constructeur class managerArticle
     * @param PDO $bdd
     */
    public function __construct(PDO $bdd)
    {
        $this->setDb($bdd);
    }

    /**
     * methode setDb set attribut pdo instance
     * @param PDO $bdd
     */
    public function setDb(PDO $bdd):void{
        $this->_bdd = $bdd;
    }

    /**
     * methode ajout d'un article dans la base de donnée
     * @param Article $article
     */
    public function addArticle(Article $article){
        $sql = 'INSERT INTO article (title, description, url,url_img, date_create, id_user, id_category) VALUES ( :title, :description, :url,:url_img, :date_create, :id_user, :id_category)';
        $req = $this->_bdd->prepare($sql);
        $req->bindValue('title',$article->getTitle(),PDO::PARAM_STR);
        $req->bindValue('description',$article->getDescription(),PDO::PARAM_STR);
        $req->bindValue('date_create',$article->getDate_create(),PDO::PARAM_STR);
        $req->bindValue('url',$article->getUrl(),PDO::PARAM_STR);
        $req->bindValue('url_img',$article->getUrl_img(),PDO::PARAM_STR);
        $req->bindValue('id_user',$article->getId_user(),PDO::PARAM_INT);
        $req->bindValue('id_category',$article->getId_category(),PDO::PARAM_INT);
        $req->execute();
    }

    /**
     * methode suppression d'un objet Article
     * @param Article $article
     */
    public function deleteArticle(Article $article){
        $sql = 'DELETE FROM article WHERE id_article = :id';
        $req = $this->_bdd->prepare($sql);
        $req->bindValue('id',$article->getId_article(),PDO::PARAM_INT);
        $req->execute();
    }

    /**
     * method mise a jour d'un article
     * @param Article $article
     */
    public function updateArticle(Article $article){
        $sql = 'UPDATE article SET title = :title, description = :description, url = :url,url_img = :url_img, date_create = :date_create, id_user = :id_user, id_category = :id_category WHERE id_article = :id';
        $req = $this->_bdd->prepare($sql);
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
        $req = $this->_bdd->prepare($sql);
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
        $req = $this->_bdd->prepare($sql);
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
        $req = $this->_bdd->prepare($sql);
        $req->bindValue('id',$id_user,PDO::PARAM_INT);
        $req->execute();
        $reponse = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach($reponse as $value){
            $article = new Article($value);
            $tabArticle[] = $article;
        }
        return $tabArticle;
    }


    public function getXarticle(int $number):array{
        $tabArticle=[];
        $sql = 'SELECT * FROM article ORDER BY date_create ASC LIMIT 0,3';
        $req = $this->_bdd->prepare($sql);
        //$req->bindValue('num',$number,PDO::PARAM_INT);
        $req->execute();
        $reponse = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach($reponse as $value){
            $article = new Article($value);
            $tabArticle[] = $article;
        }
        return $tabArticle;
    }
}



?>