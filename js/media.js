'use strict';

// Fonctions pour la recherche audio
function clearSearch() {
    document.querySelector('.searchInput').value = '';
    document.querySelector('.search-form').submit();
}

// Validation du formulaire de création de compte
function validatePasswordMatch() {
    const mdp1 = document.getElementById('mdp1');
    const mdp2 = document.getElementById('mdp2');
    if (mdp1.value !== mdp2.value) {
        mdp2.setCustomValidity('Les mots de passe ne correspondent pas');
    } else {
        mdp2.setCustomValidity('');
    }
}

    // Gestion de l'affichage du bouton clear dans la recherche
    const searchInput = document.querySelector('.searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            document.querySelector('.clearButton').style.display = this.value ? 'block' : 'none';
            
            // Mise à jour du champ caché
            const searchHidden = document.getElementById('search-hidden');
            if (searchHidden) {
                searchHidden.value = this.value;
            }
        });
    }

// Déclaration des éléments du thème
const themeBody = document.getElementById("themeBody");
const boutonTheme = document.getElementById("boutonTheme");

document.addEventListener('DOMContentLoaded', () => {
    // renvoi les elements stockés en local au chargement de la page
    if (localStorage.className && localStorage.textContent != null) {
        themeBody.className = localStorage.className;
        boutonTheme.textContent = localStorage.textContent;
    } else {
        // affiche le theme par defaut, et le stocke dans localStorage
        themeBody.className = "clair-theme";
        localStorage.className = "clair-theme";
        boutonTheme.textContent = "dark_mode";
        localStorage.textContent = "dark_mode";
    }
});

// Changer le thème ---- Sombre-Clair -----------------------------------
// Sélectionner les éléments de changement de thème et ajouter les écouteurs d'événements
function initThemeElements() {
    const themeElements = [
        document.querySelector('.boutonTheme'),
        document.querySelector('.iconeTheme')
    ].filter(element => element !== null);

    themeElements.forEach(element => {
        if (element) {
            element.addEventListener('click', handleThemeChange);
        }
    });
}

function handleThemeChange() {
    /* map fait appel à un tableau pour les deux elements avec ecoute simultanée */
    document.body.classList.toggle('clair-theme');
    document.body.classList.toggle('sombre-theme');

    const className = document.body.className;
    /* localStorage stocke la classe et l'icone du theme actif pour persitance*/
    if (localStorage.className == "clair-theme" && localStorage.textContent == "dark_mode") {
        localStorage.className = "sombre-theme";
        localStorage.textContent = "light_mode";
    } else {
        localStorage.className = "clair-theme";
        localStorage.textContent = "dark_mode";
    }

    console.log('nom de classe: ' + className + ` et bouton : ` + localStorage.textContent);
    
    if (themeBody) {
        themeBody.className = `${localStorage.className}`;
    }
    if (boutonTheme) {
        boutonTheme.textContent = `${localStorage.textContent}`;
    }
    const iconeTheme = document.querySelector('.iconeTheme');
    if (iconeTheme) {
        iconeTheme.textContent = `${localStorage.textContent}`;
    }
}

// Initialiser les écouteurs d'événements au chargement du DOM
document.addEventListener('DOMContentLoaded', initThemeElements);


// Menu de la navbar ---------------------------------------------------
// Gestion de l'ouverture automatique du formulaire d'ajout audio
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#form-add') {
        document.querySelector('.collapsible').click();
    }
});

function myFunction() {
    var x = document.getElementById("myMenunav");
    if (x.className === "menunav") {
        x.className += " break";
    } else {
        x.className = "menunav";
    }
}

// Ouvrir la modale de connexion --------------------------------------
//const modal = document.getElementById('log');

// Fermeture de la modale ---------------------------------------------
/*window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}*/

// Collapse dans Mon Espace de gestion----------------------------------------
var collap = document.getElementsByClassName("collapsible");
if (collap && collap.length > 0) {
    Array.from(collap).forEach(function(element) {
        element.addEventListener("click", function () {
            this.classList.toggle("activeCollapse");
            var content = this.nextElementSibling;
            if (content) {
                if (content.style.maxHeight) {
                    content.style.maxHeight = null;
                } else {
                    content.style.maxHeight = content.scrollHeight + "10px";
                }
            }
        });
    });
}

// Masquage menu navigation
const menuNav = document.getElementById('myMenunav');
if (menuNav && (window.location.pathname === '/index.php' || window.location.pathname === '/ConnectForm.php')) {
    menuNav.style.display = 'none';
}


// Fonction Recherche 
const searchInputAudio = document.querySelector("input[type=search]");
if (searchInputAudio) {
    searchInputAudio.addEventListener("input", function(e) {
        const searchValue = e.target.value.toLowerCase();
        const audioItems = document.querySelectorAll('.audio-item');
        
        audioItems.forEach(item => {
            const titleElement = item.querySelector('h4');
            const artistElement = item.querySelector('p');
            
            if (titleElement && artistElement) {
                const title = titleElement.textContent.toLowerCase();
                const artist = artistElement.textContent.toLowerCase();
                
                if (title.includes(searchValue) || artist.includes(searchValue)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            }
        });
    });
}
