const form = document.getElementById('formulaire');
const cvFile = document.getElementById('filecv');
const sizeMax = 2097152; // 2Mo
const afficher_cv = document.querySelector('.afficher_cv');
const cv_name = document.querySelector('.cv_name');
const cvLabel = document.getElementById('cv');
const lmFile = document.getElementById('filelm');
const afficher_lm = document.querySelector('.afficher_lm');
const lmLabel = document.getElementById('lm');
const lm_name = document.querySelector('.lm_name');
const notifForm = document.querySelector('.notifForm');

let ifPostuler = 0; // Déplacé en dehors de l’eventListener

function error(valeur, text) {
    const errorElem = valeur.nextElementSibling;
    errorElem.classList.remove('invisible');
    valeur.style.border = "1px solid red";
    errorElem.innerText = text;
}

function success(valeur) {
    const errorElem = valeur.nextElementSibling;
    errorElem.classList.add('invisible');
    valeur.style.border = "none";
    errorElem.innerText = '';
}

cvFile.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        if (file.size > sizeMax) {
            error(cvLabel, "Le fichier dépasse la taille maximale de 2Mo.");
            cvFile.value = "";
            afficher_cv.style.display = "none";
        } else {
            success(cvLabel);
            cv_name.textContent = file.name;
            afficher_cv.style.display = "flex";
        }
    }
});

lmFile.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        if (file.size > sizeMax) {
            error(lmLabel, "Le fichier dépasse la taille maximale de 2Mo.");
            lmFile.value = "";
            afficher_lm.style.display = "none";
        } else {
            success(lmLabel);
            lm_name.textContent = file.name;
            afficher_lm.style.display = "flex";
        }
    }
});

form.addEventListener('submit', function (e) {
    e.preventDefault();
    let isValid = true;

    if(cvValue === ""){
        error(cvLabel, "CV obligatoire")
        isValid = 1;
    }
    else if(!cv.classList.contains('invisible')){
        success(cvLabel);
    }

    if(lmValue === ""){
        error(lmLabel, "Lettre de motivation obligatoire")
        isValid = 1;
    }
    else if(!lm.classList.contains('invisible')){
        success(cvLabel);
    }

    if (isValid === 0) {
        notifToast();
    }
});

function notifToast() {
    notifForm.classList.add("toast");

    if (ifPostuler === 0) {
        notifForm.innerHTML = `
            <i class="fa-solid fa-check" style="color: #00ff00;"></i>
            <span>Votre candidature a bien été transmise.</span>
        `;
        ifPostuler = 1;
        // Soumission réelle du formulaire
        form.submit();
    } else {
        notifForm.innerHTML = `
            <i class="fa-solid fa-xmark" style="color: #ff0000;"></i>
            <span>Vous ne pouvez pas postuler une nouvelle fois.</span>
        `;
    }

    setTimeout(() => {
        notifForm.classList.add('fade-out');
        setTimeout(() => {
            notifForm.innerHTML = '';
            notifForm.classList.remove('fade-out', 'toast');
        }, 500);
    }, 2500);
}


const btn_postuler = document.getElementById('btn_postuler');
const formbg = document.querySelector('.formulairebg');
const pos_formbg = formbg.getBoundingClientRect();

btn_postuler.addEventListener('click', function(){
    window.scrollTo({
        top: formbg.offsetTop, // Position par rapport au haut de la page
        behavior: "smooth"
    });
});