
let valid_register = () =>{

    let error = false;

    //on recupère les données du formulaire au submit
    let login = document.getElementById('login');
    let email = document.getElementById('email');
    let password = document.getElementById('password');
    let password2 = document.getElementById('password2');

    // on verifie le login

    if(login.value == ""){
        error = true;
        styleLabel('labelLogin',true);
    }else{
        styleLabel('labelLogin',false);
    }

    // on verifie l'email
    let regexCourriel = /.+@.+\..+/;
    if(email.value === '' || !regexCourriel.test(email.value)){
        error = true;
        styleLabel('labelEmail',true);
    }else{
        styleLabel('labelEmail');
    }

     //on verifie le mot de passe
     if(!verifPassword('password')){
        error = true;
        styleLabel('labelPassword',true);
    }else{
        styleLabel('labelPassword');
    }

    //on verifie la confirmation du mot de passe
    if(password.value != password2.value){
        error = true;
        styleLabel('labelPassword2', true);
    }else{
        styleLabel('labelPassword2');
    }


    if(error){
        return false;
    }
    return true;

}

//listener sur le input password

let el = document.getElementById('password');
//on rajoute un evènement sur la saisie dans le champs password
el.addEventListener('input', function(e){
    verifPassword('password');
});

//verification du mot de passe
function verifPassword(id){
    let password = document.getElementById(id);
    let error = false;
    //verif du nombre de caractère
    if(password.value.length<6 || password.value.length>16 || password.value===''){
        error = true;
        styleLabel('validPasswordC',true);
    }else{
        styleLabel('validPasswordC');
    }

    //verif d'au moins une majuscule
    let regexMaj = /[A-Z]/;
    if(!regexMaj.test(password.value)){
        error=true;
        styleLabel('validPasswordUpper',true);
        
    }else{
        styleLabel('validPasswordUpper');
        
    }

    //verif au moins une minuscule
    let regexMin = /[a-z]/;
    if(!regexMin.test(password.value)){
        error = true;
        styleLabel('validPasswordLower', true);
    }else{
        styleLabel('validPasswordLower');
    }

    //verif au moins un chiffre
    let regexNumber = /[0-9]/;
    if(!regexNumber.test(password.value)){
        error = true;
        styleLabel('validPasswordNumber', true);
    }else{
        styleLabel('validPasswordNumber');
    }

    return (error)?false:true;
}


// met en forme les labels en fonction de la saisie

function styleLabel(id,error=false){
    if(error){
        document.getElementById(id).style.color='red';
        document.getElementById(id).style.fontWeight='bold';
    }else{
        document.getElementById(id).style.color='black';
        document.getElementById(id).style.fontWeight='normal';
    }
}

//listener sur le click sur la balise span

let eye = document.getElementById('eye');
eye.addEventListener('click',function (e){
    visible('password');
})

let eye2 = document.getElementById('eye2');
eye2.addEventListener('click',function (e){
    visible('password2');
})


//rendre visible le mot de passe
function visible(id){
    let password = document.getElementById(id);
    if(password.getAttribute('type') == "password"){
        password.setAttribute("type", "text");
    }else{
        password.setAttribute("type", "password");
    }
}