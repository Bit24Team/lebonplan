function formatText(command, value = null) {
  const editor = document.getElementById("offerDescription");
  editor.focus();

  if (command === "strikeThrough") {
    document.execCommand(command, false, null);

    const selection = window.getSelection();
    if (!selection.isCollapsed) {
      const range = selection.getRangeAt(0);
      const span = document.createElement("span");
      span.style.textDecoration = "line-through";
      range.surroundContents(span);
    }
  } else {
    document.execCommand(command, false, value);
  }

  document.getElementById("descriptionHtml").value = editor.innerHTML;
}

// Fonction pour insérer un lien
function insertLink() {
  const url = prompt("Entrez l'URL du lien:", "http://");
  if (url) {
    const editor = document.getElementById("offerDescription");
    const selection = window.getSelection();
    const range = selection.getRangeAt(0);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.textContent = selection.toString();

    range.deleteContents();
    range.insertNode(link);

    const newRange = document.createRange();
    newRange.selectNodeContents(link);
    selection.removeAllRanges();
    selection.addRange(newRange);

    document.getElementById("descriptionHtml").value = editor.innerHTML;
  }
}

const skillsContainer = document.getElementById("skillsContainer");
const newSkillInput = document.getElementById("newSkill");

function addSkill() {
  const skill = newSkillInput.value.trim();
  if (skill) {
    const skillTag = document.createElement("div");
    skillTag.className = "skill-tag";
    skillTag.innerHTML = `
              ${skill}
              <span class="remove-skill" onclick="this.parentElement.remove()">
                  <i class="fas fa-times"></i>
              </span>
          `;
    skillsContainer.appendChild(skillTag);
    newSkillInput.value = "";
  }
}

// Soumission du formulaire
document.getElementById("offerForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  
  // Préparation des données comme dans le code actuel
  document.getElementById("descriptionHtml").value =
    document.getElementById("offerDescription").innerHTML;

  const offerData = {
    title: document.getElementById("offerTitle").value,
    description: document.getElementById("descriptionHtml").value,
    skills: Array.from(skillsContainer.children).map((tag) =>
      tag.textContent.trim().replace("×", "").trim()
    ),
    salary: document.getElementById("salary").value,
    startDate: document.getElementById("startDate").value,
    endDate: document.getElementById("endDate").value,
  };

  // Envoi des données au serveur
  try {
    const response = await fetch('/ajouter/offre', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(offerData)
    });
    
    if (response.ok) {
      alert("Offre créée avec succès!");
      this.reset();
      skillsContainer.innerHTML = "";
      document.getElementById("offerDescription").innerHTML = "";
    } else {
      alert("Erreur lors de la création de l'offre");
    }
  } catch (error) {
    console.error('Error:', error);
    alert("Erreur réseau");
  }
});

// Permettre d'ajouter une compétence avec Entrée
newSkillInput.addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    e.preventDefault();
    addSkill();
  }
});

// Initialisation de l'éditeur
document
  .getElementById("offerDescription")
  .addEventListener("input", function () {
    // Force le retour à la ligne si le texte dépasse la largeur
    this.style.height = "auto";
    this.style.height = this.scrollHeight + "px";
  });
