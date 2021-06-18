window.onload = () =>{

    const filterForm = document.getElementById('formCategories');
    //on ajoute un listener sur chaque radio

    document.querySelectorAll('#formCategories .input-radio').forEach(radio => {
        radio.addEventListener("change", () => {
            const Form = new FormData(filterForm);

            // On fabrique la "queryString"
            const Params = new URLSearchParams();

            Form.forEach((value, key) => {
                Params.append(key, value);
            });

            // On récupère l'url active
            const Url = new URL(window.location.href);

            requeteAjax(Url,Params);
        })
    })

}

function requeteAjax(url, params){
    let xmlhttp = new XMLHttpRequest();
    const path = url.origin+"/ajax.php" + "?" + params.toString() + "&categorySearch=1"
    console.log(path);

    xmlhttp.open("GET", path, true);
    xmlhttp.onreadystatechange = function() {

        if (this.readyState == 4 && this.status == 200) {
            var data= JSON.parse(this.responseText);
            console.log(data); 

            document.querySelector("#content").innerHTML=data.content;

        }
    };
    xmlhttp.send();

}
