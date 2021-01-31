<?php
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'secure.php';
require_once '../bdd/bdd.php';
require_once '../class/User.php';
require_once '../class/Editeur.php';
require_once '../class/Admin.php';
require_once '../class/ManagerArticle.php';
//init
$user = null;
$articles = [];
// objet user
$user = isset($_SESSION['user'])? unserialize($_SESSION['user']) : "";

$managerArticle = new ManagerArticle($bdd);

//test si user admin ou editeur
if($user->getRole() === LEVEL_EDITEUR){

    $articles = $managerArticle->getArticleByIdUser($user->getId_user());

}else{

    $articles = $managerArticle->getAllArticle();

}


$title = "Tableau de bord";
?>
<?php require dirname(__DIR__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php'; ?>
    <main>
        <div class="container">
            <div class="row">
                <h1>Tableau de Bord</h1>
            </div>
            <div class="row">
                <div class="col-12 col-md-2 border-end">
                    <div class="d-flex align-items-start">
                        <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="v-pills-home-tab" data-bs-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Info-Site</a>
                            <a class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Profile</a>
                            <a class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Messages</a>
                            <a class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">Settings</a>
                        </div>
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">...</div>
                            <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">...</div>
                            <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">...</div>
                            <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">...</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-10">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Titre</th>
                                <th scope="col">Description</th>
                                <th scope="col">Url</th>
                                <th scope="col">Url_image</th>
                                <th scope="col">Date creation</th>
                                <th scope="col">Catégorie</th>
                                <?php if($user->getRole()===LEVEL_ADMIN): ?>
                                    <th scope="col">utilisateur</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($articles as $article): ?>
                            <tr>
                                <td><?= $article->getTitle() ?></td>
                                <td><?= substr($article->getDescription(),0,50) ?></td>
                                <td><?= $article->getUrl() ?></td>
                                <td><?= $article->getUrl_img() ?></td>
                                <td><?= $article->getDate_create() ?></td>
                                <td><?= $article->getId_category() ?></td>
                                <?php if($user->getRole()===LEVEL_ADMIN): ?>
                                    <td><?= $article->getId_user() ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    
    </main>
<?php require dirname(__DIR__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'footer.php'; ?>