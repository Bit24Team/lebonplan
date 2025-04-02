document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'affichage des noms de fichiers
    const cvInput = document.getElementById('filecv');
    const lmInput = document.getElementById('filelm');
    const cvName = document.querySelector('.cv_name');
    const lmName = document.querySelector('.lm_name');

    cvInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            cvName.textContent = this.files[0].name;
        }
    });

    lmInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            lmName.textContent = this.files[0].name;
        }
    });

    // Bouton pour remonter en haut de page
    const btnRemonter = document.querySelector('.btn_remonter');
    btnRemonter.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Affichage du bouton quand on scroll
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            btnRemonter.style.display = 'block';
        } else {
            btnRemonter.style.display = 'none';
        }
    });
});