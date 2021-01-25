<?php

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'ManagerLogin.php';
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'bdd'.DIRECTORY_SEPARATOR.'bdd.php';

$user = "";
$errors = [];

if(isset($_POST['submit']) && $_POST['submit']== 'Connexion'){

    $managerLogin = new ManagerLogin($bdd);

    $user = $managerLogin->traitementDonnees($_POST);

    if($user != false){
        session_start();
        $_SESSION['user']=serialize($user);
        header('location: ../index.php?login=success');
        exit();

    }else{
        $errors = $managerLogin->getErrors();
    }

}

$title = 'Connexion';

?>
<?php require dirname(__DIR__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php'; ?>
    <main>
        <div class="container">
            <div class="row mt-5">
                <div class="col-12">
                    <h1>Connexion</h1>
                </div>
            </div>
            <div class="row mt-2">
                <form method="POST" action="" onsubmit="return valid_login()">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label" id="labelLogin" for="login">Login</label>
                            <input class="form-control" type="text" name="name" id="name" placeholder="votre login">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" id="labelEmail" for="email">Email</label>
                            <input class="form-control" type="text" name="email" id="email" placeholder="Votre email">
                        </div>
                        <div class="col-12 mt-1">
                            <label class="form-label" id="labelPassword" for="password">Mot de passe</label>
                            <div class="row">
                                <div class="col-9 col-md-10 col-lg-11">
                                    <input class="form-control" type="password" name="password" id="password" >
                                </div>
                                <div class="col-3 col-md-2 col-lg-1">
                                    <span id="eye" >
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <input class="btn btn-primary"type="submit" name="submit" value="Connexion">
                    </div>
                </form>
            </div>
            <?php if(!empty($errors)): ?>
            <div class="row">
                <div class="col-10 offset-2">
                    <ul class="list-group list-group-flush">
                    <?php foreach($errors as $error): ?>
                        <li class="list-group-item"><?= $error ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
    <?php require dirname(__DIR__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'footer.php'; ?>
    <script src="../js/verif_login.js"></script>
    </body>
</html>