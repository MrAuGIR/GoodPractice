<?php
    require_once __DIR__.DIRECTORY_SEPARATOR.'User.php';
    require_once __DIR__.DIRECTORY_SEPARATOR.'Editeur.php';
    require_once __DIR__.DIRECTORY_SEPARATOR.'Admin.php';

    class ManagerLogin{

        private $_bdd;
        private $_errors=[];

        public function __construct(PDO $bdd)
        {
            $this->setBdd($bdd);
        }


        //getter
        
        /**
         * getBdd
         *
         * @return PDO
         */
        public function getBdd():PDO
        {
            return $this->_bdd;
        }
        
        /**
         * getErrors
         *
         * @return array
         */
        public function getErrors():array
        {
            return $this->_errors;
        }

        // setter
        
        /**
         * setBdd
         *
         * @param  mixed $pdo
         * @return void
         */
        public function setBdd(PDO $bdd)
        {
            $this->_bdd = $bdd;
            return $this;
        }

        
        /**
         * setErrors
         *
         * @param  mixed $errors
         * @return void
         */
        public function setErrors(array $errors)
        {
            $this->_errors = $errors;
            return $this;
        }
        
        /**
         * traitementDonnees
         *
         * @param  mixed $post
         * @return mixed
         */
        public function traitementDonnees(array $post){

            $name = isset($post['name'])? $post['name']:"";
            $password = isset($post['password'])?$post['password']:"";

            // donnée non saisie
            if(empty($name) || empty($password)){

                $this->_errors['saisie']= 'saisie vide';
            }

            //si pas d'erreur
            if(empty($this->getErrors())){

                $sql = 'SELECT * FROM user WHERE name=:name';
                $req = $this->getBdd()->prepare($sql);
                $req->bindValue('name',$name,PDO::PARAM_STR);
                $req->execute();
                $data = $req->fetch(PDO::FETCH_ASSOC);

                //si user bien present dans bdd
                if($data){
                    //si le mot de passe est correct
                    if( password_verify($password, $data['password'])){
                        //on crée l'instance user
                        return $this->createObjectUser($data);

                    }else{
                        $this->_errors['saisie'] = "utilisateur inexistant";
                        return false;
                    }
                }else{
                    $this->_errors['saisie'] = "utilisateur inexistant";
                    return false;
                }
            }
        }

        
        /**
         * createObjectUser
         *
         * @param  mixed $data
         * @return object
         */
        private function createObjectUser(array $data):object
        {
            if($data['role']!= LEVEL_ADMIN){
                $user = new Editeur($data);
                return $user;
            }
            return new Admin($data);
        }
    }



?>