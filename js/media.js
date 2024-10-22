'use strict';

window.onload = () => {
    let themeBody = document.getElementById("themeBody");
    let boutonTheme = document.getElementById("boutonTheme");

    // renvoi les elements stockés en local au chargement de la page
    if (localStorage.className && localStorage.textContent != null){
        themeBody.className = `${localStorage.className}`;
        boutonTheme.textContent = `${localStorage.textContent}`;
    }else{ 
        // affiche le theme par defaut, et le stocke dans localStorage
        themeBody.className = `clair-theme`;
        localStorage.className = "clair-theme";
        boutonTheme.textContent = `dark_mode`;
        localStorage.textContent = "dark_mode";
    }
}

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
function myFunction() {
    var x = document.getElementById("myMenunav");
    if (x.className === "menunav") {
        x.className += " break";
    } else {
        x.className = "menunav";
    }
}

// Ouvrir la modale de connexion --------------------------------------
const modal = document.getElementById('log');

// Fermeture de la modale ---------------------------------------------
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

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
            content.style.maxHeight = content.scrollHeight + "px";
        }
    });
}

// Masquage menu navigation
if (window.location.pathname === '/index.php' || window.location.pathname === '/ConnectForm.php') {
    document.getElementById('myMenunav').style.display = 'none';
}
