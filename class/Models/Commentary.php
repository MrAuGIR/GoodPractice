<?php
namespace App\Models;

/**
 * Class objet commentaire posté sur un article
 */

class Commentary{

    private $_id_commentary;
    private $_title;
    private $_description;
    private $_date_posted;
    private $_approved;
    private $_disapproved;
    private $_id_article;
    private $_id_user;

    /**
     * Constructeur de la class Commentary
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    //getter

    /**
     * methode retourne l'id du commentaire
     * @return int $this->_id_commentary
     */
    public function getId_commentary():int{
        return $this->_id_commentary;
    }

    /**
     * methode retourne le titre du commentaire
     * @return string $this->_title
     */
    public function getTitle():string{
        return $this->_title;
    }

    /**
     * methode retourne la description du commentaire
     * @return string $this->_description
     */
    public function getDescription():string{
        return $this->_description;
    }

    /**
     * methode retourne la date d'envoie du commentaire
     * @return string $this->_date_posted
     */
    public function getDate_posted():string{
        return $this->_date_posted;
    }

    /**
     * methode retourne le nombre de like du commentaire
     * @return int $this->_approved
     */
    public function getApproved():int{
        return $this->_approved;
    }

    /**
     * methode retourne le nombre de dislike du commentaire
     * @return int $this->_disapproved
     */
    public function getDisapproved():int{
        return $this->_disapproved;
    }

    /**
     * methode retourne l'id de l'utilisateur 
     * @return int $this->_id_user
     */
    public function getId_user():int{
        return $this->_id_user;
    }

    /**
     * methode retourne l'id de l'article
     * @return int $this->_id_article
     */
    public function getId_article():int{
        return $this->_id_article;
    }

    //setter

    /**
     * methode écrit l'attribut id_commentary de l'objet
     * @param int $id
     */
    public function setId_commentary(int $id){
        $this->_id_commentary = $id;
    }

    /**
     * methode écrit l'attribut title de l'objet
     * @param string $title
     */
    public function setTitle(string $title){
        $this->_title = $title;
    }

    /**
     * methode écrit l'attribut description de l'objet
     * @param string $description
     */
    public function setDescription(string $description){
        $this->_description = $description;
    }

    /**
     * methode écrit l'attribut date_posted de l'objet
     * @param string $date_posted
     */
    public function setDate_posted(string $date_posted){
        $this->_date_posted = $date_posted;
    }

    /**
     * methode écrit l'attribut approved de l'objet
     * @param int $approved
     */
    public function setApproved(int $approved){
        $this->_approved = $approved;
    }

    /**
     * methode écrit l'attribut disapproved de l'objet
     * @param int $disapproved
     */
    public function setDisapproved(int $disapproved){
        $this->_disapproved = $disapproved;
    }

    /**
     * methode écrit l'attribut id de l'utilisateur de l'objet
     * @param int $id_user
     */
    public function setId_user(int $id_user){
        $this->_id_user = $id_user;
    }

    /**
     * methode écrit l'attribut id de l'article de l'objet
     * @param int $id_article
     */
    public function setId_article(int $id_article){
        $this->_id_article = $id_article;
    }

    /**
     * Method hydratation 
     * @param array $data
     */

    public function hydrate(array $data){
        foreach($data as $key=> $value){
            $method = 'set'.ucfirst($key);

            if(method_exists($this,$method)){
                $this->$method($value);
            }
        }
    }

}


?>