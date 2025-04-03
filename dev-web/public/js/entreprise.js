const btn_noter = document.getElementById('btn_noter');
const block_notation = document.getElementById('rating_block');
const croix = document.getElementById('croix');
const btn_send = document.getElementById('btn_send');
const rating_form = document.getElementById('rating_form');
const rating_value = document.getElementById('rating_value');

btn_noter.addEventListener('click', function(){
    block_notation.style.display = "flex";
});

croix.addEventListener('click', function(){
    block_notation.style.display = "none";
});

// Modification ici pour utiliser l'événement submit du formulaire
rating_form.addEventListener('submit', function(e) {
    e.preventDefault(); // Empêche le rechargement de la page
    block_notation.style.display = "none";
    
    // Envoi du formulaire via AJAX ou laisser le submit normal se faire
    this.submit();
});

const star1 = document.getElementById('star1');
const star2 = document.getElementById('star2');
const star3 = document.getElementById('star3');
const star4 = document.getElementById('star4');
const star5 = document.getElementById('star5');
const starCont = document.querySelector('.star');

let stars = [star1, star2, star3, star4, star5];
let clickStar = 0;
let starSelection = 0;

stars.forEach((star, index) => {
    star.addEventListener('mouseover', function() {
        if (clickStar !== 2) { // Seulement si aucune étoile n'a été cliquée
            for (let i = 0; i <= index; i++) {
                stars[i].classList.remove('fa-regular');
                stars[i].classList.add('fa-solid');
                stars[i].style.color = "gold";
            }
            clickStar = 1;
        }
    });

    star.addEventListener('mouseout', function() {
        if(clickStar === 1){
            stars.forEach(function(s) {
                s.classList.remove('fa-solid');
                s.classList.add('fa-regular');
                s.style.color = "black";
            }); 
            clickStar = 0;
        }        
    });   
    
    star.addEventListener('click', function(){
        clickStar = 2;
        starSelection = this.dataset.value;
        rating_value.value = starSelection; // Mise à jour de la valeur du champ hidden
        
        // Mise à jour visuelle des étoiles
        stars.forEach(function(s, i) {
            if (i < starSelection) {
                s.classList.remove('fa-regular');
                s.classList.add('fa-solid');
                s.style.color = "gold";
            } else {
                s.classList.remove('fa-solid');
                s.classList.add('fa-regular');
                s.style.color = "black";
            }
        });
    });
});

// Gestion du survol global de la zone d'étoiles
starCont.addEventListener('mouseout', function(){
    if(clickStar === 1){
        stars.forEach(function(s) {
            s.classList.remove('fa-solid');
            s.classList.add('fa-regular');
            s.style.color = "black";
        }); 
        clickStar = 0;
    }
});