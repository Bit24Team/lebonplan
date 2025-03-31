const btn_up = document.querySelector('.btn_remonter');
btn_up.style.display = "none";

window.addEventListener('scroll', function(){
    let hauteur = document.documentElement.scrollTop;
    if(hauteur >= 200){
        btn_up.style.display = "block";
    }
    else{
        btn_up.style.display = "none";
    }
})

btn_up.addEventListener('click', function(){
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
})