//---------------------------Cookies------------------------------/


const accept_cookies = document.querySelector('.accept_cookies');
const refuse_cookies = document.querySelector('.refuse_cookies');
const demande_cookies = document.querySelector('.cookies');
const more_info = document.getElementById('more_info');
const cookies_explain = document.querySelector('.cookies_explain');
const cookies_croix = document.getElementById('cookies_croix');

// Vérification du choix existant
if (!localStorage.getItem('cookieChoice')) {
    demande_cookies.style.display = 'flex'; // Affiche seulement si aucun choix n'a été fait
}

// Gestion des clics
accept_cookies.addEventListener('click', () => {
    setCookieChoice('accepted');
});

refuse_cookies.addEventListener('click', () => {
    setCookieChoice('rejected');
});

// Fonction pour enregistrer le choix (réutilisable)
function setCookieChoice(choice) {
    localStorage.setItem('cookieChoice', choice);
    demande_cookies.style.display = 'none';
    
    // Appliquez le choix ici (ex: charger/ne pas charger Google Analytics)
    if (choice === 'accepted') {
        loadTrackingScripts();
    }
    console.log(`Cookies ${choice}`);
}

// Exemple de chargement conditionnel
function loadTrackingScripts() {
    // Insérez ici vos scripts de tracking (Google Analytics etc.)
    console.log("Chargement des scripts de tracking");
}

more_info.addEventListener('click', function(){
    cookies_explain.style.display = "flex";
});

cookies_croix.addEventListener('click', function(){
    cookies_explain.style.display = "none";
});