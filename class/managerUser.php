<?php

require dirname(__DIR__). DIRECTORY_SEPARATOR .'class'.DIRECTORY_SEPARATOR.'User.php';

/**
 * Class managerUser
 */

class ManagerUser {

    private $bdd; // PDO instance

    /**
     * Constructeur de la class managerUser
     * @param PDO $bdd
     */
    public function __construct(PDO $bdd)
    {
        $this->setDb($bdd);
    }

    /**
     * method set attribut pdo de la class
     * @param PDO $bdd
     */
    public function setDb(PDO $bdd):void{
        $this->bdd = $bdd;
    }


    /**
     * Méthode ajouter un utilisateur
     * @param User $user
     */
    public function addUser(User $user):void{
        $sql = 'INSERT INTO user(name, email, password, role) VALUES (:name, :email, :password, :role)';
        $req = $this->bdd->prepare($sql);
        $req->bindValue('name',$user->getName(),PDO::PARAM_STR);
        $req->bindValue('email',$user->getEmail(),PDO::PARAM_STR);
        $req->bindValue('password',$user->getPassword(),PDO::PARAM_STR);
        $req->bindValue('role',$user->getRole(),PDO::PARAM_STR);
        $req->execute();
    }

    /**
     * Méthode suppression d'un utilisateur
     * @param User $user
     */
    public function deleteUser(User $user):void{
        $sql = 'DELETE FROM user WHERE id_user = :id';
        $req = $this->bdd->prepare($sql);
        $req->bindValue('id',$user->getId_user(),PDO::PARAM_INT);
        $req->execute();
    }

    /**
     * Methode modification d'un utilisateur existant
     * @param User $user
     */
    public function updateUser(User $user):void{
        $sql = 'UPDATE user SET name = :name, email = :email, password = :password, role = :role WHERE id_user = :id ';
        $req = $this->bdd->prepare($sql);
        $req->bindValue('name',$user->getName(),PDO::PARAM_STR);
        $req->bindValue('email',$user->getEmail(),PDO::PARAM_STR);
        $req->bindValue('password',$user->getPassword(),PDO::PARAM_STR);
        $req->bindValue('role',$user->getRole(),PDO::PARAM_STR);
        $req->bindValue('id',$user->getId_user(),PDO::PARAM_INT);
        $req->execute();
    }

    /**
     * Methode recupérer tous les utilisateurs en base de donnée
     * @return array $users
     */
    public function getAll():array{
        $users=[];
        $sql = 'SELECT * FROM user';
        $req = $this->bdd->prepare($sql);
        $req->execute();
        foreach ($req->fetch(PDO::FETCH_ASSOC) as $user){
            $users[] = new User($user);
        }

        return $users;
    }

    /**
     * Méthode recupérer un utilisateur en base de donnée
     * @param int $id
     * @return User $user
     */
    public function getUserById(int $id):User{
        $sql = 'SELECT * FROM user WHERE id_user = :id';
        $req = $this->bdd->prepare($sql);
        $req->bindValue('id',$id,PDO::PARAM_INT);
        $req->execute();
        $reponse = $req->fetch(PDO::FETCH_ASSOC);
        $user = new User($reponse);
        return $user;
    }
}

?>