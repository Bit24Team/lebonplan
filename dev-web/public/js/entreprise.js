const btn_noter = document.getElementById('btn_noter');
const block_notation = document.querySelector('.block_notation');
const croix = document.getElementById('croix');
const btn_send = document.getElementById('btn_send');

btn_noter.addEventListener('click', function(){
    block_notation.style.display = "flex"
});

croix.addEventListener('click', function(){
    block_notation.style.display = "none"
});

btn_send.addEventListener('click', function(){
    block_notation.style.display = "none"
    console.log(starSelection);
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
        for (let i = 0; i <= index; i++) {
            stars[i].classList.remove('fa-regular');
            stars[i].classList.add('fa-solid');
            stars[i].style.color = "gold";
        }
        if(clickStar === 2){
            clickStar = 1;
        }
        
    });

    star.addEventListener('mouseout', function() {
        if(clickStar === 0){
            stars.forEach(function(s) {
                s.classList.remove('fa-solid');
                s.classList.add('fa-regular');
                s.style.color = "black";
            }); 
        }        
    });   
    
    star.addEventListener('click', function(){
        clickStar = 2;
        starSelection = this.dataset.value;
        stars.forEach(function(s) {
            s.classList.remove('fa-solid');
            s.classList.add('fa-regular');
            s.style.color = "black";
        }); 
        for (let i = 0; i <= starSelection-1; i++) {
            stars[i].classList.remove('fa-regular');
            stars[i].classList.add('fa-solid');
            stars[i].style.color = "gold";
        }
    });
});

starCont.addEventListener('mouseout', function(){
    if(clickStar === 1){
        stars.forEach(function(s) {
            s.classList.remove('fa-solid');
            s.classList.add('fa-regular');
            s.style.color = "black";
        }); 
        for (let i = 0; i <= starSelection-1; i++) {
            stars[i].classList.remove('fa-regular');
            stars[i].classList.add('fa-solid');
            stars[i].style.color = "gold";
        }
        clickStar = 2;
    }
});
    
