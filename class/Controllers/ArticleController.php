<?php
namespace App\Controllers;

use App\Controllers\Controller;
use App\Application;
use App\Db;
use App\Managers\ManagerArticle;
use App\Managers\ManagerCategory;
use App\Models\Card;
use App\Render;

class ArticleController extends Controller{

    protected $managerName = \App\Managers\ManagerArticle::class;

    public function index()
    {
        //on regarde si un utilisateur est connecté
        $user = (!empty($_SESSION['user']))? $_SESSION['user']: null;
        
        $tabArticles = $this->manager->getXarticle(3);
        $tabCards = [];
        foreach ($tabArticles as $article) {
            $tabCards[] = new Card($article);
        }
        
        Render::render('Articles/index',['title' => 'Accueil', 'user'=>$user, 'cards'=>$tabCards]);
    }


    public function list()
    {
        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;
        
        $tabArticles = $this->manager->getAllArticle();
        foreach ($tabArticles as $article) {
            $tabCards[] = new Card($article);
        }

        Render::render('Articles/list', ['title'=>'Les Articles', 'user' => $user, 'cards' => $tabCards]);

    }


    public function show()
    {
        $id = null;
        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;
        if(!empty($_GET['q']) && ctype_digit($_GET['q'])){
            $id = $_GET['q'];
        }

        if (!$id) {
            Render::render('Article/error', ['message'=>'article non trouvé']);
        }

        $article = $this->manager->getArticleById($id);
        Render::render('Articles/show',['title'=> $article->getTitle(), 'user' => $user, 'article'=>$article]);

    }


    public function add()
    {

        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;

        // si soumission du formulaire de création
        if(isset($_POST['submit']) && $_POST['submit']==='Ajouter'){

            $errorAdd = $this->manager->traitementDonnees($_POST);
            //si pas d'erreur lors de la creation
            if(!$errorAdd){
                header('location: ?controller=article&action=admin');
                exit();
            }
        }

        $managerCategory = new ManagerCategory();
        $listeCategory = $managerCategory->getAllCategory();

        Render::render('Articles/add', ['title' => 'Ajouter un Article', 
            'user' => $user, 
            'category'=>$listeCategory, 
            'errorAdd'=>$errorAdd=false]);
    }

    public function admin()
    {
        //on regarde si un utilisateur est connecté
        $user = Application::secure();

        //creation du manager article

        if($user['role']===LEVEL_ADMIN){
            $articles = $this->manager->getAllArticle();
        }else{
            $articles = $this->manager->getArticleByIdUser($user['id']);
        }


        Render::render('Articles/admin', ['title' => 'Administration', 'user'=> $user, 'articles' => $articles] );
    }

}