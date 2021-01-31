<?php
if(session_status()===PHP_SESSION_NONE){
    session_start();
}
require_once '../bdd/bdd.php';
require_once '../class/ManagerUser.php';
require_once '../class/ManagerArticle.php';
require_once '../class/Article.php';
$id = null;

//reception de l'id de l'article
if(isset($_GET['q']) && !empty($_GET['q'])){

    $id = (int)$_GET['q'];

    //creation instance de managerArticle
    $managerArticle = new ManagerArticle($bdd);

    //on crée l'instance de l'article
    $article = $managerArticle->getArticleById($id);

    //recuperation des infos de l'autheur de l'article
    $managerUser = new ManagerUser($bdd);
    $user = $managerUser->getUserById($article->getId_user());

}


$title = "Lecture";
?>
<?php require dirname(__DIR__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php'; ?>
    <main>
        <div class="container">
            <div class="row my-5">
                <div class="col-12">
                    <h2><?= $article->getTitle() ?></h2>
                </div>
            </div>
            <div class="row my-5">
                <div class="col-12 col-md-9 col-lg-10 border-end py-3 px-3">
                    <span><?= $article->getDescription() ?></span>
                </div>
                <div class="col-12 col-md-3 col-lg-2">
                    <ul class="list-group list-group-flush">
                        
                        <li class="list-group-item">Auteur : <?= $user->getName() ?></li>
                        <li class="list-group-item">Date : <?= $article->getDate_create() ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
<?php require dirname(__DIR__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'footer.php'; ?>