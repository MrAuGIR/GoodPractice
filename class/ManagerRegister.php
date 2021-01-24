<?php

    class ManagerRegister{

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

        public function traitementDonnee(array $POST){

            $name = isset($POST['login'])?$POST['login']:"";
            $email = isset($POST['email'])?$POST['email']:"";
            $password = isset($POST['password'])?$POST['password']:"";
            $password2 = isset($POST['password2'])?$POST['password2']:"";

            $this->verificationLogin($name)->verificationEmail($email);
            $password_hash = $this->verificationPassword($password,$password2);

            if(empty($this->_errors)){

                $sql = 'INSERT INTO user(name,email,password,role) VALUE (:name, :email, :password, 1)';
                $req = $this->_bdd->prepare($sql);
                $req->bindValue('name',$name,PDO::PARAM_STR);
                $req->bindValue('email',$email,PDO::PARAM_STR);
                $req->bindValue('password',$password_hash,PDO::PARAM_STR);
                $req->execute();

                $user_id = $this->_bdd->lastInsertId(); //renvoie le dernier id généré par pdo

                $sql = 'SELECT * FROM user WHERE id_user = :id';
                $reponse = $this->_bdd->prepare($sql);
                $reponse->bindValue('id',$user_id,PDO::PARAM_INT);
                $reponse->execute();
                $user=$reponse->fetch();
                
                return $user;
            }
            return false;
        }
        
        
        /**
         * verificationLogin
         *
         * @param  mixed $login
         * @return object
         */
        private function verificationLogin(?string $login):object
        {
            if(empty($login)){
                $this->_errors['login'] = 'Login non renseigner';
            }else{
                if($this->login_exist($login)){
                    //login déjà utilisé
                    $this->_errors['login'] = 'Login déjà utilisé';
                }
            }
            return $this;
        }

        /**
         * verificationEmail
         *
         * @param  mixed $email
         * @return object
         */
        private function verificationEmail(?string $email):object
        {

            if(empty($email)){
                $this->_errors['email'] = 'Email non renseigné';
            }else{
                if($this->email_exist($email)){
                    //email déjà utilisé
                    $this->_errors['email'] = 'Email déjà utilisé';
                }
            }
            return $this;
        }
        
        /**
         * verificationPassword
         *
         * @param  mixed $password
         * @param  mixed $password2
         * @return void
         */
        private function verificationPassword(?string $password, $password2)
        {
            if(empty($password) || strlen($password)<6 || strlen($password)>16){
                $this->_errors['password']= "Mot de passe non conforme";
            }elseif($password != $password2){
                $this->_errors['password'] = "La confirmation du mot de passe invalide";
            }else{
                return $password_hash = password_hash($password,PASSWORD_BCRYPT);
            }
            return false;
        }
       
        /**
         * email_exist
         * la fonction renvoie true si l'email existe déjà en bdd
         * @param  mixed $email
         * @return bool
         */
        private function email_exist(string $email):bool
        {
            $sql = 'SELECT email FROM user WHERE email = :email';
            $req = $this->_bdd->prepare($sql);
            $req->bindValue('email',$email,PDO::PARAM_STR);
            $req->execute();

            return ($req->fetch())?true:false;
        }
       
        /**
         * login_exist
         * la fonction renvoie true si l'email existe déjà en bdd
         * @param  mixed $login
         * @return bool
         */
        private function login_exist(string $login):bool
        {
            $sql = 'SELECT name FROM user WHERE name = :name';
            $req = $this->_bdd->prepare($sql);
            $req->bindValue('name',$login,PDO::PARAM_STR);
            $req->execute();

            return ($req->fetch())? true:false;
        }

    }



?>