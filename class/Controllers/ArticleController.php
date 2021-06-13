<?php
namespace App\Controllers;

use App\Controllers\Controller;
use App\Application;
use App\Db;
use App\Managers\ManagerArticle;
use App\Managers\ManagerCategory;
use App\Models\Card;
use App\Models\Category;
use App\Render;

class ArticleController extends Controller{

    protected $managerName = \App\Managers\ManagerArticle::class;
    public $managerCategory;
    private $_category;

    public function __construct()
    {
        parent::__construct();
        $this->managerCategory = new ManagerCategory(); 
        $this->_category = $this->managerCategory->getAllCategory();
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

        //on recupère les categories les plus utilisé et les articles asociés
        /** @var Category[] */
        $bestCategories = $this->managerCategory->getBestCategories();
        $tabArticleByCategories = [];
        foreach($bestCategories as $category){
            $tab = [];
            $articles = $this->manager->getArticleByFilter($category->getId_category());
            foreach($articles as $article){
                $tab[] = new Card($article);
            }
            $tabArticleByCategories[$category->getName_Category()] = $tab;
        }

        
        Render::render('articles/index',[
            'title' => 'Accueil',
            'user'=>$user, 
            'cards'=>$tabCards,
            'category' => $this->_category,
            'listArticleBestCategory'=> $tabArticleByCategories
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

        Render::render('articles/list', [
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
            Render::render('article/error', ['message'=>'article non trouvé']);
        }

        $article = $this->manager->getArticleById($id);
        Render::render('articles/show',[
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
                header('location:?controller=admin&action=adminArticle');
                exit();
            }
        }


        Render::render('articles/add', ['title' => 'Ajouter un Article', 
            'user' => $user, 
            'category'=>$this->_category, 
            'errorAdd'=>$errorAdd=false]);
    }

    public function edit(){

        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;

        $id_article =(isset($_GET['q']))?(int)$_GET['q']: null;

        // erreur si id de l'article manquant
        if(empty($id_article)){
            header('location:?controller=admin&action=adminArticle&edit=fail');
            exit();
        }

        //si soumission du formulaire de modification
        if(isset($_POST['submit']) && $_POST['submit']==='Editer'){

            $errorAdd = $this->manager->traitementDonnees($_POST,'update');
            //si pas d'erreur lors de la creation
            echo $errorAdd;
            if (!$errorAdd) {
                header('location:?controller=admin&action=adminArticle');
                exit();
            }
        }

        $article = $this->manager->getArticleById($id_article);

        Render::render('articles/edit', ['title' =>'Editer un article',
            'user'=> $user,
            'category'=>$this->_category,
            'article'=>$article,
            'errorAdd'=>$errorAdd=false]);

    }

    

}