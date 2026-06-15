<?php
namespace App\Controllers;
use App\Db;
use App\Render;
use App\Application;
use App\Models\User;
use App\Managers\ManagerLogin;
use App\Controllers\Controller;
use App\Managers\Manager;
use App\Managers\ManagerRegister;
use App\Managers\ManagerUser;

class UserController extends Controller{

    protected $managerName = \App\Managers\ManagerUser::class;

    public function login()
    {
        // soumission du formulaire de connexion
        if(!empty($_POST['submit']) && $_POST['submit']==='Connexion'){
            $managerLogin = new ManagerLogin();
            $user = $managerLogin->traitementDonnees($_POST);

            if ($user != false) {
                session_start();
                $_SESSION['user']['name'] = $user->getName();
                $_SESSION['user']['id'] = $user->getId_user();
                $_SESSION['user']['role'] = $user->getRole();
                header('location: ?controller=article&action=index');
                exit();
            } else {
        
                $this->session->set('danger', 'identifiants incorrecte');
                $this->session->redirect('?controller=user&action=login');
            }


        }

        Render::render('users/login', ['title' => 'Connexion']);
    }


    public function register()
    {
        //si soumission du formulaire d'inscription
        if (isset($_POST['submit']) && $_POST['submit'] === 'Inscription') {
            //on initialise le tableau d'erreurs
            $errors = [];
            //on crée l'instance de gestion des données reçus
            $managerRegister = new ManagerRegister();
            /** @var User $user */
            $user = $managerRegister->traitementDonnee($_POST);

            //si user créé 
            if ($user) {
                $_SESSION['user']['name'] = $user->getName();
                $_SESSION['user']['id'] = $user->getId_user();
                $_SESSION['user']['role'] = $user->getRole();
                
                $this->session->set('success', "Inscription réussite");
                $this->session->redirect('?controller=article&action=index');
            }
            $listErrors = $managerRegister->getErrors();
            $message = "<ul>";
            foreach($listErrors as $key => $value){
                $message .= "<li>".$value."</li>";
            }
            $message .= "</ul>";
            
            // si retour false => erreur lors de l'enregistrement
            $this->session->set('danger', $message);
            $this->session->redirect('?controller=user&action=register');
            
        }


        Render::render('users/register', ['title' => 'Inscription']);
    }

    public static function logout(){

        unset($_SESSION['user']);
        header('location: ?controller=article&action=index');
        exit();
    }

    public function edit(){

        //on verifie le level de l'utilisateur connecté
        $user = Application::secure([LEVEL_ADMIN]);
        //on verifie l'id de l'utilisateur a modifier
        $id = ( isset($_GET['q']) && !empty($_GET['q']) )? (int) $_GET['q'] : null;
        if(!$id){
            $this->session->set('warning','Utilisateur Inconnu');
            $this->session->redirect('?controller=admin&action=adminUser');
        }
        //l'utilisateur a modifier
        /** @var User $user */
        $user = $this->manager->getUserById($id);
        if(!$user){
            $this->session->set('warning','Utilisateur Inconnu');
            $this->session->redirect('?controller=admin&action=adminUser');
        }

        //si soumission du formulaire
        if(isset($_POST['submit']) && $_POST['submit'] === 'Editer'){
            $idUser = isset($_POST['id'])? (int)$_POST['id'] : null;
            $email = isset($_POST['email'])? $this->cleanString($_POST['email']) : '';
            $name = isset($_POST['name'])? $this->cleanString($_POST['name']) : '';
            $role = isset($_POST['role'])? $this->cleanString($_POST['role']) : '';

            $user->setEmail($email);
            $user->setName($name);
            $user->setRole($role);

            $managerUser = new ManagerUser();
            $result = $managerUser->updateUser($user);

            if(!$result){
                $this->session->set('danger','Erreur lors de la mise a jour');
                $this->session->redirect('?controller=user&action=edit&q='.$id);
            }

            $this->session->set('success','Utilisateur mis à jour');
            $this->session->redirect('?controller=admin&action=adminUser');
        }

        Render::render('users/edit',['title' => 'Modifier Utilisateur', 'user' => $user]);

        
    }

    public function delete(){

    }

}