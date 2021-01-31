<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <link href="/style/main.css" rel="stylesheet" type="text/css">
    <title><?= $title ?></title>
</head>
    <body>
        <header>
            <div class="background-header">
                <div class="top-header">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <img src="/img/logo.png" alt="logo">
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
                                                    <a class="nav-link active" aria-current="page" href="/index.php">Accueil</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="/content/article.php">articles</a>
                                                </li>
                                                <?php if(empty($user)): ?>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="/content/login.php">Connexion</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="/content/register.php">Inscription</a>
                                                </li>
                                                <?php else: ?>
                                                <li class="nav-item dropdown">
                                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <?= $user->getName() ?>
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                                        <li><a class="dropdown-item" href="/content/dashboard.php">Tableau de Bord</a></li>
                                                        <li><a class="dropdown-item" href="/content/logout.php">Déconnexion</a></li>
                                                    </ul>
                                                </li> 
                                                <?php endif; ?>
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