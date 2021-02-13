<?php
namespace App\Controllers;
use App\Db;
use App\Managers\ManagerArticle;
use App\Models\Card;
use App\Render;

class ArticleController{

    public static function index()
    {
        $manager = new ManagerArticle(Db::getInstance());
        $tabArticles = $manager->getXarticle(3);
        $tabCards = [];
        foreach ($tabArticles as $article) {
            $tabCards[] = new Card($article);
        }
        
        Render::render('Articles/index',['title' => 'Accueil', 'cards'=>$tabCards]);
    }


    public static function list()
    {
        $manager = new ManagerArticle(Db::getInstance());
        $tabArticles = $manager->getAllArticle();
        foreach ($tabArticles as $article) {
            $tabCards[] = new Card($article);
        }

        Render::render('Articles/list', ['title'=>'Les Articles','cards' => $tabCards]);

    }


    public static function show()
    {
        $id = null;

        if(!empty($_GET['q']) && ctype_digit($_GET['q'])){
            $id = $_GET['q'];
        }

        if (!$id) {
            Render::render('Article/error', ['message'=>'article non trouvé']);
        }

        $manager = new ManagerArticle(Db::getInstance());
        $article = $manager->getArticleById($id);
        Render::render('Articles/show',['title'=> $article->getTitle(), 'article'=>$article]);

    }


    public static function add()
    {



        Render::render('Articles/add', ['title' => 'Ajouter un Article']);
    }

}