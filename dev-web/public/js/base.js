//---------------------------Cookies------------------------------/


const accept_cookies = document.querySelector('.accept_cookies');
const refuse_cookies = document.querySelector('.refuse_cookies');
const demande_cookies = document.querySelector('.cookies');
const more_info = document.getElementById('more_info');
const cookies_explain = document.querySelector('.cookies_explain');
const cookies_croix = document.getElementById('cookies_croix');

let cookies = 0;

if (cookies === 0){
    demande_cookies.style.display = 'flex';
}

accept_cookies.addEventListener('click', function(){
    demande_cookies.style.display = 'none';
    cookies = 1;
    console.log("L'utilisateur à accepté les cookies");
});

refuse_cookies.addEventListener('click', function(){
    demande_cookies.style.display = 'none';
    console.log("L'utilisateur à refusé les cookies");
    cookies = 1;
});

more_info.addEventListener('click', function(){
    cookies_explain.style.display = "flex";
});

cookies_croix.addEventListener('click', function(){
    cookies_explain.style.display = "none";
});