<?php
namespace App\Controllers;

use App\Controllers\Controller;
use App\Application;
use App\Db;
use App\Managers\ManagerArticle;
use App\Managers\ManagerCategory;
use App\Managers\ManagerCommentary;
use App\Models\Card;
use App\Models\Category;
use App\Models\Commentary;
use App\Models\Article;
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
        $tabArticles = $this->manager->getXarticle(4);
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
        $listCategories = [];
        $listCategories = $managerCategory->getAllCategory();
       
        $tabArticles = ($categoryId != null) ? $this->manager->getArticleByFilter($categoryId) : $this->manager->getAllArticle();
        if(count($tabArticles)>0){
            foreach ($tabArticles as $article) {
                
                $tabCards[] = new Card($article);
            }
        }

        Render::render('articles/list', [
            'title' => 'Les Articles',
            'user' => $user,
            'cards' => $tabCards,
            'listCategories' => $listCategories,
            'categoryActif' => ($category != null) ? $category : ''
        ]);
        
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

        //on recupère les commentaires associés à l'article
        $managerComment = new ManagerCommentary();
        $comments = $managerComment->getCommentaryByIdArticle($id);

        $article = $this->manager->getArticleById($id);
        Render::render('articles/show',[
            'title'=> $article->getTitle(), 
            'user' => $user, 
            'article'=>$article,
            'comments' => $comments
        ]);

    }


    public function add()
    {

        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;

        // si soumission du formulaire de création
        if(isset($_POST['submit']) && $_POST['submit']==='Ajouter'){

            $errorAdd = $this->manager->traitementDonnees($_POST,'add');
            //si pas d'erreur lors de la creation
            if($errorAdd){
                $this->session->set('warning', 'Erreur lors de la création, vérifier les champs obligatoires');
                $this->session->redirect('?controller=article&action=add');
            }
            $this->session->set('success', 'Article créé');
            $this->session->redirect('?controller=admin&action=adminArticle');
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
            $this->session->set('warning',"Article inconnu");
            $this->session->redirect('?controller=admin&action=adminArticle&edit=fail');
        }

        //si soumission du formulaire de modification
        if(isset($_POST['submit']) && $_POST['submit']==='Editer'){

            $errorAdd = $this->manager->traitementDonnees($_POST,'update');
            //si pas d'erreur lors de la creation
            if ($errorAdd) {
                $this->session->set('warning', $errorAdd['error']);
                $this->session->redirect('?controller=article&action=edit&q='.$id_article);
            }
            $this->session->set('success', 'Modification éffectuée');
            $this->session->redirect('?controller=admin&action=adminArticle');
        }

        $article = $this->manager->getArticleById($id_article);

        Render::render('articles/edit', ['title' =>'Editer un article',
            'user'=> $user,
            'category'=>$this->_category,
            'article'=>$article,
            'errorAdd'=>$errorAdd=false]);

    }


    public function delete(){
        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;

        $id_article = (isset($_GET['q'])) ? (int)$_GET['q'] : null;

        if(!$user || !$id_article){
            $this->session->set('danger','Vous ne pouvez pas supprimer cette article');
            $this->session->redirect('?controller=admin&action=adminArticle');
        }

        //on verifie que l'article appartient bien a l'utilisateur
        /** @var Article $article  */
        $article = $this->manager->getArticleById($id_article);
        if($article->allowAction($user)){

            $result = $this->manager->deleteArticle($article);
            if($result){
                $this->session->set('success','Suppression de l\'article réussi');
                $this->session->redirect('?controller=admin&action=adminArticle');
            }
            $this->session->set('danger', 'Suppression de l\'article échoué');
            $this->session->redirect('?controller=admin&action=adminArticle');
        }

        $this->session->set('danger', 'Vous n\'êtes pas autorisé à supprimer cette article');
        $this->session->redirect('?controller=admin&action=adminArticle');
        

    }

    public function postComment(){
        
        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;
        //soumission du formulaire
        if(isset($_POST['submitComment']) && $_POST['submitComment']=="Poster"){
            $id = isset($_POST['id'])? $_POST['id'] : null;
            $content = isset($_POST['content']) ? trim($_POST['content']): "";
        }
    
        if(!$id){
            $this->session->set('danger', 'Erreur lors de la sauvegarde');
            $this->session->redirect('?controller=article&action=index');
        }

        if(!$user){
            $this->session->set('danger', 'Vous devez être connecté pour poster un commentaire');
            $this->session->redirect('?controller=article&action=show&q=' . $id);
        }
        
        $date = new \DateTime('now');

        $comment = new Commentary(['description' => $content]);
        $comment->setId_article($id)
            ->setId_user($user['id'])
            ->setApproved(0)
            ->setDisapproved(0)
            ->setDate_posted($date->format('Y-m-d H:i'))
            ->setTitle('comment-'.$id.'-user-'.$user['id']);

        $manager = new ManagerCommentary();
        $manager->addCommentary($comment);

        $this->session->set('success','Commentaire envoyé');
        $this->session->redirect('?controller=article&action=show&q=' . $id);

    }
    

    public function deleteComment()
    {
        $id = isset($_GET['q'])? (int)$_GET['q'] : null;
        $idComment = isset($_GET['id'])? (int)$_GET['id'] : null;
        $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : null;

        if($idComment == null || $id == null || !$user){
            $this->session->set('danger', 'Erreur commentaire inconnu');
            $this->session->redirect('?controller=article&action=show&q=' . $id);
        }
        
        $managerComment = new ManagerCommentary();
        //on recupère le commentaire
        $comment = $managerComment->getCommentaryById($idComment);

        //on verifie que le commentaire appartient bien a l'utilisateur
        if($user['id'] != $comment->getId_user()){
            $this->session->set('danger', 'Vous ne pouvez pas supprimer ce commentaire');
            $this->session->redirect('?controller=article&action=show&q=' . $id);
        }

        //suppression du commentaire
        $managerComment->deleteCommentary($comment);

        $this->session->set('success', 'Commentaire Supprimé');
        $this->session->redirect('?controller=article&action=show&q=' . $id);

    }

}   