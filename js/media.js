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

document.addEventListener('DOMContentLoaded', () => {
    const themeBody = document.getElementById("themeBody");
    const boutonTheme = document.getElementById("boutonTheme");

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
const switcher = document.querySelector('.boutonTheme');
const switchMobile = document.querySelector('.iconeTheme');

[switcher, switchMobile].map(Element => Element.addEventListener('click', function() {
    /* map fait appel à un tableau pour les deux elements avec ecoute simultanée */
    document.body.classList.toggle('clair-theme');
    document.body.classList.toggle('sombre-theme');

    const className = document.body.className;
    const iconeTheme = document.getElementsByClassName("iconeTheme");
    /* localStorage stocke la classe et l'icone du theme actif pour persitance*/
    if (localStorage.className == "clair-theme" && localStorage.textContent == "dark_mode") {
        localStorage.className = "sombre-theme";
        localStorage.textContent = "light_mode";
    }else{
        localStorage.className = "clair-theme";
        localStorage.textContent = "dark_mode";
    }

    console.log('nom de classe: ' + className + ` et bouton : ` + localStorage.textContent);
    themeBody.className = `${localStorage.className}`;
    boutonTheme.textContent = `${localStorage.textContent}`;
    iconeTheme.textContent = `${localStorage.textContent}`;
}));


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
var i;

for (i = 0; i < collap.length; i++) {
    collap[i].addEventListener("click", function () {
        this.classList.toggle("activeCollapse");
        var content = this.nextElementSibling;
        if (content.style.maxHeight) {
            content.style.maxHeight = null;
        } else {
            content.style.maxHeight = content.scrollHeight + "10px";
        }
    });
}

// Masquage menu navigation
if (window.location.pathname === '/index.php' || window.location.pathname === '/ConnectForm.php') {
    document.getElementById('myMenunav').style.display = 'none';
}


// Fonction Recherche 
document
    .querySelectorAll("input[type=search]")[0]
    .addEventListener("input", function(e) {
        const searchValue = e.target.value.toLowerCase();
        const audioItems = document.querySelectorAll('.audio-item');
        
        audioItems.forEach(item => {
            const title = item.querySelector('h4').textContent.toLowerCase();
            const artist = item.querySelector('p').textContent.toLowerCase();
            
            if (title.includes(searchValue) || artist.includes(searchValue)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
