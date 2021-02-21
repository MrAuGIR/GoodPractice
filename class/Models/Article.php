<?php
namespace App\Models;
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

    /**
     * Constructeur
     * @param $data array
     */
    public function __construct(array $data)
    {
        $this->hydrate($data);
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