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

document.addEventListener('DOMContentLoaded', () => {
    // Initialize audio player if we're on the player page
    const audioData = document.getElementById('audioData');
    if (audioData) {
        try {
            const tracks = JSON.parse(audioData.dataset.audios || '[]');
            if (!Array.isArray(tracks) || tracks.length === 0) {
                console.warn('Aucune piste audio disponible');
                return;
            }
            // Vérifier la validité des chemins audio
            tracks.forEach(track => {
                if (!track.path) {
                    console.error('Chemin audio manquant pour:', track);
                }
            });
            initializeAudioPlayer(tracks);
            console.log('Audio tracks initialized:', tracks);
        } catch (error) {
            console.error('Error initializing audio player:', error);
            // Afficher un message d'erreur à l'utilisateur
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = 'Erreur lors de l\'initialisation du lecteur audio';
            document.querySelector('.player-container').prepend(errorDiv);
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

// Fonction pour sauvegarder et jouer les pistes sélectionnées
async function saveAndPlaySelectedTracks() {
    console.log('Fonction saveAndPlaySelectedTracks appelée');
    
    const selectedCheckboxes = document.querySelectorAll('.select-audio:checked');
    console.log('Nombre de pistes sélectionnées:', selectedCheckboxes.length);
    
    if (selectedCheckboxes.length === 0) {
        showMessage('Veuillez sélectionner au moins une piste audio.', true);
        return;
    }

    const selectedTracks = Array.from(selectedCheckboxes).map(checkbox => {
        const audioItem = checkbox.closest('.audio-item');
        return checkbox.getAttribute('data-audio-id');
    });

    try {
        const csrfTokenInput = document.querySelector('input[name="csrf_token"]');
        if (!csrfTokenInput) {
            throw new Error('Token CSRF non trouvé');
        }
        const csrfToken = csrfTokenInput.value;
        console.log('Token CSRF trouvé:', csrfToken);

        console.log('Envoi de la requête avec les pistes:', selectedTracks);
        
        const response = await fetch('index.php?url=audio/saveSelection', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ 
                tracks: selectedTracks,
                csrf_token: csrfToken 
            })
        });

        console.log('Réponse reçue:', response.status);
        const data = await response.json();
        console.log('Données reçues:', data);

        if (data.success) {
            console.log('Redirection vers le lecteur');
            window.location.href = '?url=audio/player';
        } else {
            console.error('Erreur serveur:', data.message);
            showMessage(data.message || 'Erreur lors de la sauvegarde de la sélection', true);
        }
    } catch (error) {
        console.error('Erreur complète:', error);
        showMessage('Erreur lors de la sauvegarde de la sélection: ' + error.message, true);
    }
}

// Fonction pour afficher les messages
function showMessage(message, isError = true) {
    // Supprimer les messages précédents de même type
    const className = isError ? 'error-message' : 'success-message';
    const existingMessages = document.querySelectorAll('.' + className);
    existingMessages.forEach(msg => msg.remove());
    
    const div = document.createElement('div');
    div.className = className;
    div.textContent = message;
    
    const container = document.querySelector('.audio-section');
    if (container) {
        container.insertBefore(div, container.firstChild);
        
        // Animation de disparition
        setTimeout(() => {
            div.style.opacity = '0';
            setTimeout(() => div.remove(), 1000);
        }, 5000);
    }
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
