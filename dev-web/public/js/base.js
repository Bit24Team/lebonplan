//---------------------------Cookies------------------------------/


const accept_cookies = document.querySelector('.accept_cookies');
const refuse_cookies = document.querySelector('.refuse_cookies');
const demande_cookies = document.querySelector('.cookies');
const more_info = document.getElementById('more_info');
const cookies_explain = document.querySelector('.cookies_explain');
const cookies_croix = document.getElementById('cookies_croix');

// Vérifie si le choix a déjà été fait (au lieu de toujours afficher)
if (!localStorage.getItem('cookies')) {
    demande_cookies.style.display = 'flex';
}

accept_cookies.addEventListener('click', function(){
    demande_cookies.style.display = 'none';
    localStorage.setItem('cookies', 'true'); // Sauvegarde dans le localStorage
    console.log("L'utilisateur a accepté les cookies");
});

refuse_cookies.addEventListener('click', function(){
    demande_cookies.style.display = 'none';
    localStorage.setItem('cookies', 'false'); // Sauvegarde dans le localStorage
    console.log("L'utilisateur a refusé les cookies");
});

more_info.addEventListener('click', function(){
    cookies_explain.style.display = "flex";
});

cookies_croix.addEventListener('click', function(){
    cookies_explain.style.display = "none";
});