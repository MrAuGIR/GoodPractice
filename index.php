<?php
if(session_status()===PHP_SESSION_NONE){
    session_start();
}
require_once 'class/User.php';
require_once 'class/Editeur.php';
// objet user
$user = isset($_SESSION['user'])? unserialize($_SESSION['user']) : "";
require_once 'bdd/bdd.php';
require 'class/ManagerArticle.php';
require 'class/Card.php';


$title = 'Bonne pratique du quotidien';
?>
<?php require 'includes/header.php'; ?>
        <main>
            <div class="container">
                <div class="row mt-5">
                    <div class="col-12">
                        <h1>Dernier Articles ajoutés</h1>
                    </div>
                </div>
                <div class="row mt-2 mb-5 pt-2 pb-1">
                    <?php $managerArticle = new ManagerArticle($bdd); ?>
                    <?php $tabArticles = $managerArticle->getXarticle(3); ?>
                    <?php foreach ($tabArticles as $article): ?>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4">
                            <?php $card = new Card($article); ?>
                            <?= $card->generateCard(); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="row mt-5">
                    <div class="col-12 col-lg-6">
                        <span>Ajouter un article ?</h1>
                    </div>
                    <div class="col-12 col-lg-6">
                        <button class="btn-primary">Ajouter</button>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-12">
                        <h1>Catégories</h1>
                    </div>
                </div>
            </div>
        </main>
        <?php require 'includes/footer.php'; ?>
    </body>
</html>