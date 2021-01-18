<?php
require_once 'bdd/bdd.php';
require 'class/ManagerArticle.php';
require 'class/Card.php';


$title = 'Bonne pratique du quotidien';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="style/main.css" rel="stylesheet" type="text/css">
    <title><?= $title ?></title>
</head>
    <body>
        <header>
            <div class="background-header">
                <div class="top-header">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <img src="img/logo.png" alt="logo">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bottom-header">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                                    <div class="container-fluid">
                                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                                            <span class="navbar-toggler-icon"></span>
                                        </button>
                                        <div class="collapse navbar-collapse" id="navbarNavDropdown">
                                            <ul class="navbar-nav">
                                                <li class="nav-item">
                                                    <a class="nav-link active" aria-current="page" href="index.php">Accueil</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="content/article.php">articles</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="content/board.php">Mon dashboard</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Dropdown link
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                                        <li><a class="dropdown-item" href="#">Action</a></li>
                                                        <li><a class="dropdown-item" href="#">Another action</a></li>
                                                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
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
                <?= password_hash('admin', PASSWORD_BCRYPT); ?>
            </div>
        </main>
        <footer>
        
        </footer>
        <script src="bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>