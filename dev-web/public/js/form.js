const form = document.getElementById('formulaire');
const cvFile = document.getElementById('filecv');
const sizeMax = 2097152; //2Mo
const afficher_cv = document.querySelector('.afficher_cv')
const cv_name = document.querySelector('.cv_name');
const cvLabel = document.getElementById('cv');
const lmFile = document.getElementById('filelm');
const afficher_lm = document.querySelector('.afficher_lm')
const lmLabel = document.getElementById('lm');
const lm_name = document.querySelector('.lm_name');
const notifForm = document.querySelector('.notifForm');

cvFile.addEventListener('change', function(){
    const files = this.files;
    if(files.length > 0){     
        if(files[0].size > sizeMax){
            error(cvLabel, "Le fichier dépasse la taille maximale de 2Mo.");
            cvFile.value = "";
            afficher_cv.style.display = "none";
        }
        else if(!cvFile.classList.contains('invisible')){
            success(cvLabel);
            const fileName = files[0].name;
            afficher_cv.style.display = "flex";
            cv_name.innerHTML = fileName;
        }   
    }
});

lmFile.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        if (file.size > sizeMax) {
            error(lmLabel, "Le fichier dépasse la taille maximale de 2Mo.");
            lmFile.value = ""; // Réinitialisation du champ file
            afficher_lm.style.display = "none";
        } else {
            success(lmLabel);
            lm_name.textContent = file.name;
            afficher_lm.style.display = "flex";
        }
    }
});

let ifError = 0;

function error (valeur, text){
    valeur.nextElementSibling.classList.remove('invisible');
    valeur.style.border = "1px solid red";
    valeur.nextElementSibling.innerText = text;
    ifError = 1;
}

function success(valeur){
    valeur.nextElementSibling.classList.add('invisible');
    valeur.style.border = "none";
    valeur.nextElementSibling.innerText = '';
}

form.addEventListener('submit', function(e){
    ifError = 0;
    e.preventDefault();
    console.log("efezffs");

    const cvValue = cvFile.value;
    const lmValue = lmFile.value;

    if(cvValue === ""){
        error(cvLabel, "CV obligatoire")
    }
    else if(!cv.classList.contains('invisible')){
        success(cvLabel);
    }

    if(lmValue === ""){
        error(lmLabel, "Lettre de motivation obligatoire")
    }
    else if(!lm.classList.contains('invisible')){
        success(cvLabel);
    }

    const notifForm_O = `
        <i class="fa-solid fa-check" style="color: #00ff00;"></i>
        <span>Votre candidature à bien été transmise.</span>
    `

    const notifForm_X = `
        <i class="fa-solid fa-xmark" style="color: #ff0000;"></i>
         <span>Vous ne pouvez pas postuler une nouvelle fois à cette annonce.</span>
    `
    let ifPostuler = 0;

    if(ifError === 0){
        notifForm.classList.add("toast")
        if(ifPostuler === 0){
            notifForm.innerHTML = notifForm_O;
            ifPostuler += 1;
        }
        else{
            notifForm.innerHTML = notifForm_X;
        }
            setTimeout(function() {
                notifForm.classList.add('fade-out');
        
                setTimeout(function(){
                    notifForm.innerHTML='';
                }, 500);
            }, 2500);
        console.log(ifPostuler);
    }
});

const btn_postuler = document.getElementById('btn_postuler');
const formbg = document.querySelector('.formulairebg');
const pos_formbg = formbg.getBoundingClientRect();

btn_postuler.addEventListener('click', function(){
    window.scrollTo({
        top: formbg.offsetTop, // Position par rapport au haut de la page
        behavior: "smooth"
    });
});