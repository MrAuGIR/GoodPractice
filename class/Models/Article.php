<?php
namespace App\Models;

use App\Managers\ManagerUser;
use App\Managers\ManagerArticle;
use App\Managers\ManagerCategory;
use App\Managers\ManagerCommentary;

/**
 * class Article
 * @author  Aurelien Girard
 * 
 */
class Article{
    private $_id_article;
    private $_title;
    private $_description;
    private $_url;
    private $_url_img;
    private $_date_create;
    private $_id_user;
    private $_id_category;
    private $_num_comment; // n'est pas dans la bdd

    /**
     * Constructeur
     * @param $data array
     */
    public function __construct(array $data)
    {
        $this->hydrate($data);
        $this->getNumCommnents();
    }

    //getter

    public function getId_article():int{
        return $this->_id_article;
    }

    public function getTitle():string{
        return $this->_title;
    }

    public function getDescription():string{
        return $this->_description;
    }

    public function getUrl():?string{
        return $this->_url;
    }

    public function getUrl_img():string{
        return $this->_url_img;
    }

    public function getDate_create():string{
        return $this->_date_create;
    }

    public function getId_user():int{
        return $this->_id_user;
    }

    public function getId_category():int{
        return $this->_id_category;
    }

    public function getNum_commnents():int{
        return $this->_num_comment;
    }

    //setter

    public function setId_article(int $id){
        $this->_id_article = $id;
    }

    public function setTitle(string $title){
        $this->_title = $title;
    }

    public function setDescription(string $description){
        $this->_description = $description;
    }

    public function setUrl(?string $url){
        $this->_url = $url;
    }

    public function setUrl_img(string $url){
        $this->_url_img = $url;
    }

    public function setDate_create(string $date){
        $this->_date_create = $date;
    }

    public function setId_user(int $id_user){
        $this->_id_user = $id_user;
    }

    public function setId_category(int $id_category){
        $this->_id_category = $id_category;
    }

    public function setNum_comment(int $numComment){
        if($numComment < 0) $numComment = 0;
        $this->_num_comment = $numComment;
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
    
    /**
     * getUser
     *  retourne l'utilisateur auteur de l'article
     * @return User
     */
    public function getUser():User{
        $managerUser = new ManagerUser();
        return $managerUser->getUserById($this->getId_user());
    }
    
    /**
     * getCategorie
     *  retourn l'objet category de l'article
     * @return Category
     */
    public function getCategory():Category{
        $managerCategory = new ManagerCategory();
        return $managerCategory->getCategoryById($this->getId_category());
    }

    public function allowAction(array $user){
        
        if($this->_id_user === $user['id'] || $user['role'] === 'admin') return true;
    }

    
    /**
     * Renvoie le nombre de commentaires de l'article
     *
     * @return int
     */
    private function getNumCommnents(){
        $managerArticle = new ManagerArticle();
        $num = $managerArticle->getNumCommnents($this->getId_article());
        $this->setNum_comment($num);
    }
}


?>