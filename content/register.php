<?php

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'ManagerRegister.php';
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'bdd'.DIRECTORY_SEPARATOR.'bdd.php';

//soumission du formulaire
if(isset($_POST['submit']) && $_POST['submit'] === 'Inscription'){

    echo 'je suis dans la soumission';

    //on initialise le tableau d'erreurs
    $errors=[];
    //on crée l'instance de gestion des données reçus
    $managerRegister = new ManagerRegister($bdd);

    $user = $managerRegister->traitementDonnee($_POST);

    //si user créé 
    if($user){
        session_start(); //je demarre la session une fois que l'utilisateur est validé
        $_SESSION['user'] = $user;

        header('location: ../index.php?login=success');
        exit();
    }
    // si retour false => erreur lors de l'enregistrement
    $errors = $managerRegister->getErrors();

}


$title = 'Inscription';

?>
<?php require dirname(__DIR__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php'; ?>
    <main>
        <div class="container">
            <div class="row mt-5">
                <div class="col-12">
                    <h1>Inscription</h1>
                </div>
            </div>
            <div class="row mt-2">
                <form method="POST" action="" onsubmit="return valid_register()">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label" id="labelLogin" for="login">Login</label>
                            <input class="form-control" type="text" name="login" id="login" placeholder="votre login">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" id="labelEmail" for="email">Email</label>
                            <input class="form-control" type="text" name="email" id="email" placeholder="Votre email">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" id="labelPassword" for="password">Mot de passe</label>
                            <div class="row">
                                <div class="col-10">
                                    <input class="form-control" type="password" name="password" id="password" >
                                </div>
                                <div class="col-2">
                                    <span id="eye" >
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" id="labelPassword2" for="password2">Confirmer le mot de passe</label>
                            <div class="row">
                                <div class="col-10">
                                    <input class="form-control" type="password" name="password2" id="password2" >
                                </div>
                                <div class="col-2">
                                    <span id="eye2" >
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <input class="btn btn-primary"type="submit" name="submit" value="Inscription">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12 verif-password">
                            <ul class="list-group list-group-flush">
                                <li id="validPasswordC" class="list-group-item">Mot de passe entre 6 et 16 caractères</li>
                                <li id="validPasswordUpper" class="list-group-item">Au moins une majuscule</li>
                                <li id="validPasswordLower" class="list-group-item">Au moins une minuscule</li>
                                <li id="validPasswordNumber" class="list-group-item">Au moins un chiffre</li>
                            </ul>
                        </div>
                    </div>
                
                
                </form>
            </div>
        
        
        
        </div>
    </main>
    <?php require dirname(__DIR__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'footer.php'; ?>
    <script src="../js/verif_register.js"></script>
    </body>
</html>