// appelle le lien vers l'api pour récupérer les offres
const apiUrl = 'http://dev-web-ntag.westeurope.cloudapp.azure.com/api/offers';
// appeller l'api
fetch(apiUrl)
    .then(response => response.json())
    .then(data => {
        // Remplacer les données fictives par les données de l'API
        mockOffers = data.offers.map(offer => ({
            title: offer.title,
            company: offer.company,
            description: offer.description,
            skills: offer.skills,
            salary: offer.salary,
            startDate: offer.startDate,
            endDate: offer.endDate,
            applicants: offer.applicants,
        }))
    });

// Compétences disponibles (générées à partir des offres)
const allSkills = Array.from(new Set(mockOffers.flatMap(offer => offer.skills)));

// Éléments DOM
const filterButton = document.getElementById('filterButton');
const filterPanel = document.getElementById('filterPanel');
const skillsContainer = document.getElementById('skillsContainer');
const salaryRange = document.getElementById('salaryRange');
const salaryValue = document.getElementById('salaryValue');
const applyFilters = document.getElementById('applyFilters');
const searchInput = document.getElementById('searchInput');
const resultsContainer = document.getElementById('resultsContainer');

// Variables d'état
let selectedSkills = [];
let minSalary = 1000;
let startDate = null;
let searchQuery = '';

// Initialisation
function init() {
    renderSkills();
    renderOffers(mockOffers);
    setupEventListeners();
}

// Rendre les compétences disponibles
function renderSkills() {
    skillsContainer.innerHTML = '';
    allSkills.forEach(skill => {
        const skillElement = document.createElement('div');
        skillElement.className = 'skill-tag';
        skillElement.textContent = skill;
        skillElement.addEventListener('click', () => toggleSkill(skill));
        skillsContainer.appendChild(skillElement);
    });
}

// Basculer la sélection d'une compétence
function toggleSkill(skill) {
    const index = selectedSkills.indexOf(skill);
    if (index === -1) {
        selectedSkills.push(skill);
    } else {
        selectedSkills.splice(index, 1);
    }
    updateSkillTags();
}

// Mettre à jour l'apparence des tags de compétences
function updateSkillTags() {
    document.querySelectorAll('.skill-tag').forEach(tag => {
        if (selectedSkills.includes(tag.textContent)) {
            tag.classList.add('selected');
        } else {
            tag.classList.remove('selected');
        }
    });
}

// Rendre les offres
function renderOffers(offers) {
    resultsContainer.innerHTML = '';
    
    if (offers.length === 0) {
        resultsContainer.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #777;">Aucune offre ne correspond à vos critères</p>';
        return;
    }
    
    offers.forEach(offer => {
        const offerElement = document.createElement('div');
        offerElement.className = 'offer-card';
        
        // Calculer la durée en mois
        const start = new Date(offer.startDate);
        const end = new Date(offer.endDate);
        const durationMonths = (end.getMonth() - start.getMonth()) + 
                              (end.getFullYear() - start.getFullYear()) * 12;
        
        offerElement.innerHTML = `
            <div class="offer-header">
                <h3 class="offer-title">${offer.title}</h3>
                <div class="offer-company">${offer.company}</div>
                <div class="skills-container">
                    ${offer.skills.map(skill => `
                        <span class="skill-tag ${selectedSkills.includes(skill) ? 'selected' : ''}">${skill}</span>
                    `).join('')}
                </div>
            </div>
            <div class="offer-body">
                <p class="offer-description">${offer.description}</p>
                <div class="offer-details">
                    <span class="offer-salary">${offer.salary}€/mois</span>
                    <span class="offer-duration">${durationMonths} mois</span>
                </div>
            </div>
            <div class="offer-footer">
                <span class="offer-applicants">${offer.applicants} candidats</span>
                <button class="view-button">Voir l'offre</button>
            </div>
        `;
        
        resultsContainer.appendChild(offerElement);
    });
}

// Filtrer les offres
function filterOffers() {
    return mockOffers.filter(offer => {
        // Filtre par texte
        const matchesSearch = searchQuery === '' || 
            offer.title.toLowerCase().includes(searchQuery.toLowerCase()) || 
            offer.company.toLowerCase().includes(searchQuery.toLowerCase()) || 
            offer.skills.some(skill => skill.toLowerCase().includes(searchQuery.toLowerCase()));
        
        // Filtre par compétences
        const matchesSkills = selectedSkills.length === 0 || 
            selectedSkills.every(skill => offer.skills.includes(skill));
        
        // Filtre par salaire
        const matchesSalary = offer.salary >= minSalary;
        
        // Filtre par date (si spécifié)
        const matchesDate = !startDate || new Date(offer.startDate) >= new Date(startDate);
        
        return matchesSearch && matchesSkills && matchesSalary && matchesDate;
    });
}

// Configurer les écouteurs d'événements
function setupEventListeners() {
    // Bouton filtre
    filterButton.addEventListener('click', () => {
        filterPanel.classList.toggle('active');
    });
    
    // Slider salaire
    salaryRange.addEventListener('input', () => {
        minSalary = parseInt(salaryRange.value);
        salaryValue.textContent = `${minSalary}€`;
    });
    
    // Appliquer les filtres
    applyFilters.addEventListener('click', () => {
        startDate = document.getElementById('startDate').value;
        const filteredOffers = filterOffers();
        renderOffers(filteredOffers);
        filterPanel.classList.remove('active');
    });
    
    // Recherche en temps réel
    searchInput.addEventListener('input', () => {
        searchQuery = searchInput.value.trim();
        const filteredOffers = filterOffers();
        renderOffers(filteredOffers);
    });
    
    // Clic en dehors pour fermer le panneau
    document.addEventListener('click', (e) => {
        if (!filterPanel.contains(e.target) && !filterButton.contains(e.target)) {
            filterPanel.classList.remove('active');
        }
    });
}

// Démarrer l'application
init();
