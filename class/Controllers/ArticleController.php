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
    private $_category;

    public function __construct()
    {
        parent::__construct();
        $managerCategory = new ManagerCategory(); 
        $this->_category = $managerCategory->getAllCategory();
    }

    public function index()
    {
        //on regarde si un utilisateur est connecté
        $user = (!empty($_SESSION['user']))? $_SESSION['user']: null;
        
        //on recupère les derniers articles
        $tabArticles = $this->manager->getXarticle(3);
        $tabCards = [];
        foreach ($tabArticles as $article) {
            $tabCards[] = new Card($article);
        }

        
        Render::render('Articles/index',[
            'title' => 'Accueil',
            'user'=>$user, 
            'cards'=>$tabCards,
            'category' => $this->_category
            ]);
    }


    public function list()
    {
        $tabArticles = [];
        $tabCards = [];
        $category = null;
        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;

        $categoryId = (!empty($_GET['category']))? (int)$_GET['category'] : null;

        $managerCategory = new ManagerCategory();
        if($categoryId != null){
            $category = $managerCategory->getCategoryById($categoryId);
        }
       
        $tabArticles = ($categoryId != null) ? $this->manager->getArticleByFilter($categoryId) : $this->manager->getAllArticle();
        if(count($tabArticles)>0){
            foreach ($tabArticles as $article) {
                
                $tabCards[] = new Card($article);
            }
        }

        Render::render('Articles/list', [
            'title'=>'Les Articles', 
            'user' => $user, 
            'cards' => $tabCards, 
            'categoryActif'=> ($category != null)? $category : '']);

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
        Render::render('Articles/show',[
            'title'=> $article->getTitle(), 
            'user' => $user, 
            'article'=>$article]);

    }


    public function add()
    {

        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;

        // si soumission du formulaire de création
        if(isset($_POST['submit']) && $_POST['submit']==='Ajouter'){

            $errorAdd = $this->manager->traitementDonnees($_POST,'add');
            //si pas d'erreur lors de la creation
            if(!$errorAdd){
                header('location: ?controller=article&action=admin');
                exit();
            }
        }


        Render::render('Articles/add', ['title' => 'Ajouter un Article', 
            'user' => $user, 
            'category'=>$this->_category, 
            'errorAdd'=>$errorAdd=false]);
    }

    public function edit(){

        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;

        $id_article =(isset($_GET['q']))?(int)$_GET['q']: null;

        // erreur si id de l'article manquant
        if(empty($id_article)){
            header('location:?controller=article&action=admin&edit=fail');
            exit();
        }

        //si soumission du formulaire de modification
        if(isset($_POST['submit']) && $_POST['submit']==='Editer'){

            $errorAdd = $this->manager->traitementDonnees($_POST,'update');
            //si pas d'erreur lors de la creation
            echo $errorAdd;
            if (!$errorAdd) {
                header('location:?controller=article&action=admin');
                exit();
            }
        }

        $article = $this->manager->getArticleById($id_article);

        Render::render('Articles/edit', ['title' =>'Editer un article',
            'user'=> $user,
            'category'=>$this->_category,
            'article'=>$article,
            'errorAdd'=>$errorAdd=false]);

    }

    

}