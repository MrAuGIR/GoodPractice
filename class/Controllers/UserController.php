<?php
namespace App\Controllers;
use App\Db;
use App\Render;
use App\Managers\ManagerLogin;
use App\Managers\ManagerRegister;

class UserController{


    public static function login()
    {
        // soumission du formulaire de connexion
        if(!empty($_POST['submit']) && $_POST['submit']==='Connexion'){
            $managerLogin = new ManagerLogin(Db::getInstance());

            $user = $managerLogin->traitementDonnees($_POST);

            if ($user != false) {
                session_start();
                $_SESSION['user']['name'] = $user->getName();
                $_SESSION['user']['id'] = $user->getId_user();
                $_SESSION['user']['role'] = $user->getRole();
                header('location: ?controller=article&action=index');
                exit();
            } else {
                $errors = $managerLogin->getErrors();
            }


        }

        Render::render('users/login', ['title' => 'Connexion']);
    }


    public static function register()
    {
        //si soumission du formulaire d'inscription
        if (isset($_POST['submit']) && $_POST['submit'] === 'Inscription') {
            //on initialise le tableau d'erreurs
            $errors = [];
            //on crée l'instance de gestion des données reçus
            $managerRegister = new ManagerRegister(Db::getInstance());

            $user = $managerRegister->traitementDonnee($_POST);

            //si user créé 
            if ($user) {
                $_SESSION['user']['name'] = $user->getName();
                $_SESSION['user']['id'] = $user->getId_user();
                $_SESSION['user']['role'] = $user->getRole();
                header('location:?controller=article&action=index');
                exit();
            }
            // si retour false => erreur lors de l'enregistrement
            $errors = $managerRegister->getErrors();
        }


        Render::render('users/register', ['title' => 'Inscription']);
    }

    public static function logout(){

        unset($_SESSION['user']);
        header('location: ?controller=article&action=index');
        exit();
    }



}