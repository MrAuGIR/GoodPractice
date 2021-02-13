<?php
namespace App\Managers;
use \PDO;
use App\Models\Commentary;
/**
 * class manager commentary
 */
class ManagerCommentary{

    private $_bdd ; // instance PDO

    /**
     * constructeur de la class ManagerCommentary
     * @param PDO $bdd
     */
    public function __construct(PDO $bdd)
    {
        $this->setDb($bdd);
    }


    /**
     * Methode set attribut $_bdd de l'objet managerCommentary
     */
    public function setDb(PDO $bdd){
        $this->_bdd = $bdd;
    }

    /**
     * méthode ajout d'un commentaire dans la base de donnée
     * @param Commentary $comment
     */
    public function addCommentary(Commentary $comment){
        $sql = 'INSERT INTO commentary(title, description, date_posted, approved, disapproved, id_article, id_user) VALUES (:title, :description, :date_posted, :approved, :disapproved, :id_article, :id_user)';
        $req = $this->_bdd->prepare($sql);
        $req->bindValue('title', $comment->getTitle(),PDO::PARAM_STR);
        $req->bindValue('description', $comment->getDescription(),PDO::PARAM_STR);
        $req->bindValue('date_posted', $comment->getDate_posted(),PDO::PARAM_STR);
        $req->bindValue('approved', $comment->getApproved(),PDO::PARAM_INT);
        $req->bindValue('disapproved', $comment->getDisapproved(),PDO::PARAM_INT);
        $req->bindValue('id_article', $comment->getId_article(),PDO::PARAM_INT);
        $req->bindValue('id_user', $comment->getId_user(),PDO::PARAM_INT);
        $req->execute();
    }


    /**
     * méthode suppression d'un commentaire
     * @param Commentary $comment
     */
    public function deleteCommentary(Commentary $comment){
        $sql = 'DELETE FROM commentary WHERE id_commentary = :id';
        $req = $this->_bdd->prepare($sql);
        $req->bindValue('id',$comment->getId_commentary(),PDO::PARAM_INT);
        $req->execute();
    }

    /**
     * méthode modification d'un commentaire
     * @param Commentary $comment
     */
    public function updateCommentary(Commentary $comment){
        $sql = 'UPDATE commentary SET title = :title, description = :descrip, date_posted = :date, approved = :approved, disapproved = :disapproved, id_article = :id_article, id_user = :id_user WHERE id_commentary = :id';
        $req = $this->_bdd->prepare($sql);
        $req->bindValue('title',$comment->getTitle(),PDO::PARAM_STR);
        $req->bindValue('descrip',$comment->getDescription(),PDO::PARAM_STR);
        $req->bindValue('date',$comment->getDate_posted(),PDO::PARAM_STR);
        $req->bindValue('approved',$comment->getApproved(),PDO::PARAM_INT);
        $req->bindValue('disapproved',$comment->getDisapproved(),PDO::PARAM_INT);
        $req->bindValue('id_article',$comment->getId_article(),PDO::PARAM_INT);
        $req->bindValue('id_user',$comment->getId_user(),PDO::PARAM_INT);
        $req->bindValue('id_commentary',$comment->getId_commentary(),PDO::PARAM_INT);
        $req->execute();
    }

    /**
     * méthode recuperation de tous les commentaires de la base de données
     * @return array $reponse
     */
    public function getAllCommentary():array{
        $sql = 'SELECT * FROM commentary';
        $req = $this->_bdd->prepare($sql);
        $req->execute();
        $reponse = $req->fetchAll(PDO::FETCH_ASSOC);
        return $reponse;
    }

    /**
     * méthode recuperation un commentaire en fonction de son id
     * @param int $id
     * @return Commentary $commentary
     */
    public function getCommentaryById(int $id):Commentary{
        $sql = 'SELECT * FROM commentary WHERE id_commentary = :id';
        $req = $this->_bdd->prepare($sql);
        $req->bindValue('id',$id,PDO::PARAM_INT);
        $req->execute();
        $reponse = $req->fetch(PDO::FETCH_ASSOC);
        $commentary =  new Commentary($reponse);
        return $commentary;
    }

    /**
     * méthode recuperation de commentaires fonction de leur utilisateurs
     * @param int $id_user
     * @return array $tabComment
     */
    public function getCommentaryByIdUser(int $id_user):array{
        $tabComment=[];
        $sql = 'SELECT * FROM commentary WHERE id_user = :id';
        $req = $this->_bdd->prepare($sql);
        $req->bindValue('id',$id_user,PDO::PARAM_INT);
        $req->execute();
        
        while($reponse = $req->fetch(PDO::FETCH_ASSOC) ){
            $comment = new Commentary($reponse);
            $tabComment[]=$comment;
        }
        return $tabComment;
    }

    /**
     * méthode recuperation un commentaire en fonction de son article
     * @param int $id_article
     * @return array $tabComment
     */
    public function getCommentaryByIdArticle(int $id_article):array{
        $tabComment=[];
        $sql = 'SELECT * FROM commentary WHERE id_article = :id';
        $req = $this->_bdd->prepare($sql);
        $req->bindValue('id',$id_article,PDO::PARAM_INT);
        $req->execute();
        while($reponse = $req->fetch(PDO::FETCH_ASSOC)){
            $comment = new Commentary($reponse);
            $tabComment[]=$comment;
        }
        return $tabComment;
    }

}



?>