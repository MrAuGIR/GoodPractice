<?php
namespace App\Controllers;

use App\Application;
use App\Db;
use App\Managers\ManagerArticle;
use App\Models\Card;
use App\Render;

class ArticleController{

    public static function index()
    {
        //on regarde si un utilisateur est connecté
        $user = (!empty($_SESSION['user']))? $_SESSION['user']: null;
        $manager = new ManagerArticle(Db::getInstance());
        $tabArticles = $manager->getXarticle(3);
        $tabCards = [];
        foreach ($tabArticles as $article) {
            $tabCards[] = new Card($article);
        }
        
        Render::render('Articles/index',['title' => 'Accueil', 'user'=>$user, 'cards'=>$tabCards]);
    }


    public static function list()
    {
        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;
        $manager = new ManagerArticle(Db::getInstance());
        $tabArticles = $manager->getAllArticle();
        foreach ($tabArticles as $article) {
            $tabCards[] = new Card($article);
        }

        Render::render('Articles/list', ['title'=>'Les Articles', 'user' => $user, 'cards' => $tabCards]);

    }


    public static function show()
    {
        $id = null;
        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;
        if(!empty($_GET['q']) && ctype_digit($_GET['q'])){
            $id = $_GET['q'];
        }

        if (!$id) {
            Render::render('Article/error', ['message'=>'article non trouvé']);
        }

        $manager = new ManagerArticle(Db::getInstance());
        $article = $manager->getArticleById($id);
        Render::render('Articles/show',['title'=> $article->getTitle(), 'user' => $user, 'article'=>$article]);

    }


    public static function add()
    {

        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;

        Render::render('Articles/add', ['title' => 'Ajouter un Article', 'user' => $user]);
    }

    public static function admin()
    {
        //on regarde si un utilisateur est connecté
        $user = Application::secure();

        //creation du manager article
        $manager = new ManagerArticle(Db::getInstance());

        if($user['role']===LEVEL_ADMIN){
            $articles = $manager->getAllArticle();
        }else{
            $articles = $manager->getArticleByIdUser($user['id']);
        }


        Render::render('Articles/admin', ['title' => 'Administration', 'user'=> $user, 'articles' => $articles] );
    }

}