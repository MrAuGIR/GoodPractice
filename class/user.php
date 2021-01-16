<?php


class user{

    private $name;
    private $email;
    private $password;
    private $role;
    private $id_user;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }


    // getter

    public function getName():string{
        return $this->name;
    }

    public function getEmail():string{
        return $this->email;
    }

    public function getPassword():string{
        return $this->password;
    }

    public function getRole():string{
        return $this->role;
    }

    public function getId_user():int{
        return $this->id_user;
    }

    //setter

    public function setName(string $name):void{
        $this->name = $name;
    }

    public function setEmail(string $email):void{
        $this->email = $email;
    }

    public function setPassword(string $password):void{
        $this->password = $password;
    }

    public function setRole(string $role):void{
        $this->role = $role;
    }

    public function setId_user(int $id_user):void{
        $this->id_user = $id_user;
    }
    
    //methodes

    /**
     * @param array
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