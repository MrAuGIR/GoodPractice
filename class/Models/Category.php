<?php
namespace App\Models;


class Category{

    private $_id_category;
    private $_name_category;
    private $_default_image;


    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    //getter

    public function getId_category():int{
        return $this->_id_category;
    }

    public function getName_Category():string{
        return $this->_name_category;
    }

    public function getDefault_image():string{
        return $this->_default_image;
    }

    //setter

    public function setId_category($id){
        $this->_id_category = $id;
        return $this;
    }

    public function setName_category(string $name){
        $this->_name_category = $name;
        return $this;
    }

    public function setDefault_image(string $default_img){
        $this->_default_image = $default_img;
        return $this;
    }

    //methodes

    /**
     * methode hydration des attributs
     * @param array $data
     */

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

}